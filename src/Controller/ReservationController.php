<?php

namespace App\Controller;

use App\Controller\Admin\ReservationCrudController;
use App\Repository\ClientRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReservationStateRepository;
use App\Service\PdfService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
  #[Route('reservation/pdf/{id}', name: 'app_reservation_pdf_download', requirements: ['id' => '\d+'])]
  public function generatePdfContract(int $id, ReservationRepository $reservationRepository, ClientRepository $clientRepository, PdfService $pdfService)
  {
    $reservation = $reservationRepository->find($id);
    $apartment = $reservation->getApartment();
    $client = $clientRepository->find($reservation->getClient()->getId());

    // Get images
    $iban = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/iban-boursorama.jpg');
    $logo = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/logo-sejours-evasion.png');
    $logoApartment = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/' . str_replace(" ", "", $apartment) . '.jpeg');

    $dateNow = new DateTime('now');

    $data = [
      'reservation' => $reservation,
      'client' => $client,
      'iban' => $iban,
      'logo' => $logo,
      'now' => $dateNow,
      'logoApartment' => $logoApartment,
    ];

    // Get the good template
    $template = null;
    switch ($apartment) {
      case 'capbreton':
        $template = 'pdf/capbreton.html.twig';
        break;
      case 'carnac':
        $template = 'pdf/carnac.html.twig';
        break;
      case 'valmorel':
        $template = 'pdf/valmorel.html.twig';
        break;
      case 'moliets':
        $template = 'pdf/moliets.html.twig';
        break;
      
      default:
        $template = 'pdf/pdf-layout.html.twig';
        break;
    }

    $html = $this->renderView($template, $data);
    $pdfService->showPdfFile($html, $reservation->getId());
  }

  #[Route('reservation/send-contract/{id}', name: 'app_reservation_send_contract', requirements: ['id' => '\d+'])]
  public function sendContract(int $id, ReservationRepository $reservationRepository, PdfService $pdfService, MailerInterface $mailer, EntityManagerInterface $em, ConfigurationRepository $conf, ReservationStateRepository $reservationStateRepository, AdminUrlGenerator $adminUrlGenerator)
  {
    $reservation = $reservationRepository->find($id);
    $apartment = strtolower($reservation->getApartment()->getName());
    $client = $reservation->getClient()->getId();
    $clientEmail = $reservation->getClient()->getEmail();
    $emailAdmin = $conf->find('1')->getValue();
    $idStateContractSend = $conf->find('7')->getValue();
    $stateContractSend = $reservationStateRepository->find($idStateContractSend);


    // Get images
    $iban = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/iban-boursorama.jpg');
    $logo = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/logo-sejours-evasion.png');
    $logoApartment = $pdfService->imageToBase64($this->getParameter('kernel.project_dir'). '/assets/img/' . str_replace(" ", "", $apartment) . '.jpeg');

    $dateNow = new DateTime('now');

    $data = [
      'reservation' => $reservation,
      'client' => $client,
      'iban' => $iban,
      'logo' => $logo,
      'now' => $dateNow,
      'logoApartment' => $logoApartment,
    ];

    // Get the good template
    $pdfTemplate = null;
    switch ($apartment) {
      case 'capbreton':
        $pdfTemplate = 'pdf/capbreton.html.twig';
        break;
      case 'carnac':
        $pdfTemplate = 'pdf/carnac.html.twig';
        break;
      case 'valmorel':
        $pdfTemplate = 'pdf/valmorel.html.twig';
        break;
      case 'moliets':
        $pdfTemplate = 'pdf/moliets.html.twig';
        break;
      
      default:
        $pdfTemplate = 'pdf/pdf-layout.html.twig';
        break;
    }

    $html =  $this->renderView($pdfTemplate, $data);
    $pdfService->generateAndSavePdfContract($html, $id);

    // Get the pdf file
    $contractPDF = $pdfService->getPdfContract($id);

    $templateEmail = 'emails/send-contract/base.html.twig';
    $email = (new TemplatedEmail())
      ->from(new Address($emailAdmin, 'Séjour evasion'))
      ->to($clientEmail)
      ->cc(new Address($emailAdmin, 'Séjour evasion'))
      ->subject('Votre demande de séjour à ' . $reservation->getApartment()->getName())
      ->addPart(new DataPart(new File($contractPDF), 'Contrat de réservation n°' . $id, 'application/pdf'))
      ->htmlTemplate($templateEmail)
      ->context([
        'reservation' => $reservation,
      ]);

    try {
      $mailer->send($email);
      $reservation->setState($stateContractSend);
      $em->persist($reservation);
      $em->flush();

      $pdfService->removePdfContract($id);

      $redirectUrl = $adminUrlGenerator
        ->setController(ReservationCrudController::class)
        ->setAction(Crud::PAGE_DETAIL)
        ->setEntityId($reservation->getId())
        ->generateUrl();
      
      return $this->redirect($redirectUrl);

    } catch (TransportExceptionInterface $e) {
      echo $e->getMessage();
    }
  }

  #[Route('reservation/validate-payment/{id}', name: 'app_ajax_validate_payment_type', requirements: ['id' => '\d+'])]
  public function ajaxValidatePaymentType(Request $request, int $id, ReservationRepository $reservationRepository)
  {
    $reservation = $reservationRepository->find($id);

    $paymentName = $request->get('paymentName');
    $paymentSetMethod = 'set'.ucfirst($paymentName).'Validated';
    $newPaymentValue = $request->get('paymentValue');

    if ($newPaymentValue === 'false') {
      $newPaymentValue = 0;
    } else {
      $newPaymentValue = 1;
    }

    $reservation->$paymentSetMethod($newPaymentValue);

    $reservationRepository->save($reservation, true);

    $data = [$paymentSetMethod, $newPaymentValue];

    return new JsonResponse($data);
  }

  #[Route('reservation/send-instructions/{id}', name: 'app_reservation_send_instructions', requirements: ['id' => '\d+'])]
  public function sendInstructions(int $id, ReservationRepository $reservationRepository, MailerInterface $mailer, EntityManagerInterface $em, ConfigurationRepository $conf, ReservationStateRepository $reservationStateRepository, AdminUrlGenerator $adminUrlGenerator)
  {
    $reservation = $reservationRepository->find($id);
    $apartment = strtolower($reservation->getApartment()->getName());
    $clientEmail = $reservation->getClient()->getEmail();
    $emailAdmin = $conf->find('1')->getValue();
    $idStateInstructionsSend = $conf->find('5')->getValue();
    $stateInstructionsSend = $reservationStateRepository->find($idStateInstructionsSend);
    $apartment = strtolower($reservationRepository->find($reservation->getId())->getApartment()->getName());
        
    $file = 'consignes-' . $apartment . '.pdf';
    $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/instructions/' . $file;

    // Get the good template
    $template = null;
    switch ($apartment) {
      case 'capbreton':
        $template = 'emails/send-instructions/capbreton.html.twig';
        break;
      case 'carnac':
        $template = 'emails/send-instructions/carnac.html.twig';
        break;
      case 'valmorel':
        $template = 'emails/send-instructions/valmorel.html.twig';
        break;
      case 'moliets':
        $template = 'emails/send-instructions/moliets.html.twig';
        break;
      
      default:
        $template = 'emails/send-instructions/base.html.twig';
        break;
    }

    $email = (new TemplatedEmail())
      ->from(new Address($emailAdmin, 'Séjours evasion'))
      ->to($clientEmail)
      ->cc($emailAdmin)
      ->subject('Votre séjour à ' . ucfirst($reservation->getApartment()->getName()) . ' démarre bientôt')
      ->addPart(new DataPart(new File($filePath), 'Consignes du séjour'))
      ->htmlTemplate($template)
      ->context([
        'reservation' => $reservation,
      ]);

    try {
      $mailer->send($email);
      $reservation->setState($stateInstructionsSend);
      $em->persist($reservation);
      $em->flush();

      $redirectUrl = $adminUrlGenerator
        ->setController(ReservationCrudController::class)
        ->setAction(Crud::PAGE_DETAIL)
        ->setEntityId($reservation->getId())
        ->generateUrl();
      
      return $this->redirect($redirectUrl);

    } catch (TransportExceptionInterface $e) {
      echo $e->getMessage();
    }
  }
}