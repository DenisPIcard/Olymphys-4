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



class EquipesFilterType extends FilterType
{ use FilterTypeTrait;
    public function __construct(RequestStack $requestStack)
                    {  
                        $this->requestStack=requestStack;
                       
                    }
    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $datas)
    { 
       
       $datas =$form->getParent()->getData(); 
       dd($datas);
       $listparam=array();
        if(!isset($datas['edition'])){
          
           $this->session->set('edition_titre',$this->session->get('edition')->getEd()); 
      }
        if(isset($datas['edition'])){
                              $listparam['edition_']=$datas['edition'];
       $this->session->set('edition_titre',$datas['edition']->getEd()); 
       }     
        
       //$queryBuilder->expr()->eq();
      
      
      if(isset($datas['edition'])){
            
         $queryBuilder->leftJoin('entity.infoequipe', 'u')
                               ->andWhere( 'u.edition =:edition')
                              ->setParameter('edition',$datas['edition']);
        
       }     
      
     
         
    }
    
     public function configureOptions(OptionsResolver $resolver)
    {    $resolver->setDefaults([
            'choice_label' => [
                'Edition' => 'infoequipe',
               
                // ...
            ],
        ]);
       
    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



