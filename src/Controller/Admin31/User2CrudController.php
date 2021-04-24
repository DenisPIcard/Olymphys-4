<?php

namespace App\Controller\Admin31;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class User2CrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'username', 'roles', 'email', 'token', 'rne', 'nom', 'prenom', 'adresse', 'ville', 'code', 'phone', 'civilite']);
    }

    public function configureFields(string $pageName): iterable
    {
        $username = TextField::new('username');
        $email = TextField::new('email');
        $roles = ArrayField::new('roles');
        $password = TextField::new('password');
        $isActive = BooleanField::new('is_active');
        $id = IntegerField::new('id', 'ID');
        $isActive = Field::new('isActive');
        $token = TextField::new('token');
        $passwordRequestedAt = DateTimeField::new('passwordRequestedAt');
        $rne = TextField::new('rne');
        $nom = TextField::new('nom');
        $prenom = TextField::new('prenom');
        $adresse = TextField::new('adresse');
        $ville = TextField::new('ville');
        $code = TextField::new('code');
        $phone = TextField::new('phone');
        $createdAt = DateTimeField::new('createdAt');
        $updatedAt = DateTimeField::new('updatedAt');
        $lastVisit = DateTimeField::new('lastVisit');
        $civilite = TextField::new('civilite');
        $centrecia = AssociationField::new('centrecia');
        $autorisationphotos = AssociationField::new('autorisationphotos');
        $interlocuteur = AssociationField::new('interlocuteur');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$username, $nom, $prenom, $email, $roles, $isActive];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $username, $roles, $password, $email, $isActive, $token, $passwordRequestedAt, $rne, $nom, $prenom, $adresse, $ville, $code, $phone, $createdAt, $updatedAt, $lastVisit, $civilite, $centrecia, $autorisationphotos, $interlocuteur];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$username, $email, $roles, $password];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$username, $email, $roles, $isActive];
        }
    }
}
