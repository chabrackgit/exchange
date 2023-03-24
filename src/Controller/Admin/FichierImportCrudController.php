<?php

namespace App\Controller\Admin;

use App\Entity\FichierImport;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FichierImportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FichierImport::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('customer'),
            TextField::new('uid'),
            TextField::new('imageFile')->setFormType(VichImageType::class)->onlyWhenCreating(),
            ImageField::new('file')->setBasePath('/uploads/peintures')->onlyOnIndex()->hideOnIndex()
        ];
    }
    
}
