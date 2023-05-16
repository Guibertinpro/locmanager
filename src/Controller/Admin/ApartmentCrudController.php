<?php

namespace App\Controller\Admin;

use App\Entity\Apartment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ApartmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Apartment::class;
    }
}
