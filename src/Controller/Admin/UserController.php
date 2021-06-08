<?php
namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;


class UserController extends EasyAdminController
{
    
    public function deleteAction()
    {
        $class = $this->entity['class'];
        $id = $this->request->query->get('id');
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository($class);
        $user = $repository->find(['id' => $id]);
        $repositoryProf = $this->getDoctrine()->getRepository('App:Professeurs');
        $repositoryEquipes = $this->getDoctrine()->getRepository('App:Equipesadmin');
        $repositoryEleves = $this->getDoctrine()->getRepository('App:Elevesinter');
        $equipes = $repositoryEquipes->createQueryBuilder('e')
            ->where('e.idProf1 =:user or e.idProf2 =:user')
            ->setParameter('user', $user)
            ->getQuery()->getResult();

        $prof=$repositoryProf->createQueryBuilder('p')
            ->where('p.user =:user')
            ->setParameter('user',$user)
            ->getQuery()->getOneOrNullResult();
        if ($prof!=null){
            $equipesprof=$prof->getEquipes();
            foreach($equipesprof as $equipeprof) {
                $prof->removeEquipe($equipeprof);
            }
        $em->remove($prof);
        $em->flush(); }
        if ($equipes != null) {
            foreach ($equipes as $equipe) {
                if ($equipe->getIdProf1() == $user) {
                    $equipe->setIdProf1(null);
                    $eleves=$repositoryEleves->createQueryBuilder('el')
                                            ->where('el.equipe =:equipe')
                                            ->setParameter('equipe', $equipe)
                        ->getQuery()->getResult();
                    if ($eleves !=null){
                        foreach($eleves as $eleve){
                            $eleve->setEquipe(null);
                            $em->remove($eleve);
                            $em->flush();
                        }
                    }
                    $photos=$repositoryEleves->createQueryBuilder('ph')
                        ->where('ph.equipe =:equipe')
                        ->setParameter('equipe', $equipe)
                        ->getQuery()->getResult();
                    if ($photos!=null){
                        foreach($photos as $photo){
                            $photo->setEquipe(null);
                            $em->remove($photo);
                            $em->flush();
                        }


                    }
                    $em->remove($equipe);
                    $em->flush();
                }
                if ($equipe->getIdProf2() == $user) {
                    $equipe->setIdProf2(null);
                }
            }
        }


        $em->remove($user);
        $em->flush();
        return parent::deleteAction();
    }


}

