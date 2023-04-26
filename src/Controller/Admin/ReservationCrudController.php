<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

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
        return [
            FormField::addTab('Détails client'),
            IdField::new('id')->hideOnForm(),

            /* FormField::addPanel('Détails client')->setColumns(6), */
                AssociationField::new('client', 'Client'),
                AssociationField::new('apartment', 'Appartement'),
                DateTimeField::new('startAt', 'Début')->setFormat('medium')->setColumns(6),
                DateTimeField::new('endAt', 'Fin')->setFormat('medium')->setColumns(6),
            
            FormField::addTab('Détails réservation'),
            /* FormField::addPanel('Détails réservation')->setColumns(6), */
                AssociationField::new('state', 'Statut')->hideOnIndex(),
                AssociationField::new('contractFile', 'Contrat')
                    ->formatValue(function ($value) {
                        return $value ? '<i class="fa-solid fa-check d-flex justify-content-center align-items-center" style="background:#42c66a; color:white; padding:2px; width:20px; height:20px; border-radius:50%;"></i>' : '<i class="fa-solid fa-xmark d-flex justify-content-center align-items-center" style="background:#c24f19; color:white; padding:2px; width:20px; height:20px; border-radius:50%;"></i>';
                    })->onlyOnIndex(),
                BooleanField::new('cautionValidated', 'Caution')->setColumns(4),
                BooleanField::new('arrhesValidated', 'Arrhes')->setColumns(4),
                BooleanField::new('soldeValidated', 'Solde')->setColumns(4),
                MoneyField::new('price', 'Prix')
                    ->setStoredAsCents(false)
                    ->setCurrency('EUR'),
                MoneyField::new('arrhes', 'Arrhes')
                    ->setStoredAsCents(false)
                    ->setCurrency('EUR')
                    ->hideOnIndex(),
                MoneyField::new('leftToPay', 'Solde')
                    ->setCurrency('EUR')
                    ->setStoredAsCents(false)
                    ->hideOnIndex(),
                
                AssociationField::new('contractFile', 'Contrat')->hideOnIndex(),
                AssociationField::new('state', 'Statut')->onlyOnIndex(),
        ];
    }

}
