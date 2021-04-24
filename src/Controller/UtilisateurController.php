<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Equipesadmin;
use App\Entity\Elevesinter;
use App\Entity\Rne;
use App\Service\Mailer;
use App\Form\UserType;
use App\Form\UserRegistrationFormType;
use App\Form\InscrireEquipeType;
use App\Form\ModifEquipeType;
use App\Form\ResettingType;
use App\Form\ProfileType;

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
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class UtilisateurController extends AbstractController
{    private $session;
   
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
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
     *  
     * @Route("/Utilisateur/inscrire_equipe,{idequipe}", name="inscrire_equipe")
     */
    public function inscrire_equipe (Request $request,Mailer $mailer,$idequipe)
    {   $date = new \datetime('now');
    
   
    
        if($date<$this->session->get('edition')->getDateouverturesite() or ($date>$this->session->get('edition')->getDateclotureinscription())){
        
           $request->getSession()
                                                     ->getFlashBag()
                                                     ->add('info', 'Les inscriptions sont closes. Inscriptions entre le '.$this->session->get('edition')->getDateouverturesite()->format('d-m-Y').' et le '.$this->session->get('edition')->getDatelimcia()->format('d-m-Y').' 22 heures(heure de Paris)') ;
            
            
            return $this->redirectToRoute('core_home');
        
    
        }
        $em=$this->getDoctrine()->getManager();
         $repositoryEquipesadmin=$em->getRepository('App:Equipesadmin');
         $repositoryEleves=$em->getRepository('App:Elevesinter');
        if( null!=$this->getUser()){
            
         if ($this->getUser()->getRoles()[0]=='ROLE_PROF'){
         $edition=$this->session->get('edition');
         $edition=$em->merge($edition);
         if ($idequipe=='x'){
         $equipe = new Equipesadmin(); 
          $form1=$this->createForm(InscrireEquipeType::class, $equipe,['rne'=>$this->getUser()->getRne()]);
         $modif=false;
         $eleves=[];         }
         else{
           $equipe=   $repositoryEquipesadmin->findOneById(['id'=>intval($idequipe)]);
           $eleves= $repositoryEleves->findByEquipe(['equipe'=>$equipe]);
           $form1=$this->createForm(ModifEquipeType::class, $equipe,['rne'=>$this->getUser()->getRne(),'eleves'=>$eleves]); 
           $modif=true;
         }
        
         $form1->handleRequest($request); 
          if ($form1->isSubmitted() && $form1->isValid()){
              
              $repositoryRne=$em->getRepository('App:Rne');
               
              $lastEquipe=$repositoryEquipesadmin->createQueryBuilder('e')
                                                                            ->select('e, MAX(e.numero) AS max_numero')
                                                                            ->andWhere('e.edition = :edition')
                                                                            ->setParameter('edition', $edition)
                                                                            ->getQuery()->getSingleResult();
              
              if(($lastEquipe['max_numero']==null) and ($modif==false)){
                  $numero=1;
                $equipe->setNumero($numero);
              }
              elseif( $modif==false){
                  $numero= intval($lastEquipe['max_numero'])+1;
                  $equipe->setNumero($numero);
              }
              
             $rne_objet=$repositoryRne->findOneByRne(['rne'=>$this->getUser()->getRne()]);
             $equipe->setPrenomprof1($form1->get('idProf1')->getData()->getPrenom());
             $equipe->setNomprof1($form1->get('idProf1')->getData()->getNom());
             if ($form1->get('idProf2')->getData() != null){
             $equipe->setPrenomprof2($form1->get('idProf2')->getData()->getPrenom());
             $equipe->setNomprof2($form1->get('idProf2')->getData()->getNom());
             }
             $equipe->setEdition($edition);
             $equipe->setRne($this->getUser()->getRne());
             $equipe->setRneid($rne_objet);
             $equipe->setDenominationLycee($rne_objet->getDenominationPrincipale());
             $equipe->setNomLycee($rne_objet->getAppellationOfficielle());
             $equipe->setLyceeAcademie($rne_objet->getAcademie());
             $equipe->setLyceeLocalite($rne_objet->getAcheminement()); 
             
             $em->persist($equipe);
             $em->flush();
             
               for($i=1;$i<7;$i++){
                  if ($form1->get('prenomeleve'.$i)->getData()!=null){
                     try {
                         
                        $id= $form1->get('id'.$i)->getData();
                        $eleve[$i]=$repositoryEleves->find(['id'=>$form1->get('id'.$i)->getData()]);
                     } catch (\Exception $ex) {
                              $eleve[$i]=new Elevesinter(); 
                     }
                     
                      $eleve[$i]->setPrenom($form1->get('prenomeleve'.$i)->getData());
                      $eleve[$i]->setNom($form1->get('nomeleve'.$i)->getData());
                      $eleve[$i]->setCourriel($form1->get('maileleve'.$i)->getData());
                      $eleve[$i]->setGenre($form1->get('genreeleve'.$i)->getData());
                      $eleve[$i]->setClasse($form1->get('classeeleve'.$i)->getData());
                      $eleve[$i]->setEquipe($equipe);
                      $em->persist($eleve[$i]);
                      $em->flush();
                  }
               }
             
            
             $mailer->sendConfirmeInscriptionEquipe($equipe,$this->getUser(), $modif);
             
               
             if($modif==false){
               
              return $this->redirectToRoute('fichiers_afficher_liste_fichiers_prof ', array('infos'=>$equipe->getId().'-'.$this->session->get('concours').'-liste_equipe'));
              }
            if ($modif ==true){
                
                return $this->redirectToRoute('inscrire_equipe', array('idequipe'=>$equipe->getId()));  
            }
          }
         return $this->render('register/inscrire_equipe.html.twig',array('form'=>$form1->createView(),'equipe'=>$equipe,'concours'=>$this->session->get('concours'),'choix'=>'liste_prof', 'modif'=>$modif, 'eleves'=>$eleves));
             
         }
         else{  return $this->redirectToRoute('core_home');}
        }
        
        else{
      
            return $this->redirectToRoute('login');
           
        }
        
        
        
    }
   /**
     * 
     *  @Security("is_granted('ROLE_PROF')")
     *  
     * @Route("/Utilisateur/supr_eleve", name="supr_eleve")
     */ 
        
   public function supr_eleve(Request $request){
       
       $em=$this->getDoctrine()->getManager();
       $repositoryEleves=$em->getRepository('App:Elevesinter');
     
      
       $ideleve=$request->get('myModalID');
       $eleve= $repositoryEleves->findOneById(['id'=>intval($ideleve)]); 
       $equipe=$eleve->getEquipe();
       $eleves= $repositoryEleves->createQueryBuilder('e')
                                                   ->where('e.equipe =:equipe')
                                                   ->setParameter('equipe',$equipe)
                                                   ->getQuery()->getResult();
       if (count($eleves)>2){
       
       if ($eleve->getAutorisationphotos() !=null){
           $autorisation = $eleve->getAutorisationphotos();
           $file= $autorisation->getFichier();
           copy('fichiers/autorisations/'.$file, 'fichiers/autorisations/archives/'.$file);// dans le cas où l'élève d'ésinscrit a participé aux cia avec une autroisation photo mais ne participe plus au cn 
           
           $eleve->setAutorisationphotos(null);
           $em->remove($autorisation);
           $em->flusch();
           
       }
    $em->remove($eleve);
       $em->flush();}
      if  (count($eleves)==2) {
          
          $request->getSession()
                            ->getFlashBag()
                            ->add('alert','Désinscription impossible. Une équipe ne peut avoir moins de deux élèves !');
          
          
      }
       
       
   return  $this->redirectToRoute('inscrire_equipe', array('idequipe'=>$equipe->getId()));
       
       
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