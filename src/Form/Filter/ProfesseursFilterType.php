<?php

namespace App\Form\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\FilterType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\FilterTypeTrait;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;use App\Entity\Edition ;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;



class ProfesseursFilterType extends FilterType
{ use FilterTypeTrait;
    private $ession;
    public function __construct(RequestStack $requestStack)
                    {  
                        $this->requestStack=requestStack;
                       
                    }
    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata)
    { 
        
      $datas =$form->getParent()->getData();

      
      if(isset($datas['edition'])){
            
         $queryBuilder
             ->select('App:Edition')
             ->where( 'entite.edition =:edition')
             ->setParameter('edition',$datas['edition']);
        
       }     

     
         
    }
    
     public function configureOptions(OptionsResolver $resolver)
    {    $resolver->setDefaults([
            'choice_label' => [
                'Edition' => 'edition',

                // ...
            ],
        ]);
       
    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



