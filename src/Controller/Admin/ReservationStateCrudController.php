<?php

namespace App\Controller\Admin;

use App\Entity\ReservationState;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReservationStateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReservationState::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('États de réservation')
            ->setPageTitle('detail',
                fn (ReservationState $reservationState) => sprintf('Etat de réservation n°%s', $reservationState->getId()))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action) {
                return $action->setLabel('Créer une nouvelle relation d\'état');
            })
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id', 'Id'),
                TextField::new('name', 'Dénomination'),
                TextField::new('color', 'Couleur')->setTemplatePath("admin/fields/index_reservation_state_color.html.twig"),
            ];
        } else {
            return [
                TextField::new('name', 'Dénomination'),
                ColorField::new('color', 'Couleur'),
            ];
        }
    }
}
