<?php

namespace App\Form\Type\Admin;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Edition ;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;



class CustomEditionFilterType extends AbstractType
{   private $session;
    public function __construct(SessionInterface $session)
                    {  
                        $this->session=$session;
                       
                    }

    
     public function configureOptions(OptionsResolver $resolver)

    {
        $resolver->setDefaults([
        'comparison_type_options' => ['type' => 'entity'],
        'value_type' => EntityType::class,
            'class'=>Edition::class,
            ''
    ]);

    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



