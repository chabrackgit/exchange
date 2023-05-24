<?php

namespace App\Controller\Admin;

use App\Entity\TemplateCode;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class TemplateCodeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TemplateCode::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(CRUD::PAGE_INDEX, 'Liste des codes templates')
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
            TextField::new('description'),
            TextField::new('dossier'),
            TextField::new('cheminBackup'),
            TextField::new('cheminImport'),
            TextField::new('cheminBackupImport'),
            AssociationField::new('session')
        ];
    }

}
