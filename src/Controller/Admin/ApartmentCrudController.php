<?php

namespace App\Controller\Admin;

use App\Entity\Apartment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ApartmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Apartment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id')->hideOnForm(),
                TextField::new('color', '')->setTemplatePath("admin/fields/index_apartment_color.html.twig"),
                TextField::new('name', 'Nom'),
                TextField::new('address', 'Adresse'),
                TextField::new('complementAddress', 'Complément d\'adresse'),
                NumberField::new('postcode', 'Code postal'),
                TextField::new('city', 'Ville'),
            ];
        
        } else {
            return [
                ColorField::new('color', 'Couleur'),
                TextField::new('name', 'Nom'),
                TextField::new('address', 'Adresse'),
                TextField::new('complementAddress', 'Complément d\'adresse'),
                TextField::new('localisationDescription', 'Description de la localisation'),
                NumberField::new('postcode', 'Code postal'),
                TextField::new('city', 'Ville'),
                ChoiceField::new('type', 'Type')->setChoices([
                    'Appartement' => 'appartement',
                    'Maison' => 'maison',
                    'Box' => 'box',
                ]),
                TextField::new('capacity', 'Capacité'),
                TextField::new('surface', 'Surface'),
                ChoiceField::new('pets', 'Animaux')->setChoices([
                    'Oui' => 'oui',
                    'Non' => 'non',
                ]),
                TextField::new('numberOfRooms', 'Nombre de pièces'),
                TextField::new('numberOfBeds', 'Nombre de lits'),
                TextField::new('firstCode', 'Premier code'),
                TextField::new('secondCode', 'Second code'),
                TextField::new('thirdCode', 'Troisième code'),
                
            ];
        }
        
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Appartements')
        ;
    }
}
