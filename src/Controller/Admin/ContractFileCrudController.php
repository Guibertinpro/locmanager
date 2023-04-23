<?php

namespace App\Controller\Admin;

use App\Entity\ContractFile;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContractFileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContractFile::class;
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
