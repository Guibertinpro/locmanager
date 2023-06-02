<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PropertyAccess\PropertyPath;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('reservation')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setEntityLabelInPlural('Réservations')
            ->setPageTitle('detail',
                fn (Reservation $reservation) => sprintf('Réservation n°%s', $reservation->getId()))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        // View contract action
        $viewContract = Action::new('viewContract', 'Voir le contrat de réservation original', 'fa fa-eye')->linkToRoute('app_reservation_pdf_download', function (Reservation $reservation): array {
            return [
                'id' => $reservation->getId(),
            ];
        });

        // Send contract action
        $sendContract = Action::new('sendContract', 'Envoyer le contrat par mail', 'fa fa-paper-plane')->linkToRoute('app_reservation_send_contract', function (Reservation $reservation): array {
            return [
                'id' => $reservation->getId(),
            ];
        });

        // Send instructions action
        $sendInstructions = Action::new('sendInstructions', 'Envoyer les consignes', 'fa fa-paper-plane')->linkToRoute('app_reservation_send_instructions', function (Reservation $reservation): array {
            return [
                'id' => $reservation->getId(),
            ];
        });

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action) {
                return $action->setLabel('Créer une nouvelle réservation');
            })
            ->add(Crud::PAGE_DETAIL, $viewContract)
            ->add(Crud::PAGE_DETAIL, $sendContract)
            ->add(Crud::PAGE_DETAIL, $sendInstructions)
            ->update(Crud::PAGE_DETAIL, Action::EDIT, function(Action $action) {
                return $action->setLabel('')->setIcon('fa fa-edit');
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function(Action $action) {
                return $action->setLabel('')->setIcon('fa fa-trash');
            })
            ->remove(Crud::PAGE_DETAIL, Action::INDEX)
            ->reorder(Crud::PAGE_DETAIL, ['viewContract', 'sendContract', 'sendInstructions', Action::EDIT, Action::DELETE])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id')->hideOnForm(),
                AssociationField::new('client', 'Client')->setTemplatePath("admin/fields/index_client_name.html.twig"),
                AssociationField::new('apartment', 'Appartement')->setTemplatePath("admin/fields/index_apartment_name.html.twig"),
                DateTimeField::new('startAt', 'Début')->setFormat('medium')->setColumns(6),
                DateTimeField::new('endAt', 'Fin')->setFormat('medium')->setColumns(6),
                FormField::addTab('Détails réservation'),
                TextField::new('pdfName', 'Contrat')
                    ->formatValue(function ($value) {
                        return $value ? '<i class="fa-solid fa-check d-flex justify-content-center align-items-center" style="background:#42c66a; color:white; padding:2px; width:20px; height:20px; border-radius:50%;"></i>' : '<i class="fa-solid fa-xmark d-flex justify-content-center align-items-center" style="background:#c24f19; color:white; padding:2px; width:20px; height:20px; border-radius:50%;"></i>';
                    })->onlyOnIndex(),
                BooleanField::new('cautionValidated', 'Caution')->setColumns(4),
                BooleanField::new('arrhesValidated', 'Arrhes')->setColumns(4),
                BooleanField::new('soldeValidated', 'Solde')->setColumns(4),
                MoneyField::new('price', 'Prix')
                    ->setStoredAsCents(false)
                    ->setCurrency('EUR'),
                AssociationField::new('state', 'Statut')->setTemplatePath("admin/fields/index_reservation_state.html.twig")->onlyOnIndex(),
            ];

        } elseif(Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addPanel('Détails réservation'),
                    AssociationField::new('state', 'Statut')->setTemplatePath("admin/fields/detail_reservation_state.html.twig"),
                    DateTimeField::new('startAt', 'Début')->setFormat('medium'),
                    DateTimeField::new('endAt', 'Fin')->setFormat('medium'),
                    BooleanField::new('cautionValidated', 'Caution reçue'),
                    BooleanField::new('arrhesValidated', 'Arrhes reçues'),
                    BooleanField::new('soldeValidated', 'Solde reçu'),
                    NumberField::new('nbOfAdults', 'Nombre d\'adultes'),
                    NumberField::new('nbOfChildren', 'Nombre d\'enfants'),
                    MoneyField::new('price', 'Prix')
                        ->setStoredAsCents(false)
                        ->setCurrency('EUR'),
                    MoneyField::new('arrhes', 'Arrhes')
                        ->setStoredAsCents(false)
                        ->setCurrency('EUR'),
                    MoneyField::new('leftToPay', 'Solde')
                        ->setCurrency('EUR')
                        ->setStoredAsCents(false),
                    DateTimeField::new('dateLeftToPay', 'Date limite réception caution et solde')->setFormat('medium'),
                    TextField::new('pdfName', 'Contrat PDF')->setTemplatePath("admin/fields/detail_contract_link.html.twig"),

                FormField::addPanel('Détails client'),
                    AssociationField::new('client', 'Nom'),
                    AssociationField::new('apartment', 'Appartement'),
                    NumberField::new('nbOfAdults', 'Nombre d\'adultes'),
                    NumberField::new('nbOfChildren', 'Nombre d\'enfants'),
                    
                
            ];

        } else {
            return [
                FormField::addTab('Détails client'),
                /* FormField::addPanel('Détails client')->setColumns(6), */
                    AssociationField::new('client', 'Client'),
                    AssociationField::new('apartment', 'Appartement'),
                    DateTimeField::new('startAt', 'Début')->setFormat('medium')->setColumns(6),
                    DateTimeField::new('endAt', 'Fin')->setFormat('medium')->setColumns(6),
                
                FormField::addTab('Détails réservation'),
                /* FormField::addPanel('Détails réservation')->setColumns(6), */
                    AssociationField::new('state', 'Statut'),
                    BooleanField::new('cautionValidated', 'Caution')->setColumns(4),
                    BooleanField::new('arrhesValidated', 'Arrhes')->setColumns(4),
                    BooleanField::new('soldeValidated', 'Solde')->setColumns(4),
                    NumberField::new('nbOfAdults', 'Nombre d\'adultes'),
                    NumberField::new('nbOfChildren', 'Nombre d\'enfants'),
                    MoneyField::new('price', 'Prix')
                        ->setStoredAsCents(false)
                        ->setCurrency('EUR'),
                    MoneyField::new('arrhes', 'Arrhes')
                        ->setStoredAsCents(false)
                        ->setCurrency('EUR'),
                    MoneyField::new('leftToPay', 'Solde')
                        ->setCurrency('EUR')
                        ->setStoredAsCents(false),
                    TextareaField::new('pdfFile', 'Contrat signé')
                        ->setFormType(VichFileType::class)
                        ->setFormTypeOptions(
                            [
                                'download_label' => new PropertyPath('pdfName'),
                                'allow_delete' => true,
                                'delete_label' => 'Supprimer',
                            ]),
            ];
        }
    }

}
