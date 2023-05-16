<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
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

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
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
                FormField::addPanel('Détails client'),
                    AssociationField::new('client', 'Client'),
                    AssociationField::new('apartment', 'Appartement'),
                    DateTimeField::new('startAt', 'Début')->setFormat('medium'),
                    DateTimeField::new('endAt', 'Fin')->setFormat('medium'),
                
                FormField::addPanel('Détails réservation'),
                    AssociationField::new('state', 'Statut'),
                    BooleanField::new('cautionValidated', 'Caution'),
                    BooleanField::new('arrhesValidated', 'Arrhes'),
                    BooleanField::new('soldeValidated', 'Solde'),
                    MoneyField::new('price', 'Prix')
                        ->setStoredAsCents(false)
                        ->setCurrency('EUR'),
                    MoneyField::new('arrhes', 'Arrhes')
                        ->setStoredAsCents(false)
                        ->setCurrency('EUR'),
                    MoneyField::new('leftToPay', 'Solde')
                        ->setCurrency('EUR')
                        ->setStoredAsCents(false),
                    TextareaField::new('pdfFile', 'Contrat PDF')
                        ->setFormType(VichFileType::class)
                        ->onlyOnForms(),
                    TextField::new('pdfName', 'Contrat PDF')->setTemplatePath("admin/fields/detail_contract_link.html.twig"),
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
                    MoneyField::new('price', 'Prix')
                        ->setStoredAsCents(false)
                        ->setCurrency('EUR'),
                    MoneyField::new('arrhes', 'Arrhes')
                        ->setStoredAsCents(false)
                        ->setCurrency('EUR'),
                    MoneyField::new('leftToPay', 'Solde')
                        ->setCurrency('EUR')
                        ->setStoredAsCents(false),
                    TextareaField::new('pdfFile', 'Contrat PDF')
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

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('admin')
        ;
    }

}
