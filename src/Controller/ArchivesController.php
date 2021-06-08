<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Fichiersequipes;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;
use App\Entity\Edition ;
use App\Entity\Photos ;
use App\Entity\Livredor ;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

class ArchivesController extends AbstractController
{   private $session;
    public function __construct(SessionInterface $session){

        $this->session=$session;


    }

    /**
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @Route("/archives/liste_fichiers_photos", name="archives_fichiers_photos")
     *
     */
    public function liste_fichiers_photos(Request $request)
    {
   $idedition=$request->query->get('sel');
        $repositoryEdition = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Edition');
        $repositoryFichiersequipes = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Fichiersequipes');
        $repositoryEquipesadmin = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipesadmin');
        $repositoryPhotos = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Photos');
        $repositoryLivresdor = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Livredor');
        if ($idedition==null) {
            if (new \datetime('now') > $this->session->get('edition')->getConcourscia()) {
                $edition = $this->session->get('edition');
             }
            else{

                $edition = $repositoryEdition->findOneBy(['ed'=>$this->session->get('edition')->getEd()-1]) ;

            }
        }
        else{
            $edition=$repositoryEdition->findOneBy(['id'=>$idedition]);
        }

        $fichiersEquipes=$repositoryFichiersequipes->createQueryBuilder('f')
                                                   ->where('f.edition =:edition')
                                                   ->andWhere('f.typefichier <4')
                                                   ->setParameter('edition',$edition)
                                                   ->getQuery()->getResult();
        $equipes = $repositoryEquipesadmin->createQueryBuilder('f')
                                          ->where('f.edition =:edition')
                                          ->andWhere('f.rneId IS NOT NULL')
                                          ->setParameter('edition',$edition)
                                          ->addOrderBy('f.lettre','ASC')
                                          ->addOrderBy('f.numero','ASC')
                                          ->getQuery()->getResult();

        $editions=$repositoryEdition->createQueryBuilder('e')
                                    ->select('e')
                                    ->where('e.concourscia !=:datelim')
                                    ->setParameter('datelim',$this->session->get('edition')->getConcourscia())
                                    ->orderBy('e.ed','DESC')
                                    ->getQuery()->getResult();


        $i=0;
        foreach($equipes as $equipe){

          $qb1= $repositoryPhotos->createQueryBuilder('p')

                                   ->where('p.edition =:edition')
                                   ->setParameter('edition',$edition)
                                   ->andWhere('p.equipe =:equipe')
                                   ->setParameter('equipe',$equipe);

            if ($qb1->getQuery()->getResult()!=null){
                $photos= $qb1->getQuery()->getResult();
                shuffle($photos);
                $photoseqcia[$i]=$photos[array_rand([0..(count($photos)-1)])];
            }

              if ($equipe->getSelectionnee()== true) {

               $qb2=$repositoryPhotos->createQueryBuilder('p')

                   ->where('p.edition =:edition')
                   ->setParameter('edition',$edition)
                   ->andWhere('p.equipe =:equipe')
                   ->setParameter('equipe',$equipe)
                   ->andWhere('p.national = 1');

               $photos= $qb2->getQuery()->getResult();
               if ($photos!=null){
               shuffle($photos);
               $photoseqcn[$i]=$photos[array_rand([0..(count($photos)-1)])];}
           }
                 $i++;
        }

        $livresdor=$repositoryLivresdor->findBy(['edition'=>$edition]);

        return  $this->render('archives\archives.html.twig',
                            array('fichiersequipes' => $fichiersEquipes,'editions' => $editions,'photoseqcn' =>$photoseqcn,'photoseqcia' =>$photoseqcia,'equipes'=>$equipes, 'livresdor'=>$livresdor,'edition_affichee'=>$edition));

    }
}