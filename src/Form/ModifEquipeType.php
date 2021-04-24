<?php

namespace App\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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

class ModifEquipeType extends AbstractType
{  
        
    public function buildForm(FormBuilderInterface $builder, array $options)
    {    
       
      $rne= $options['rne']   ;          
      $eleves= $options['eleves']   ;
      $nbEleves=count($eleves);
      $required=[true,true,false,false,false,false];
      
      
        $builder ->add('titreProjet', TextType::class, [
                                'label' => 'Titre du projet',
                                'mapped'=>true
              ])
                
                
                
                      ->add('idProf1', EntityType::class, [
                            'class'=>'App:User',
                            'query_builder'=>function (EntityRepository $er) use($rne) {
                                                        return $er->createQueryBuilder('u')
                                                                ->andWhere('u.rne =:rne')
                                                                ->setParameter('rne',$rne)
                                                                ->addOrderBy('u.nom', 'ASC');
                                                                
                                                        ;},
                            'choice_value' => 'getId'                 ,                 
                            'choice_label' =>'getPrenomNom',
                             'mapped'=>true,
                             'required'=>true,
                             ] )
          ->add('idProf2', EntityType::class, [
                            'class'=>'App:User',
                            'required'=>false,
                            'query_builder'=>function (EntityRepository $er) use($rne) {
                                                        return $er->createQueryBuilder('u')
                                                                ->andWhere('u.rne =:rne')
                                                                ->setParameter('rne',$rne)
                                                                ->addOrderBy('u.nom', 'ASC');
                            } ,
                           'choice_value' =>'getId',
                           'choice_label' =>'getPrenomNom',
                          
                           'mapped'=>true,
                          
                             ] );
                            $i=1;
        foreach($eleves as $eleve) {        
          
         $builder->add('prenomeleve'.$i, TextType::class,[
                                            'mapped' => false,
                                            'empty_data'=>$eleves[$i-1]->getPrenom(),
                                               'data'=>$eleves[$i-1]->getPrenom(),
                                            'required'=>$required[$i-1],
                             ]) 
                        ->add('nomeleve'.$i, TextType::class,[
                                             'mapped' => false,
                                             'empty_data'=>$eleves[$i-1]->getNom(),
                                             'data'=>$eleves[$i-1]->getNom(),
                                             'required'=>$required[$i-1],
                                            ])

                        ->add('maileleve'.$i, EmailType::class,[
                                             'mapped' =>false,
                                             'empty_data'=>$eleves[$i-1]->getCourriel(),
                                              'data'=>$eleves[$i-1]->getCourriel(),
                                           'required'=>$required[$i-1],
                                            ])
                        ->add('classeeleve'.$i, ChoiceType::class,[
                                            'choices'=>['2nde'=>'2nde',
                                                 '1ère'=>'1ere',
                                                 'Term'=>'Term',
                                                 ],
                                             'mapped' =>false,
                                              'data'=>$eleves[$i-1]->getClasse(),
                                              'placeholder'=>$eleves[$i-1]->getClasse(),
                                              'required'=>$required[$i-1],
                            
                                               ])
                        ->add('genreeleve'.$i, ChoiceType::class,[
                                             'mapped' =>false,
                                             'data'=>$eleves[$i-1]->getGenre(),
                                              'placeholder'=>$eleves[$i-1]->getGenre(),
                                             'required'=>$required[$i-1],
                                            'choices'=>['F'=>'F',
                                                              'M'=>'M']])
                     ->add('id'.$i, HiddenType::class,[
                         'mapped'=>false,
                         'data'=>$eleves[$i-1]->getId()
                     ]) ;    
       
         $i++;
                   }
                   
         for($i=$nbEleves+1; $i<7;$i++) {                
                   
                   
                 $builder->add('prenomeleve'.$i, TextType::class,[
                              'mapped' =>false,
                              'required'=>$required[$i-1],
                             ]) 
                                  ->add('nomeleve'.$i, TextType::class,[
                              'mapped' => false,
                              'required'=>$required[$i-1],
                             ])
                               ->add('maileleve'.$i, EmailType::class,[
                              'mapped' => false,
                               'required'=>$required[$i-1],
                             ]) 
                               ->add('classeeleve'.$i, ChoiceType::class,[
                              'choices'=>['2de'=>'2de',
                                                 '1ère'=>'1ère',
                                                 'Term'=>'Term',
                              ],
                                             'mapped' =>false,
                                              'required'=>$required[$i-1],
                                            ])
                                ->add('genreeleve'.$i, ChoiceType::class,[
                              'mapped' =>false,
                              'required'=>$required[$i-1],
                            'choices'=>['F'=>'F',
                                               'M'=>'M']]);            
                         }
                                              
                                                            
          $builder->add('partenaire',TextType::class,[
                              'mapped' =>true,
                              'required'=>false,
                             ])          
                       ->add('contribfinance',ChoiceType::class,[
                              'mapped' =>true,
                              'required'=>true,
                           'empty_data'=>' ',
                            'choices'=>['Prof1'=>1,
                                               'Prof2'=>2,
                                               'Gestionnaire du lycée'=>3,
                                               'Autre'=>4
                              ],
                             
                             ])      
                      ->add('recompense',TextType::class,[
                              'mapped' => true,
                              'required'=>false,
                             ])
                      ->add('origineprojet',TextType::class,[
                              'mapped' =>true,
                              'required'=>true,
                             ])  
                  ->add('description',TextType::class,[
                            
                               'required'=>true,
                              'mapped' => true,
                             
                             ])  
                    ->add('save',      SubmitType::class)
                    ->add('inscrite',     CheckboxType::class,[
                             'value'=>1,
                             'required'=>false,
                             'mapped' => true,
                          
                    ]);
          
       
            
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Equipesadmin::class,'rne'=>null, 'eleves'=>null]);
       
    }
}