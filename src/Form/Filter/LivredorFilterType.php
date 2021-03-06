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
use App\Entity\Livredor;




class LivredorFilterType extends FilterType
{   public function __construct(RequestStack $requestStack)
        {
            $this->requestStack=$requestStack;            
        }
    
    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata)
    { 

        $datas =$form->getParent()->getData();

      if(null!==$datas['edition']){
            
         $queryBuilder->andWhere( 'entity.edition =:edition')
                      ->setParameter('edition',$datas['edition']);

         $this->session->set('edition_titre',$datas['edition']->getEd()); 
       }     

       return $queryBuilder;
         
    }
    
     public function configureOptions(OptionsResolver $resolver)
    {    $resolver->setDefaults([
            'choice_label' => [
                'Edition' => 'edition',


            ],
        ]);
       
    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



