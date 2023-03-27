<?php

namespace App\Controller\Admin;

use App\Entity\Entite;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EntiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Entite::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(CRUD::PAGE_INDEX, 'Liste des entités')
            // ->setEntityLabelInPlural('Conference Comments')
            // ->setSearchFields(['author', 'text', 'email'])
            // ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            TextField::new('codeKyriba'),
            TextField::new('codePeopleSoft'),
            TextField::new('codeUbw'),
        ];
    }
    
}
