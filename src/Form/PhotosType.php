<?php


namespace App\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Photosinter;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;
use Vich\UploaderBundle\Form\Type\VichFileType;;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\TypeEntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PhotosType extends AbstractType
{   
    private $session;
   
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
        }
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        
       if ($options['role']!= 'ROLE_PROF'){   
         if( $options['concours']=='inter' ){
              
        $builder->add('equipe',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                       'query_builder'=>function (EntityRepository $ea) {
                                                        return $ea->createQueryBuilder('e')
                                                                ->andWhere('e.edition =:edition')
                                                                ->setParameter('edition',$this->session->get('edition'))
                                                                ->addOrderBy('e.centre', 'ASC')
                                                                 ->addOrderBy('e.numero', 'ASC')
                                                        ;},
                                        'choice_label'=>'getInfoequipe',
                                        'label' => 'Choisir une équipe',
                                         'mapped'=>false
                                         ]);
              }
          if( $options['concours']=='cn' ){    
              $builder->add('equipe',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                       'query_builder'=>function (EntityRepository $ea) {
                                                        return $ea->createQueryBuilder('e')
                                                                ->andWhere('e.edition =:edition')
                                                                ->setParameter('edition',$this->session->get('edition'))
                                                                ->andWhere('e.selectionnee = 1')
                                                               // ->setParameter('selectionnee', 'TRUE')
                                                                 ->addOrderBy('e.lettre', 'ASC');
                                                                                               },
                                        'choice_label'=>'getInfoequipenat',
                                        'label' => 'Choisir une équipe .',
                                         'mapped'=>false
                                         ]);
          }
       }  
               if ($options['role']== 'ROLE_PROF'){
                   $id=$options['id'];
          $this->session->set('idProf',$id);
          if( $options['concours']=='inter' ){
                   $builder->add('equipe',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                       'query_builder'=>function (EntityRepository $ea) {
                                                        return $ea->createQueryBuilder('e')
                                                                ->andWhere('e.idProf1 =:id')
                                                                ->orWhere('e.idProf2 =:id')
                                                                ->setParameter('id', $this->session->get('idProf'))
                                                                ->andWhere('e.edition =:edition')
                                                                ->setParameter('edition',$this->session->get('edition'))
                                                                 ->addOrderBy('e.numero', 'ASC');
                                                                 },
                                        'choice_label'=>'getInfoequipe',
                                        'label' => 'Choisir une équipe',
                                         'mapped'=>false
                                         ]);
          }
          
           if( $options['concours']=='cn' ){
                   $builder->add('equipe',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                       'query_builder'=>function (EntityRepository $ea) {
                                                        return $ea->createQueryBuilder('e')
                                                                ->andWhere('e.idProf1 =:id')
                                                                ->orWhere('e.idProf2 =:id')
                                                                ->setParameter('id', $this->session->get('idProf'))
                                                                ->andWhere('e.edition =:edition')
                                                                ->setParameter('edition',$this->session->get('edition'))
                                                                ->andWhere('e.selectionnee = TRUE')
                                                                 ->addOrderBy('e.lettre', 'ASC');
                                                      },
                                        'choice_label'=>'getInfoequipenat',
                                        'label' => 'Choisir une équipe',
                                         'mapped'=>false
                                         ]);
          }
        }
                $builder ->add('photoFiles', FileType::class, [
                                      'label' => 'Choisir les photos(format .jpeg obligatoire)',
                                        'mapped' => true,
                                       'required' => false,
                                        'multiple'=>true,
                                          ])
                                     ->add('Valider', SubmitType::class);
                                               
    
                                       }
      
     /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => null, 'concours' => '',
        'role'=>'', 'id' =>null]); 
        $resolver->setAllowedTypes('concours', 'string', 'int');
    }
}