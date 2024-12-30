<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ReservationController;
use App\Repository\ApartmentRepository;
use App\Repository\ReservationRepository;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatisticController extends AbstractController
{

    public function __construct(
      private ChartBuilderInterface $chartBuilder,
      private ApartmentRepository $apartmentRepository,
      private ReservationRepository $reservationRepository,
      private ReservationController $reservationController,
      private AdminUrlGenerator $adminUrlGenerator
    )
    {
        
    }

    /**
     * @Route("/statistiques", name="statistics")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
      // Get data
      $number_of_reservations = $this->reservationRepository->getTotalReservations();
      $total_sales = $this->reservationRepository->getTotalReservationsSales();
      $apartments = $this->apartmentRepository->findAll();
      $reservations = $this->reservationRepository->findAll();

      $newReservations = [];
      // Format reservations data
      foreach ($reservations as $reservation) {
          $redirectUrl = $this->adminUrlGenerator
              ->setController(ReservationCrudController::class)
              ->setAction(Crud::PAGE_DETAIL)
              ->setEntityId($reservation->getId())
              ->generateUrl();
  
          $newReservations[] = [
              'id' => $reservation->getId(),
              'start' => $reservation->getStartAt()->format('Y-m-d H:i'),
              'end' => $reservation->getEndAt()->format('Y-m-d H:i'),
              'title' => $reservation->getClient()->getFullName(),
              'time' => false,
              'backgroundColor' => $reservation->getApartment()->getColor(),
              'textColor' => 'white',
              'url' => $redirectUrl,
          ];
      }

      // Encode reservations data
      $reservationsData = json_encode($newReservations);
      
      // Construct chart for year sales per month
      $months = ['Janvier' => '1', 'Février' => '2', 'Mars' => '3', 'Avril' => '4', 'Mai' => '5', 'Juin' => '6', 'Juillet' => '7', 'Août' => '8', 'Septembre' => '9', 'Octobre' => '10', 'Novembre' => '11', 'Décembre' => '12'];
      $now = new DateTime('now');
      $year = $now->format('Y');
      $datasets = [];
      $i = 0;
      foreach ($apartments as $apartment) {
          $monthData = [];
          foreach ($months as $k => $v) {
          $monthData[] = $this->reservationRepository->getTotalSalesByYearAndMonth($year, $v, $apartment);
          }
          $datasets[] = [
          'stack' => 'Stack '.$i,
          'label' => $apartment->getName(),
          'backgroundColor' => $apartment->getColor(),
          'data' => $monthData,
          ];
          $i = $i+1;
      }

      // Create chart for current year statistics
      $currentYearChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
      $currentYearChart->setData([
          'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
          'datasets' => $datasets,
      ]);

      $currentYearChart->setOptions([
          'scales' => [
              'y' => [
                  'suggestedMin' => 0,
                  'suggestedMax' => 100,
              ],
          ],
          'responsive' => true,
          'maintainAspectRatio' => false,
      ]);

      // On veut un graphique par année regroupant le CA par appartement
      // labels dynamique de année de départ 2022 jusqu'à année actuelle
      // somme des ventes par appartement et par année

      // Construct chart for year sales per apartment
      $years = [];
      $beginYear = 2022;
      $now = new DateTime('now');
      $actualYear = $now->format('Y');
      $nextYearNumber = intval($actualYear) + 1;
      for ($i = $nextYearNumber; $i >= $beginYear; $i--) {
          $years[] = strval($i);
      }
      
      $datasets = [];
      $i = 0;
      foreach ($apartments as $apartment) {
          $yearData = [];
          foreach ($years as $k => $v) {
          $yearData[] = $this->reservationRepository->getTotalSalesByYearAndApartment($v, $apartment);
          }
          $datasets[] = [
          'stack' => 'Stack '.$i,
          'label' => $apartment->getName(),
          'backgroundColor' => $apartment->getColor(),
          'data' => $yearData,
          ];
          $i = $i+1;
      }

      // Create chart for sales per apartment per year
      $chartSalesPerYearAndApartment = $this->chartBuilder->createChart(Chart::TYPE_BAR);
      $chartSalesPerYearAndApartment->setData([
          'labels' => $years,
          'datasets' => $datasets,
      ]);

      $chartSalesPerYearAndApartment->setOptions([
          'indexAxis' => 'y',
          'scales' => [
              'x' => [
                  'suggestedMin' => 0,
                  'suggestedMax' => 100,
              ],
          ],
          'responsive' => true,
          'maintainAspectRatio' => true,
          'plugins' => [
              'datalabels' => [
                  'anchor'=> 'end',
                  'align'=> 'top',
                  'font'=> [
                      'weight'=> 'bold',
                      'size'=> 16
                  ]
              ],
          ],
      ]);

      return $this->render('admin/statistiques.html.twig', [
          'nbReservations' => $number_of_reservations,
          'totalSales' => $total_sales,
          'reservations' => $reservationsData,
          'apartments' => $apartments,
          'currentYearChart' => $currentYearChart,
          'chartSalesPerYearAndApartment' => $chartSalesPerYearAndApartment
      ]);
    }
}