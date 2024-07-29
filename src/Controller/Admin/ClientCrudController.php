<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action) {
                return $action->setLabel('Créer un nouveau client');
            })
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id'),
                TextField::new('firstname', 'Prénom'),
                TextField::new('lastname', 'Nom'),
                TextField::new('email', 'Email'),
                TextField::new('phone', 'Téléphone'),
                DateField::new('dateCreate','Date de création')
            ];
        
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addPanel('Données client'),
                    TextField::new('firstname', 'Prénom'),
                    TextField::new('lastname', 'Nom'),
                    TextField::new('email', 'Email'),
                    TextField::new('phone', 'Téléphone'),
                    TextField::new('phoneBis', 'Téléphone 2'),
                    TextField::new('address', 'Adresse'),
                    TextField::new('complementAddress', 'Complément d\'adresse'),
                    TextField::new('postcode', 'Code postal'),
                    TextField::new('city', 'Ville'),
                FormField::addPanel('Réservations'),
                    AssociationField::new('reservations', '')->setTemplatePath("admin/fields/detail_client_reservations.html.twig")
            ];
        } else {
            return [
                ChoiceField::new('civility', 'Civilité')->setChoices([
                    'M' => 'M',
                    'Mme' => 'Mme',
                ]),
                TextField::new('firstname', 'Prénom'),
                TextField::new('lastname', 'Nom'),
                TextField::new('email', 'Email'),
                TextField::new('phone', 'Téléphone'),
                TextField::new('phoneBis', 'Téléphone 2'),
                TextField::new('address', 'Adresse'),
                TextField::new('complementAddress', 'Complément d\'adresse'),
                TextField::new('postcode', 'Code postal'),
                TextField::new('city', 'Ville'),
            ];
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Clients')
        ;
    }
}
