<?php

namespace App\Form\Filter;

use Doctrine\ORM\QueryBuilder;

//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Edition ;
use App\Entity\Equipesadmin;
use App\Entity\Elevesinter;



class ElevesinterFilterType extends AbstractType
{


        public function configureOptions(OptionsResolver $resolver)
    {    $resolver->setDefaults([
            'class'=>Equipesadmin::class,

        ]);
       
    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



