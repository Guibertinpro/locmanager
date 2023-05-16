<?php

namespace App\Controller\Admin;

use App\Entity\ReservationState;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReservationStateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReservationState::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
