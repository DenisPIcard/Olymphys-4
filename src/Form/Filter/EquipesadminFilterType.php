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



class EquipesadminFilterType extends FilterType
{ use FilterTypeTrait;
    public function __construct(RequestStack $requestStack)
                    {  
                        $this->requestStack=requestStack;
                       
                    }
    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata)
    { 
        
       $datas =$form->getParent()->getData();
       $listparam=array();
        if(!isset($datas['edition'])){
          
           $this->session->set('edition_titre',$this->session->get('edition')->getEd()); 
      }
        if(isset($datas['edition'])){
                              $listparam['edition_']=$datas['edition'];
       $this->session->set('edition_titre',$datas['edition']->getEd()); 
       }     
         if(isset($datas['centre'])){
                             $centres = $datas['centre'];
                              $n=0;
                            foreach($centres as $centre){
                                $listparam['centre'.$n]=$centre;
                                 $n++;} 
         unset($centre);  
         ; 
         }
       //$queryBuilder->expr()->eq();
      
      
      if(isset($datas['edition'])){
            
         $queryBuilder->Where( 'entity.edition =:edition')
                              ->setParameter('edition',$datas['edition']);
        
       }     
       if(isset($datas['centre'])){
                    
           $queryBuilder->andWhere( 'entity.centre =:centre')
                              ->setParameter('centre',$datas['centre'])
                              ->addOrderBy('entity.numero','ASC');
          
       }
     
         
    }
    
     public function configureOptions(OptionsResolver $resolver)
    {    $resolver->setDefaults([
            'choice_label' => [
                'Edition' => 'edition',
                'Centre' => 'centre',
                // ...
            ],
        ]);
       
    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



