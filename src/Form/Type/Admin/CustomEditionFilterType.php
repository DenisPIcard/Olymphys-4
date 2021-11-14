<?php

namespace App\Form\Type\Admin;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\HttpFoundation\RequestStack;use App\Entity\Edition ;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;



class CustomEditionFilterType extends AbstractType
{   private $requestStack;
    public function __construct(RequestStack $requestStack)
                    {  
                        $this->requestStack=$requestStack;
                       
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



