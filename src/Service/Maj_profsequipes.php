<?php
namespace App\Service;

use App\Entity\Professeurs;
use App\Entity\User;
use App\Entity\Equipesadmin;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;


class Maj_profsequipes
{     private $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;

    }
    public function maj_profsequipes($equipe){

        //$em=$this->getDoctrine()->getManager();

        $repositoryProfesseurs= $this->em ->getRepository('App:Professeurs');
        $repositoryUser=$this->em->getRepository('App:User');
        $repositoryEquipesadmin= $this->em->getRepository('App:Equipesadmin');
        $prof1 = $repositoryUser->findOneBy(['id' => $equipe->getIdProf1()->getId()]);
        $profuser1= $repositoryProfesseurs->findOneBy(['user'=>$prof1]);
        if (is_null($profuser1)){
            $profuser1 = new Professeurs();
            $profuser1->setUser($prof1);
            $this->em->persist($profuser1);
            $this->em->flush();
        }
        if ($equipe->getIdProf2() != null) {
            $prof2 = $repositoryUser->findOneBy(['id' => $equipe->getIdProf2()->getId()]);
            $profuser2 = $repositoryProfesseurs->findOneBy(['user' => $prof2]);
            if (is_null($profuser2)){
                $profuser2 = new Professeurs();
                $profuser2->setUser($prof2);
                $this->em->persist($profuser2);
                $this->em->flush();
            }
        }
        $equipe =$repositoryEquipesadmin->createQueryBuilder('e')
            ->where('e.id =:id')
            ->setParameter('id',$equipe->getId())
            ->getQuery()->getSingleResult();
        $profuser1->addEquipe($equipe);
        $profuser1->setEquipesString($equipe->getEdition()->getEd().':'.$equipe->getNumero());
        $this->em->persist($profuser1);
        $this->em->flush();
        if ($equipe->getIdProf2()!=null){
            $profuser2=$repositoryProfesseurs->findOneBy(['user'=>$equipe->getIdProf2()]);
            $equipes=$profuser2->getEquipes()->getValues();
            $profuser2->addEquipe($equipe);
            $profuser2->setEquipesString($equipe->getEdition()->getEd().':'.$equipe->getNumero());
            $this->em->persist($profuser2);
            $this->em->flush();
        }
        //En cas de supression ou changement de prof1 ou 2

        $listeprofs= $repositoryProfesseurs->findAll();

        foreach($listeprofs as $prof) {
            $equipesprof = $prof->getEquipes()->getvalues();

            if (in_array($equipe, $equipesprof, true)) {
                if ($prof->getUser() != $equipe->getIdProf1()){

                    if ($prof->getUser()!= $equipe->getIdProf2()) {

                        $prof->removeEquipe($equipe);
                        $this->em->persist($prof);
                        $this->em->flush();
                    }
                }
            }
        }
        $this->em->flush();
    }

}