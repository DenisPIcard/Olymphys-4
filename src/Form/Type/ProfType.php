<?php
namespace App\Form\Type;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\Equipesadmin;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\TypeEntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $rne = $options['rne'];


        $builder->add('idProf1', EntityType::class, [
            'class' => 'App:User',
            'query_builder' => function (EntityRepository $er) use ($rne) {
                return $er->createQueryBuilder('u')
                    ->andWhere('u.rne =:rne')
                    ->setParameter('rne', $rne)
                    ->addOrderBy('u.nom', 'ASC');;
            },
            'choice_value' => 'getId',
            'choice_label' => 'getPrenomNom',
            'mapped' => true,
            'required' => true,
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Equipesadmin::class,'rne'=>null]);

    }
}


