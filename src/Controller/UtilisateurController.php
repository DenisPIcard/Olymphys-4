<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Equipesadmin;
use App\Entity\Elevesinter;
use App\Entity\Rne;
use App\Entity\Professeurs;
use App\Service\Mailer;
use App\Form\UserType;
use App\Form\UserRegistrationFormType;
use App\Form\InscrireEquipeType;
use App\Form\ModifEquipeType;
use App\Form\ResettingType;
use App\Form\ProfileType;
use App\Service\Maj_profsequipes;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\DependencyInjection\Security\UserProvider\EntityFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Mailer\MailerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RequestStack;


class UtilisateurController extends AbstractController
{   private $requestStack;
    private $em;
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em=$em;
    }

    /**
     * @Route("/profile_show", name="profile_show")
     */
    public function profileShow()
    {
        $user = $this->getUser();
        return $this->render('profile/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     * @Route("profile_edit", name="profile_edit")
     */
    public function profileEdit(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('core_home');
        }
        return $this->render('profile/edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    /**
     *
     *
     * @IsGranted("ROLE_PROF")
     * @Route("/Utilisateur/inscrire_equipe,{idequipe}", name="inscrire_equipe")
     */
    public function inscrire_equipe (Request $request,Mailer $mailer,$idequipe)
    {   $date = new \datetime('now');
        $session=$this->requestStack->getSession();

        if ($idequipe=='x') {
            if ($date < $session->get('edition')->getDateouverturesite() or ($date > $session->get('edition')->getDateclotureinscription())) {

                $request->getSession()
                    ->getFlashBag()
                    ->add('info', 'Les inscriptions sont closes. Inscriptions entre le ' . $session->get('edition')->getDateouverturesite()->format('d-m-Y') . ' et le ' . $session->get('edition')->getDateclotureinscription()->format('d-m-Y') . ' 22 heures(heure de Paris)');


                return $this->redirectToRoute('core_home');


            }
        }
        $em=$this->getDoctrine()->getManager();
        $repositoryEquipesadmin=$em->getRepository('App:Equipesadmin');
        $repositoryProfesseurs= $em->getRepository('App:Professeurs');
        $repositoryEleves=$em->getRepository('App:Elevesinter');
        $repositoryRne=$em->getRepository('App:Rne');
        if( null!=$this->getUser()){
            $rne_objet=$repositoryRne->findOneBy(['rne'=>$this->getUser()->getRne()]);
            if ($this->getUser()->getRoles()[0]=='ROLE_PROF'){
                $edition=$session->get('edition');

                $edition=$em->merge($edition);
                if ($idequipe=='x'){
                    $equipe = new Equipesadmin();
                    $form1=$this->createForm(InscrireEquipeType::class, $equipe,['rne'=>$this->getUser()->getRne()]);
                    $modif=false;
                    $eleves=[];         }
                else {
                    $equipe = $repositoryEquipesadmin->findOneBy(['id' => intval($idequipe)]);
                    $eleves=$repositoryEleves->findBy(['equipe'=>$equipe]);

                    if ((null===$request->request->get('modif_equipe')) and (null===$session->get('supr_eleve'))){
                        $oldEquipe = $repositoryEquipesadmin->findOneBy(['id' => intval($idequipe)]);
                       $session->set('oldequipe', $oldEquipe);
                        $oldListeEleves=$repositoryEleves->findBy(['equipe'=>$equipe]);
                       $session->set('oldlisteEleves', $oldListeEleves);
                    }


                    $eleves_supr=null;
                    if ($session->get('supr_eleve')!==null) { //le professeur efface l'élève sur le formulaire, mais ne le supprime pas encore
                        $eleves_supr = $session->get('supr_eleve');
                        $elevesinit = $repositoryEleves->findBy(['equipe' => $equipe]);

                        $i = 0;
                        foreach ($elevesinit as $eleveinit) {
                            $supr[$eleveinit->getId()] = false;
                            foreach ($eleves_supr as $eleve_supr) {
                                if ($eleveinit->getId() == $eleve_supr->getId()) {

                                    $supr[$eleveinit->getId()] = true;

                                }
                            }
                            if ($supr[$eleveinit->getId()] == false) {
                                    $elevesaff[$i] = $eleveinit;

                                    $i++;
                            }

                        }


                    }
                    if ($session->get('supr_eleve')==null){
                        $elevesaff= $repositoryEleves->findBy(['equipe'=>$equipe]);
                    }
                    $form1=$this->createForm(ModifEquipeType::class, $equipe,['rne'=>$this->getUser()->getRne(),'eleves'=>$elevesaff]);
                    $modif=true;
                }

                $form1->handleRequest($request);
                if ($form1->isSubmitted() && $form1->isValid()){
                    $oldEquipe=$session->get('oldequipe');
                    $oldListeEleves=$session->get('oldlisteEleves');

                    $repositoryRne=$em->getRepository('App:Rne');
                    $repositoryEleves=$em->getRepository('App:Elevesinter');
                    if($session->get('supr_eleve')!==null){
                        $eleves_supr=$session->get('supr_eleve');
                        foreach ($eleves_supr as $eleve_supr) {
                            $eleves=$repositoryEleves->findByEquipe(['equipe'=>$equipe]);
                            if(count($eleves)>2) {

                                $this->supr_eleve($eleve_supr->getId());


                            }
                            elseif(count($eleves)==2){
                                $request->getSession()
                                    ->getFlashBag()
                                    ->add('alert','Une équipe ne peut pas avoir moins de deux élèves');
                                break;

                            }
                        }


                    }

                    if($modif==false) {
                        $lastEquipe = $repositoryEquipesadmin->createQueryBuilder('e')
                            ->select('e, MAX(e.numero) AS max_numero')
                            ->andWhere('e.edition = :edition')
                            ->setParameter('edition', $edition)
                            ->getQuery()->getSingleResult();

                        if (($lastEquipe['max_numero'] == null) and ($modif == false)) {
                            $numero = 1;
                            $equipe->setNumero($numero);
                        } elseif ($modif == false) {
                            $numero = intval($lastEquipe['max_numero']) + 1;
                            $equipe->setNumero($numero);
                        }
                    }
                    $rne_objet=$repositoryRne->findOneBy(['rne'=>$this->getUser()->getRne()]);

                    $equipe->setPrenomprof1($form1->get('idProf1')->getData()->getPrenom());
                    $equipe->setNomprof1($form1->get('idProf1')->getData()->getNom());
                    if ($form1->get('idProf2')->getData() != null){
                        $equipe->setPrenomprof2($form1->get('idProf2')->getData()->getPrenom());
                        $equipe->setNomprof2($form1->get('idProf2')->getData()->getNom());
                    }
                    $equipe->setEdition($edition);
                    if ($modif==false) {
                        $equipe->setSelectionnee(false);}
                    $equipe->setRne($this->getUser()->getRne());
                    $equipe->setRneid($rne_objet);
                    $equipe->setDenominationLycee($rne_objet->getDenominationPrincipale());
                    $equipe->setNomLycee($rne_objet->getAppellationOfficielle());
                    $equipe->setLyceeAcademie($rne_objet->getAcademie());
                    $equipe->setLyceeLocalite($rne_objet->getAcheminement());
                    $nbeleves=$equipe->getNbeleves();
                    for($i=1;$i<7;$i++){
                        if ($form1->get('nomeleve'.$i)->getData()!=null){

                            $id=$form1->get('id'.$i)->getData();
                            if ($id!=0){
                                $id= $form1->get('id'.$i)->getData();
                                $eleve[$i]=$repositoryEleves->find(['id'=>$form1->get('id'.$i)->getData()]);
                            }
                            else{
                                $eleve[$i]=new Elevesinter();
                                $nbeleves =$nbeleves+1;
                            }

                            if (($form1->get('prenomeleve'.$i)->getData()==null) or ($form1->get('nomeleve'.$i)->getData()==null) or($form1->get('maileleve'.$i)->getData()==null) or ($form1->get('classeeleve'.$i)->getData()==null) ){
                                $request->getSession()
                                    ->getFlashBag()
                                    ->add('alert', 'Les données d\'un élève doivent être toutes complétées !') ;

                                return $this->render('register/inscrire_equipe.html.twig',array('form'=>$form1->createView(),'equipe'=>$equipe,'concours'=>$session->get('concours'),'choix'=>'liste_prof', 'modif'=>$modif, 'eleves'=>$eleves, 'rneObj'=>$rne_objet));
                            }
                            $eleve[$i]->setPrenom($form1->get('prenomeleve'.$i)->getData());
                            $eleve[$i]->setNom(strtoupper($form1->get('nomeleve'.$i)->getData()));
                            $eleve[$i]->setCourriel($form1->get('maileleve'.$i)->getData());
                            $eleve[$i]->setGenre($form1->get('genreeleve'.$i)->getData());
                            $eleve[$i]->setClasse($form1->get('classeeleve'.$i)->getData());
                            $eleve[$i]->setEquipe($equipe);

                            $em->persist($eleve[$i]);

                        }
                    }
                    $equipe->setNbEleves($nbeleves);
                    $em->persist($equipe);
                    $em->flush();
                    if ($modif==true){

                        $checkChange=$this->compare($equipe,$oldEquipe,$oldListeEleves);
                    }

                    $maj_profsequipes = new Maj_profsequipes($em);
                    $maj_profsequipes->maj_profsequipes($equipe);
                   $session->set('oldListeEleves',null);
                   $session->set('supr_eleve',null);

                    if($modif==false){
                        $mailer->sendConfirmeInscriptionEquipe($equipe,$this->getUser(), $modif,$checkChange);
                        return $this->redirectToRoute('fichiers_afficher_liste_fichiers_prof', array('infos'=>$equipe->getId().'-'.$session->get('concours').'-liste_equipe'));
                    }
                    if (($modif ==true) and ($checkChange != []) ){
                        $mailer->sendConfirmeInscriptionEquipe($equipe,$this->getUser(), $modif,$checkChange);
                        return $this->redirectToRoute('fichiers_afficher_liste_fichiers_prof', array('infos'=>$equipe->getId().'-'.$session->get('concours').'-liste_prof'));
                    }
                }
                return $this->render('register/inscrire_equipe.html.twig',array('form'=>$form1->createView(),'equipe'=>$equipe,'concours'=>$session->get('concours'),'choix'=>'liste_prof', 'modif'=>$modif, 'eleves'=>$eleves, 'rneObj'=>$rne_objet));

            }
            else{  return $this->redirectToRoute('core_home');}
        }

        else{

            return $this->redirectToRoute('login');

        }



    }

    public function compare($equipe,$oldEquipe,$oldListeEleves):array
    {
        $session=$this->requestStack->getSession();
        $checkchange = [];
        $repositoryEleves=$this->getDoctrine()->getRepository(Elevesinter::class);
        $oldnom = $oldEquipe->getTitreprojet();
        $nom = $equipe->getTitreprojet();
        if ($nom != $oldnom) {
            $checkchange['nom'] = 'le nom de l\'équipe';
        }
        $oldprof1 = $oldEquipe->getIdProf1();
        $prof1 = $equipe->getIdProf1();
        if ($prof1->getId() != $oldprof1->getId()) {
            $checkchange['prof1'] = 'prof1';
        }
        $oldprof2 = $oldEquipe->getIdProf2();
        $prof2 = $equipe->getIdProf2();
        if((null!==$prof2) and (null!==$oldprof2)) {
            if ($prof2->getId() != $oldprof2->getId()) {
                $checkchange['prof2'] = 'prof2';
            }
        }
        if((null!==$prof2) and (null===$oldprof2)){
            $checkchange['prof2'] = 'Ajout  du prof2';

        }
        if((null===$prof2) and (null!==$oldprof2)){
            $checkchange['prof2'] = 'Suppression du prof2';

        }
        $oldcontribfin = $oldEquipe->getContribfinance();
        $contribfin = $equipe->getContribfinance();
        if ($contribfin != $oldcontribfin) {
            $checkchange['contribfin'] = 'Contribution financière';
        }
        $oldOrigine = $oldEquipe->getOrigineprojet();
        $origine = $equipe->getOrigineprojet();
        if ($origine != $oldOrigine) {
            $checkchange['origine'] = 'Origine du projet';
        }
        $oldPartenaire = $oldEquipe->getPartenaire();
        $partenaire = $equipe->getPartenaire();
        if ($partenaire != $oldPartenaire) {
            $checkchange['partenaire'] = 'Partenaire';
        }

        $repositoryEleves=$this->getDoctrine()->getRepository(Elevesinter::class);
        $listeEleves=$repositoryEleves->findByEquipe(['equipe'=>$equipe]);

        $checkEleves=[];
        $checkOldEleves=[];
        foreach($listeEleves as $eleve){
            $checkEleves[$eleve->getId()]=$eleve->getId();
            foreach ($oldListeEleves as $oldEleve){

                $checkOldEleves[$oldEleve->getId()]=$oldEleve->getId();
                if ($oldEleve->getId()==$eleve->getId()){

                    if (($oldEleve->getNom()!=$eleve->getNom())or(($oldEleve->getPrenom())!=$eleve->getPrenom())or($oldEleve->getCourriel()!=$eleve->getCourriel())or ($oldEleve->getClasse()!=$eleve->getClasse())){
                        $checkchange['eleves'.$eleve->getId()] = 'Mofidification de l\'élève : '.$eleve->getNom();
                    }
                }
            }
        }

        if(count($listeEleves)!=count($oldListeEleves)){
            $elevesdif=[];
            if (count($listeEleves)>count($oldListeEleves)){
                foreach ($checkEleves as $checkEleve){

                    if(in_array($checkEleve,$checkOldEleves,false )==false){
                        $elevesdif[$checkEleve]=$checkEleve;
                    }
                }

                $message='';
                foreach($elevesdif as $elevedif){
                    $eleve=$repositoryEleves->find($elevedif);

                    $message.=$eleve->getNom().' '.$eleve->getPrenom().', ';
                }
                $checkchange['Eleve(s) inscrit(e-s)'] = 'Eleve(s) ajouté(e-s) : '.$message;

            }

            if (count($listeEleves)<count($oldListeEleves)){
                $listEleveSupr=$session->get('supr_eleve');
                $message='';
                foreach($listEleveSupr as $eleve){

                    $message.=$eleve->getNom().' '.$eleve->getPrenom().', ';
                }
                $checkchange['Eleve(s) désinscrit(e-s)'] = 'Eleve(s) désinscrit(e-s) : '.$message;
            }
           $session->set('supr_eleve',null);
           $session->set('oldListeEleves',null);
        }

        return $checkchange;
    }



    /**
     *
     *  @Security("is_granted('ROLE_PROF')")
     *
     * @Route("/Utilisateur/pre_supr_eleve", name="pre_supr_eleve")
     */

    public function pre_supr_eleve(Request $request)
    {
        $session=$this->requestStack->getSession();
        $em = $this->getDoctrine()->getManager();
        $repositoryEleves = $em->getRepository('App:Elevesinter');
        $ideleve = $request->get('myModalID');
        $eleve = $repositoryEleves->findOneById(['id' => intval($ideleve)]);
        $listeEleveSupr = $session->get('supr_eleve');
        $listeEleveSupr[$eleve->getId()] = $eleve;
       $session->set('supr_eleve', $listeEleveSupr);
        $equipe=$eleve->getEquipe();
        return  $this->redirectToRoute('inscrire_equipe', array('idequipe'=>$equipe->getId()));

    }
    /**
     *
     *  @Security("is_granted('ROLE_PROF')")
     *
     * @Route("/Utilisateur/supr_eleve,{eleve}", name="supr_eleve")
     */
    public function supr_eleve($eleveId){

        $em = $this->getDoctrine()->getManager();
        $repositoryEleves = $em->getRepository('App:Elevesinter');

        $eleve=$repositoryEleves->find($eleveId) ;

        $equipe = $eleve->getEquipe();
        $eleves= $repositoryEleves->createQueryBuilder('e')
            ->where('e.equipe =:equipe')
            ->setParameter('equipe',$equipe)
            ->getQuery()->getResult();
        if (count($eleves)>2)
        {

            if ($eleve->getAutorisationphotos() !=null){
                $autorisation = $eleve->getAutorisationphotos();
                $file= $autorisation->getFichier();
                copy('fichiers/autorisations/'.$file, 'fichiers/autorisations/trash/'.$file);// dans le cas où l'élève d'ésinscrit a participé aux cia avec une autroisation photo mais ne participe plus au cn

                $eleve->setAutorisationphotos(null);
                $em->remove($autorisation);
                $em->flush();

            }
            $equipe=$eleve->getEquipe();
            $equipe->setNbeleves($equipe->getNbeleves()-1);
            $em->persist($equipe);
            $em->remove($eleve);
            $em->flush();}



        //return  $this->redirectToRoute('inscrire_equipe', array('idequipe'=>$equipe->getId()));


    }


    /**
     *
     *  @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @Route("/Utilisateur/setlastvisit", name="setlastvisit")
     */
    public function setlastvisite(Request $request ){//fonction provisoire à supprimer après le mise au point du site

        $em=$this->getDoctrine()->getManager();
        $repositoryUser=$em->getRepository('App:User');
        $users=$repositoryUser->findAll();
        foreach($users as $user){

            $user->setLastVisit( new \datetime('now'));
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('core_home');

    }

}