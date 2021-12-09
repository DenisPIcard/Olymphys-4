<?php
namespace App\Service;

use App\Entity\OdpfArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class OdpfListeEquipes
{
    private EntityManagerInterface $em;
    private SessionInterface $session;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
    }
    public function getArray($choix): array
    {
        $edition = $this->session->get('edition');
        $repo=$this->em->getRepository(OdpfArticle::class);
        $article=$repo->findOneBy(['choix'=>$choix]);
        $idcategorie=$article->getIdCategorie();
        $titre=$article->getTitre();
        $repositoryEquipesadmin=$this->em->getRepository('App:Equipesadmin');
        $repositoryUser=$this->em->getRepository('App:User');
        $repositoryRne=$this->em->getRepository('App:Rne');
        $listEquipes=$repositoryEquipesadmin ->createQueryBuilder('e')
            ->select('e')
            ->andWhere('e.edition =:edition')
            ->setParameter('edition',$edition)
            ->orderBy('e.numero','ASC')
            ->getQuery()
            ->getResult();
        foreach ($listEquipes as $equipe)
        {
            $numero = $equipe->getNumero();
            $rne = $equipe->getRne();
            $lycee[$numero]= $repositoryRne->findByRne($rne);
            $idprof1 = $equipe->getIdProf1();
            $prof1[$numero] = $repositoryUser->findById($idprof1);
            $idprof2 = $equipe->getIdProf2();
            $prof2[$numero]= $repositoryUser->findById($idprof2);
        }
        return ['listEquipes'=> $listEquipes,
            'prof1'=>$prof1,
            'prof2'=>$prof2,
            'lycee'=>$lycee,
            'choix'=>$choix,
            'edition' =>$edition,
            'titre' => $titre,
            'idcategorie' =>$idcategorie
        ];
    }
}
