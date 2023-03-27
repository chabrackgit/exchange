<?php

namespace App\Controller\Admin;

use App\Entity\TypeTransfert;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TypeTransfertCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TypeTransfert::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(CRUD::PAGE_INDEX, 'Liste des types de transfert')
            // ->setEntityLabelInPlural('Conference Comments')
            // ->setSearchFields(['author', 'text', 'email'])
            // ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code'),
            TextField::new('libelle'),
            AssociationField::new('session'),
            TextareaField::new('description')
        ];
    }
}
