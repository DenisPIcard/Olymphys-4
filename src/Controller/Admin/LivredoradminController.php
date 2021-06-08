<?php
namespace App\Controller\Admin;
use App\Entity\Livredor;
use Doctrine\ORM\EntityRepository;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use App\Entity\Equipesadmin;

use App\Entity\Elevesinter;
use App\Entity\Edition;

use App\Entity\User;
use App\Form\Filter\LivredorFilterType;


use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use ZipArchive;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\String\UnicodeString;

class LivredoradminController extends EasyAdminController
{   private $edition;
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
            
        }
    
    
    
    
    protected function createFiltersForm(string $entityName): FormInterface
    {  

        $form = parent::createFiltersForm($entityName);
       
        $form->add('edition', LivredorFilterType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                    ->addOrderBy('u.ed', 'DESC');
                                     },
           'choice_label' => 'getEd',
            'multiple'=>false,]);
        return $form;
    
    }

   
    public  function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null){
          
       
        $edition= $this->session->get('edition');
        $this->session->set('edition_titre',$edition->getEd());
        $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder('l')
            ->select('entity')
            ->from($this->entity['class'], 'entity')
            ->andWhere('entity.edition =:edition')
            ->setParameter('edition',$edition);
           

        if (!empty($dqlFilter)) {
           
              if ($dqlFilter=='entity.categorie = eleve'){
              $queryBuilder->andWhere('entity.categorie =:categorie')
                      ->setParameter('categorie','equipe')
                      ->leftJoin('entity.equipe','eq')
                      ->addOrderBy('eq.lettre', 'ASC');;
              }
            if ($dqlFilter=='entity.categorie = profs'){
                $queryBuilder->andWhere('entity.categorie =:categorie')
                    ->setParameter('categorie','prof')
                    ->leftJoin('entity.user','u')
                    ->addOrderBy('u.nom', 'ASC');;
            }
            if ($dqlFilter=='entity.categorie = jury'){
                $queryBuilder->andWhere('entity.categorie =:categorie')
                    ->setParameter('categorie','jury')
                    ->leftJoin('entity.user','u')
                    ->addOrderBy('u.nom', 'ASC');;
            }
            if ($dqlFilter=='entity.categorie = comite'){
                $queryBuilder->andWhere('entity.categorie =:categorie')
                    ->setParameter('categorie','comite')
                    ->leftJoin('entity.user','u')
                    ->addOrderBy('u.nom', 'ASC');;
            }
        } 
            return $queryBuilder;
         
      }


         
          
          
}

