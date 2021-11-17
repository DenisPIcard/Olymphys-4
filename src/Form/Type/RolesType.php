<?php
namespace App\Form\Type;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class RolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {

        $builder->add('roles', RangeType::class, [
            'attr' => ['ROLES_ADMIN'=>'ROLES_ADMIN',
                'ROLE_PROF'=>'ROLE_PROF',
                'ROLE_JURY'=>'ROLE_JURY',
                'ROLE_ORGACIA'=>'ROLE_ORGACIA',
                'ROLE_COMITE'=>'ROLE_COMITE'],

            'required' => true,
        ]);
    }

}
