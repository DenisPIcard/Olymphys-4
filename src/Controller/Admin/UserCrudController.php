<?php

namespace App\Controller\Admin;

use App\Entity\User;


use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class UserCrudController extends AbstractCrudController
{
    private $adminContextProvider;
    public function __construct(AdminContextProvider $adminContextProvider)
    {
    $this->adminContextProvider=$adminContextProvider;
    }

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
        $id = IntegerField::new('id', 'ID');
        $email = TextField::new('email');
        $username = TextField::new('username');
        $nomPrenom = TextareaField::new('nomPrenom');
        $roles = ArrayField::new('roles');
        $rolesedit = ChoiceField::new('roles')->setChoices(['ROLES_ADMIN'=>'ROLES_ADMIN',
            'ROLE_SUPER_ADMIN'=>'ROLE_SUPER_ADMIN',
            'ROLE_ADMIN'=>'ROLE_ADMIN',
            'ROLE_PROF'=>'ROLE_PROF',
            'ROLE_JURY'=>'ROLE_JURY',
            'ROLE_ORGACIA'=>'ROLE_ORGACIA',
            'ROLE_COMITE'=>'ROLE_COMITE'])
            ->setFormTypeOption('multiple',true);
        $password = Field::new('password')->setFormType(PasswordType::class);
            if ($pageName=='edit')
            {    $iD=$_REQUEST['entityId'];
                $user=$this->getDoctrine()->getRepository(User::class)->findOneBy(['id'=>$iD]);
                $password->setFormTypeOptions(['required'=>false,'mapped'=>true,'empty_data'=>$user->getPassword()]);
            }
        $isActive = BooleanField::new('is_active');
        $nom = TextField::new('nom');
        $prenom = TextField::new('prenom');
        $rne = TextField::new('rne');
        $centrecia = AssociationField::new('centrecia');
        $rneId=AssociationField::new('rneId','UAI');


        $isActive = Field::new('isActive');
        $token = TextField::new('token');
        $passwordRequestedAt = DateTimeField::new('passwordRequestedAt');
        $adresse = TextField::new('adresse');
        $ville = TextField::new('ville');
        $code = TextField::new('code');
        $phone = TextField::new('phone');
        $createdAt = DateTimeField::new('createdAt');
        $updatedAt = DateTimeField::new('updatedAt');
        $lastVisit = DateTimeField::new('lastVisit');
        $civilite = TextField::new('civilite');
        $autorisationphotos = AssociationField::new('autorisationphotos');
        $interlocuteur = AssociationField::new('interlocuteur');
        $centreciaCentre = TextareaField::new('centrecia.centre', 'Centre CIA');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id,$email,$username,  $nomPrenom, $roles, $isActive, $centreciaCentre];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $username, $roles, $password, $email, $isActive, $token, $passwordRequestedAt, $rneId, $nom, $prenom, $adresse, $ville, $code, $phone, $createdAt, $updatedAt, $lastVisit, $civilite, $centrecia, $autorisationphotos, $interlocuteur];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$username,$email, $rolesedit, $password, $isActive, $nom, $prenom, $rneId, $centrecia,$adresse,$ville,$code,$phone];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$username,$email, $password, $rolesedit, $isActive, $centrecia, $nom, $prenom, $rneId, $centrecia,$adresse,$ville,$code,$phone ];
        }
    }
    public function configureActions(Actions $actions): Actions
    {
        $addUsers = Action::new('addUsers', 'Ajouter des users','fa fa_users', )
            // if the route needs parameters, you can define them:
            // 1) using an array
            ->linkToRoute('secretariatadmin_charge_user')
            ->createAsGlobalAction();


        $actions = $actions
        ->add(Crud::PAGE_EDIT, Action::INDEX, 'Retour à la liste')
        ->add(Crud::PAGE_NEW, Action::INDEX, 'Retour à la liste')
        ->add(Crud::PAGE_INDEX, Action::DETAIL )
        ->add(Crud::PAGE_INDEX, $addUsers);
        return $actions;
    }

}
