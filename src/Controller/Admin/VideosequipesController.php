<?php
namespace App\Controller\Admin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use App\Entity\Equipesadmin;
use App\Entity\Edition;
use App\Entity\Centrescia;

use App\Form\Filter\VideosequipesFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class VideosequipesController extends EasyAdminController
{   public function __construct(SessionInterface $session)
        {
            $this->session = $session;
            
        }
    protected function createFiltersForm(string $entityName): FormInterface
    { 
        $form = parent::createFiltersForm($entityName);
        
        $form->add('edition', VideosequipesFilterType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                    ->orderBy('u.ed', 'DESC');
                                     },
           'choice_label' => 'getEd',
            'multiple'=>false,]);
            $form->add('centre', VideosequipesFilterType::class, [
                         'class' => Centrescia::class,
                         'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                 ->orderBy('u.centre','ASC');
                                                      
                                                  },
                        'choice_label' => 'getCentre',
                         'multiple'=>false,]);
           $form->add('equipe', VideosequipesFilterType::class, [
                               'class' => Equipesadmin::class,
                               'query_builder' => function (EntityRepository $er) {
                                         return $er->createQueryBuilder('u')
                                                 ->andWhere('u.numero <:valeur')
                                                 ->setParameter('valeur',100 )
                                                 ->addOrderBy('u.numero','ASC')
                                                 ->addOrderBy('u.edition', 'DESC');
                                                      
                                                  },
                           'choice_label'=>'getInfoEquipe',
                         'multiple'=>true,]);
        return $form;
    }
    public function persistEntity($entity)
    {
        
        /*$repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
                  $entity->setEdition($edition);*/
        
         parent::persistEntity($entity);
        
    }
    public  function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null){
          
        
        $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
            $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder('l')
            ->select('entity')
            ->from($this->entity['class'], 'entity')
            ->join('entity.equipe','eq')
           ->addOrderBy('eq.centre', 'ASC')
           ->addOrderBy('eq.numero', 'ASC')
           ;
           
          if (!empty($dqlFilter)) {
              $queryBuilder->andWhere($dqlFilter);
                                          
              }
           
            return $queryBuilder;
         
      }
    
    
    
}

