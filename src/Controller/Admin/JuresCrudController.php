<?php

namespace App\Controller\Admin;

use App\Entity\Jures;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JuresCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Jures::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'prenomJure', 'nomJure', 'initialesJure', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w'])
            ->setPaginatorPageSize(30);
    }

    public function configureFields(string $pageName): iterable
    {
        if (($pageName == 'edit') or ($pageName == 'new')) {

            $qb =$this->getDoctrine()->getManager()->getRepository(user::class)->createQueryBuilder('j');
            $role='ROLE_JURY';
            $users=$qb->addOrderBy('j.nom','ASC')->getQuery()->getResult();
            $i=0;

            foreach($users as $user){
                $roles=$user->getRoles();
                if (in_array('ROLE_JURY',$roles)){
                    $listejures[$i]=$user;
                }
               $i++;
            }

            /*$qb->andWhere('j.roles LIKE :role')
                ->setParameter('role',  'a:2:{i:0;s:9:"ROLE_JURY";i:1;s:9:"ROLE_USER";}')
                ->addOrderBy('j.nom','ASC')
                ->getQuery()
                ->getResult();
            andWhere($qb->expr()->eq('j.roles', ':roles1'))
                ->setParameter('roles1', 'a:2:{i:0;s:9:"ROLE_JURY";i:1;s:9:"ROLE_USER";}')
                ->orWhere($qb->expr()->eq('j.roles', ':roles2'))
                ->setParameter('roles2', 'a:3:{i:0;s:11:"ROLE_COMITE";i:1;s:9:"ROLE_JURY";i:2;s:9:"ROLE_USER";}')
                ->addOrderBy('j.nom','ASC');
           $listejures =$qb->getQuery()->getResult();*/
        } else {
            $listejures = [];

        }

        $nomJure = TextField::new('nomJure');
        $prenomJure = TextField::new('prenomJure');
        $iduser = AssociationField::new('iduser')->setFormTypeOptions(['choices'=>$listejures]);;
        $a = TextareaField::new('A');
        $b = TextareaField::new('B');
        $c = TextareaField::new('C');
        $d = TextareaField::new('D');
        $e = TextareaField::new('E');
        $f = TextareaField::new('F');
        $g = TextareaField::new('G');
        $h = TextareaField::new('H');
        $i = TextareaField::new('I');
        $j = TextareaField::new('J');
        $k = TextareaField::new('K');
        $l = TextareaField::new('L');
        $m = TextareaField::new('M');
        $n = TextareaField::new('N');
        $o = TextareaField::new('O');
        $p = TextareaField::new('P');
        $q = TextareaField::new('Q');
        $r = TextareaField::new('R');
        $s = TextareaField::new('S');
        $t = TextareaField::new('T');
        $u = TextareaField::new('U');
        $v = TextareaField::new('V');
        $w = TextareaField::new('W');
        $x = TextareaField::new('X');
        $y = TextareaField::new('Y');
        $id = IntegerField::new('id', 'ID');
        $initialesJure = TextField::new('initialesJure');
        $a = IntegerField::new('a');
        $b = IntegerField::new('b');
        $c = IntegerField::new('c');
        $d = IntegerField::new('d');
        $e = IntegerField::new('e');
        $f = IntegerField::new('f');
        $g = IntegerField::new('g');
        $h = IntegerField::new('h');
        $i = IntegerField::new('i');
        $j = IntegerField::new('j');
        $k = IntegerField::new('k');
        $l = IntegerField::new('l');
        $m = IntegerField::new('m');
        $n = IntegerField::new('n');
        $o = IntegerField::new('o');
        $p = IntegerField::new('p');
        $q = IntegerField::new('q');
        $r = IntegerField::new('r');
        $s = IntegerField::new('s');
        $t = IntegerField::new('t');
        $u = IntegerField::new('u');
        $v = IntegerField::new('v');
        $w = IntegerField::new('w');
        $notesj = AssociationField::new('notesj');
        $nom = Field::new('nom');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$nom, $initialesJure, $iduser, $a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m, $n, $o, $p, $q, $r, $s, $t, $u, $v, $w];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $prenomJure, $nomJure, $iduser, $initialesJure, $a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m, $n, $o, $p, $q, $r, $s, $t, $u, $v, $w, $notesj];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$nomJure, $prenomJure, $iduser, $a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m, $n, $o, $p, $q, $r, $s, $t, $u, $v, $w];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$nomJure, $prenomJure, $iduser, $a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m, $n, $o, $p, $q, $r, $s, $t, $u, $v, $w];
        }
    }
}
