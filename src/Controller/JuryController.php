<?php
// src/Controller/CoreController.php
namespace App\Controller;

use App\Entity\Coefficients;
use App\Entity\Equipes;
use App\Entity\Jures;
use App\Entity\Notes;
use App\Form\EquipesType;
use App\Form\NotesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class JuryController extends AbstractController
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {

        $this->requestStack = $requestStack;;

    }


    /**
     * @Route("cyberjury/accueil", name="cyberjury_accueil")
     */
    public function accueil(): Response

    {
        $session = $this->requestStack->getSession();
        $em = $this->getDoctrine()->getManager();
        $edition = $session->get('edition');

        //$edition = $em->merge($edition);


        $repositoryJures = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Jures');
        $user = $this->getUser();
        $jure = $repositoryJures->findOneBy(['iduser' => $user]);

        $id_jure = $jure->getId();

        $attrib = $jure->getAttributions();
        //dd($attrib);
        $repositoryEquipes = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipes');
        $repositoryEquipesadmin = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipesadmin');
        $repositoryNotes = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Notes');
        $repositoryMemoires = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Fichiersequipes');
        $listEquipes = array();
        $progression = array();
        $memoires = array();
        $listeEquipes=$repositoryEquipes->createQueryBuilder('e')
            ->addOrderBy('e.ordre','ASC')
            ->getQuery()->getResult();
        foreach ($listeEquipes as $equipe) {

           /* try {
                $equipe = $repositoryEquipes->createQueryBuilder('e')
                    ->leftJoin('e.equipeinter', 'eq')
                    ->andWhere('eq.lettre =:lettre')
                    ->setParameter('lettre', $key)

                    ->getQuery()->getSingleResult();
            } catch (\Exception $e) {
                $equipe = null;
            }*/
            foreach($attrib as $key => $value) {

                if ($equipe->getEquipeinter()->getLettre()==$key) {

                    $id = $equipe->getId();
                    $note = $repositoryNotes->EquipeDejaNotee($id_jure, $id);
                    $progression[$key] = (!is_null($note)) ? 1 : 0;

                    try {
                        $memoires[$key] = $repositoryMemoires->createQueryBuilder('m')
                            ->where('m.edition =:edition')
                            ->setParameter('edition', $edition)
                            //->andWhere('m.national = 1')
                            ->andWhere('m.typefichier = 0')
                            ->andWhere('m.equipe =:equipe')
                            ->setParameter('equipe', $equipe->getEquipeinter())
                            ->getQuery()->getSingleResult();
                    } catch (\Exception $e) {
                        $memoires[$key] = null;
                    }


                }
            }

        }

        $content = $this->renderView('cyberjury/accueil.html.twig',
            array('listeEquipes' => $listeEquipes, 'progression' => $progression, 'jure' => $jure, 'memoires' => $memoires)
        );


        return new Response($content);


    }

    /**
     * @Security("is_granted('ROLE_JURY')")
     *
     * @Route( "/infos_equipe/{id}", name ="cyberjury_infos_equipe",requirements={"id_equipe"="\d{1}|\d{2}"})
     */
    public function infos_equipe(Request $request, Equipes $equipe, $id): Response
    {
        $user = $this->getUser();
        $nom = $user->getUserIdentifier();

        $repositoryJure = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Jures');

        $jure = $repositoryJure->findOneByNomJure($nom);
        $id_jure = $jure->getId();

        $note = $repositoryNotes = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Notes')
            ->EquipeDejaNotee($id_jure, $id);
        $progression = (!is_null($note)) ? 1 : 0;

        $repositoryEquipes = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipes');

        $lettre = $equipe->getEquipeinter()->getLettre();

        $repositoryEquipesadmin = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipesadmin');
        $equipeadmin = $repositoryEquipesadmin->find(['id' => $equipe->getEquipeinter()->getId()]);


        $repositoryEleves = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Elevesinter');
        $repositoryUser = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:User');
        $listEleves = $repositoryEleves->createQueryBuilder('e')
            ->where('e.equipe =:equipe')
            ->setParameter('equipe', $equipeadmin)
            ->getQuery()->getResult();

        try {
            $memoires = $this->getDoctrine()->getManager()
                ->getRepository('App:Fichiersequipes')->createQueryBuilder('m')
                ->where('m.equipe =:equipe')
                ->setParameter('equipe', $equipeadmin)
                ->andWhere('m.typefichier = 0')
                ->getQuery()->getResult();
        } catch (\Exception $e) {
            $memoires = null;
        }

        $idprof1 = $equipe->getEquipeinter()->getIdProf1();
        $idprof2 = $equipe->getEquipeinter()->getIdProf2();
        $mailprof1 = $repositoryUser->find(['id' => $idprof1])->getEmail();
        $telprof1 = $repositoryUser->find(['id' => $idprof1])->getPhone();
        if ($idprof2 != null) {
            $mailprof2 = $repositoryUser->find(['id' => $idprof2])->getEmail();
            $telprof2 = $repositoryUser->find(['id' => $idprof2])->getPhone();
        } else {
            $mailprof2 = null;
            $telprof2 = null;
        }


        $content = $this->renderView('cyberjury/infos.html.twig',
            array(
                'equipe' => $equipe,
                'mailprof1' => $mailprof1,
                'mailprof2' => $mailprof2,
                'telprof1' => $telprof1,
                'telprof2' => $telprof2,
                'listEleves' => $listEleves,
                'id_equipe' => $id,
                'progression' => $progression,
                'jure' => $jure,
                'memoires' => $memoires
            )
        );
        return new Response($content);
    }

    /**
     * @Security("is_granted('ROLE_JURY')")
     *
     * @Route("/lescadeaux", name="cyberjury_lescadeaux")
     *
     */
    public function lescadeaux(Request $request)
    {
        $user = $this->getUser();
        $nom = $user->getUsername();

        $repositoryJure = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Jures');

        $jure = $repositoryJure->findOneByNomJure($nom);
        $id_jure = $jure->getId();

        $repositoryCadeaux = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Cadeaux');
        $ListCadeaux = $repositoryCadeaux->getListCadeaux();

        $content = $this->renderView('cyberjury/lescadeaux.html.twig',
            array('ListCadeaux' => $ListCadeaux,
                'jure' => $jure)
        );
        return new Response($content);
    }

    /**
     * @Security("is_granted('ROLE_JURY')")
     *
     * @Route("/lesprix", name="cyberjury_lesprix")
     *
     */
    public function lesprix(Request $request)
    {
        $user = $this->getUser();
        $nom = $user->getUsername();

        $repositoryJure = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Jures');

        $jure = $repositoryJure->findOneByNomJure($nom);
        $id_jure = $jure->getId();
        $repositoryPrix = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Prix');


        $ListPremPrix = $repositoryPrix->findByClassement('1er');
        $ListDeuxPrix = $repositoryPrix->findByClassement('2ème');
        $ListTroisPrix = $repositoryPrix->findByClassement('3ème');

        $repositoryJure = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Jures');

        $jure = $repositoryJure->findOneByNomJure($nom);
        $id_jure = $jure->getId();

        $content = $this->renderView('cyberjury/lesprix.html.twig',
            array('ListPremPrix' => $ListPremPrix,
                'ListDeuxPrix' => $ListDeuxPrix,
                'ListTroisPrix' => $ListTroisPrix,
                'jure' => $jure)
        );
        return new Response($content);
    }

    /**
     * @Security("is_granted('ROLE_JURY')")
     *
     * @Route("palmares", name="cyberjury_palmares")
     *
     */
    public function palmares(Request $request)
    {
        $user = $this->getUser();
        $nom = $user->getUsername();

        $repositoryJure = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Jures');

        $jure = $repositoryJure->findOneByNomJure($nom);
        $id_jure = $jure->getId();
        $repositoryEquipes = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipes');
        $em = $this->getDoctrine()->getManager();

        $repositoryClassement = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Classement');

        $NbrePremierPrix = $repositoryClassement
            ->findOneByNiveau('1er')
            ->getNbreprix();

        $NbreDeuxPrix = $repositoryClassement
            ->findOneByNiveau('2ème')
            ->getNbreprix();

        $NbreTroisPrix = $repositoryClassement
            ->findOneByNiveau('3ème')
            ->getNbreprix();

        $ListPremPrix = $repositoryEquipes->palmares(1, 0, $NbrePremierPrix); // classement par rang croissant
        $offset = $NbrePremierPrix;
        $ListDeuxPrix = $repositoryEquipes->palmares(2, $offset, $NbreDeuxPrix);
        $offset = $offset + $NbreDeuxPrix;
        $ListTroisPrix = $repositoryEquipes->palmares(3, $offset, $NbreTroisPrix);

        $rang = 0;

        foreach ($ListPremPrix as $equipe) {
            $niveau = '1er';
            $equipe->setClassement($niveau);
            $rang = $rang + 1;
            $equipe->setRang($rang);
            $em->persist($equipe);
            $em->flush();
        }

        foreach ($ListDeuxPrix as $equipe) {
            $niveau = '2ème';
            $equipe->setClassement($niveau);
            $rang = $rang + 1;
            $equipe->setRang($rang);
            $em->persist($equipe);
            $em->flush();
        }
        foreach ($ListTroisPrix as $equipe) {
            $niveau = '3ème';
            $equipe->setClassement($niveau);
            $rang = $rang + 1;
            $equipe->setRang($rang);
            $em->persist($equipe);
            $em->flush();
        }

        $content = $this->renderView('cyberjury/palmares.html.twig',
            array('ListPremPrix' => $ListPremPrix,
                'ListDeuxPrix' => $ListDeuxPrix,
                'ListTroisPrix' => $ListTroisPrix,
                'NbrePremierPrix' => $NbrePremierPrix,
                'NbreDeuxPrix' => $NbreDeuxPrix,
                'NbreTroisPrix' => $NbreTroisPrix,
                'jure' => $jure)
        );
        return new Response($content);
    }

    /**
     *
     * @Security("is_granted('ROLE_JURY')")
     *
     * @Route("/evaluer_une_equipe/{id}", name="cyberjury_evaluer_une_equipe", requirements={"id_equipe"="\d{1}|\d{2}"})
     *
     */
    public function evaluer_une_equipe(Request $request, Equipes $equipe, $id)
    {
        $user = $this->getUser();
        $jure = $this->getDoctrine()->getRepository(Jures::class)->findOneBy(['iduser' => $user]);
        $repositoryEquipes = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipes');

        $lettre = $equipe->getEquipeinter()->getLettre();


        $attrib = $jure->getAttributions();

        $em = $this->getDoctrine()->getManager();

        $notes = $repositoryNotes = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Notes')
            ->EquipeDejaNotee($jure, $id);

        $repositoryMemoires = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Fichiersequipes');
        try {

            $memoire = $repositoryMemoires->createQueryBuilder('m')
                ->where('m.equipe =:equipe')
                ->setParameter('equipe', $equipe->getEquipeinter())
                ->andWhere('m.typefichier = 0')
                ->andWhere('m.national = 1')
                ->getQuery()->getSingleResult();

        } catch (\Exception $e) {
            $memoire = null;

        }

        $flag = 0;

        if (is_null($notes)) {
            $notes = new Notes();
            $notes->setEquipe($equipe);
            $notes->setJure($jure);
            $progression = 0;

            if ($attrib[$lettre] == 1) {
                $form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee' => true, 'EST_Lecteur' => true,));
                $flag = 1;
            } else {
                $notes->setEcrit(0);
                $form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee' => true, 'EST_Lecteur' => false,));
            }
        } else {
            $notes = $this->getDoctrine()
                ->getManager()
                ->getRepository('App:Notes')
                ->EquipeDejaNotee($jure, $id);
            $progression = 1;

            if ($attrib[$lettre] == 1) {
                $form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee' => false, 'EST_Lecteur' => true,));
                $flag = 1;
            } else {
                $notes->setEcrit('0');
                $form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee' => false, 'EST_Lecteur' => false,));
            }
        }
        $coefficients = $this->getDoctrine()->getRepository(Coefficients::class)->findOneBy(['id' => 1]);
        // Si la requête est en post, c'est que le visiteur a soumis le formulaire.
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            //enregistrement des valeurs dans notes
            $coefficients = $this->getDoctrine()->getRepository(Coefficients::class)->findOneBy(['id' => 1]);

            $notes->setCoefficients($coefficients);
            $total=$notes->getPoints();
            $notes->setTotal($total);

            $em->persist($notes);
            $em->flush();

            //$request->getSession()->getFlashBag()->add('notice', 'Notes bien enregistrées');
            // puis on redirige vers la page de visualisation de cette note dans le tableau de bord
            return $this->redirectToroute('cyberjury_tableau_de_bord');
        }
        // Si on n'est pas en POST, alors on affiche le formulaire.
        $type_salle = 'zoom';

        if (stripos($equipe->getSalle(), 'renater')) {
            $type_salle = 'renater';

        }
        $content = $this->renderView('cyberjury/evaluer.html.twig',
            array(
                'equipe' => $equipe,
                'type_salle' => $type_salle,

                'form' => $form->createView(),
                'flag' => $flag,
                'progression' => $progression,
                'jure' => $jure,
                'coefficients' => $coefficients,
                'memoire' => $memoire
            ));
        return new Response($content);

    }

    /**
     * @Security("is_granted('ROLE_JURY')")
     *
     * @Route("/tableau_de_bord", name ="cyberjury_tableau_de_bord")
     *
     */
    public function tableau(Request $request): Response
    {
        $user = $this->getUser();
        $nom = $user->getUsername();

        $repositoryJure = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('App:Jures');

        $jure = $repositoryJure->findOneByIduser($this->getUser());
        $id_jure = $jure->getId();

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Notes');
        $em = $this->getDoctrine()->getManager();

        $MonClassement = $repository->MonClassement($id_jure);

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipes');
        $repositoryMemoires = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Fichiersequipes');


        $em = $this->getDoctrine()->getManager();
        $memoires = array();
        $listEquipes = array();
        $j = 1;
        foreach ($MonClassement as $notes) {
            $id = $notes->getEquipe();
            $equipe = $repository->find($id);
            $listEquipes[$j]['id'] = $equipe->getId();
            $listEquipes[$j]['infoequipe'] = $equipe->getEquipeinter();
            $listEquipes[$j]['lettre'] = $equipe->getEquipeinter()->getLettre();
            $listEquipes[$j]['titre'] = $equipe->getEquipeinter()->getTitreProjet();
            $listEquipes[$j]['exper'] = $notes->getExper();
            $listEquipes[$j]['demarche'] = $notes->getDemarche();
            $listEquipes[$j]['oral'] = $notes->getOral();
            $listEquipes[$j]['origin'] = $notes->getOrigin();
            $listEquipes[$j]['wgroupe'] = $notes->getWgroupe();
            $listEquipes[$j]['ecrit'] = $notes->getEcrit();
            $listEquipes[$j]['points'] = $notes->getPoints();
            $memoires[$j] = $repositoryMemoires->createQueryBuilder('m')
                ->andWhere('m.equipe =:equipe')
                ->setParameter('equipe', $equipe->getEquipeinter())
                ->andWhere('m.national =:valeur')
                ->setParameter('valeur', 1)
                ->andWhere('m.typefichier =:typefichier')
                ->setParameter('typefichier', '0')
                ->getQuery()->getOneOrNullResult();

            $j++;

        }

        $content = $this->renderView('cyberjury/tableau.html.twig',
            array('listEquipes' => $listEquipes, 'jure' => $jure, 'memoires' => $memoires)
        );
        return new Response($content);
    }

    /**
     *
     * @Security("is_granted('ROLE_JURY')")
     *
     *
     * @Route("/phrases_amusantes/{id}", name = "cyberjury_phrases_amusantes",requirements={"id_equipe"="\d{1}|\d{2}"})
     */
    public function phrases(Request $request, Equipes $equipe, $id)
    {
        $user = $this->getUser();
        $nom = $user->getUsername();
        $repositoryJure = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Jures');
        $jure = $repositoryJure->findOneByNomJure($nom);
        $id_jure = $jure->getId();
        $notes = $repositoryNotes = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Notes')
            ->EquipeDejaNotee($id_jure, $id);
        $progression = (!is_null($notes)) ? 1 : 0;
        $repositoryPhrases = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Phrases');
        $repositoryLiaison = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Liaison');
        $repositoryEquipes = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Equipes');
        $repositoryMemoires = $this->getDoctrine()
            ->getManager()
            ->getRepository('App:Fichiersequipes');
        $idadm = $equipe->getEquipeinter();
        try {
            $memoire = $repositoryMemoires->createQueryBuilder('m')
                ->where('m.equipe =:equipe')
                ->setParameter('equipe', $equipe->getEquipeinter())
                ->andWhere('m.typefichier = 0')
                ->andWhere('m.national = TRUE')
                ->getQuery()->getSingleResult();
        } catch (\Exception $e) {
            $memoire = null;

        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(EquipesType::class, $equipe, array('Attrib_Phrases' => true, 'Attrib_Cadeaux' => false));
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->persist($equipe);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Phrase et prix amusants bien enregistrés');
            return $this->redirectToroute('cyberjury_accueil');
        }
        $content = $this->renderView('cyberjury\phrases.html.twig',
            array(
                'equipe' => $equipe,
                'form' => $form->createView(),
                'progression' => $progression,
                'jure' => $jure,
                'memoires' => $memoire
            ));
        return new Response($content);
    }


}
