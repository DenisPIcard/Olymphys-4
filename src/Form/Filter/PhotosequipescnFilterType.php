<?php

namespace App\Form\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\FilterType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\FilterTypeTrait;
use Symfony\Component\HttpFoundation\RequestStack;use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Edition ;
use App\Entity\Equipesadmin;
use App\Entity\Elevesinter;



class PhotosequipescnFilterType extends FilterType
{    public function __construct(RequestStack $requestStack)
                    {  
                        $this->requestStack=requestStack;
                       
                    }
    
    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata)
    { 
      
        
       $datas =$form->getParent()->getData();
      
      if(null!=$datas['edition']){
            
         $queryBuilder->andWhere( 'entity.edition =:edition')
                              ->setParameter('edition',$datas['edition']);
         $this->session->set('edition_titre',$datas['edition']->getEd()); 
       }     
                       
                  
       if(null!=$datas['equipe']){
            
            
            $queryBuilder->setParameter('edition',$datas['equipe']->getEdition())    
                                  ->andWhere( 'entity.equipe =:equipe')
                               ->setParameter('equipe',$datas['equipe'])
                                ->addOrderBy('eq.lettre','ASC');               
            $this->session->set('edition_titre',$datas['equipe']->getEdition()->getEd());   
       }
       
       
       
       
       
       return $queryBuilder;
         
    }
    
     public function configureOptions(OptionsResolver $resolver)
    {    $resolver->setDefaults([
            'choice_label' => [
                'Edition' => 'edition',
                'Equipe'=> 'equipe'
                // ...
            ],
        ]);
       
    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



