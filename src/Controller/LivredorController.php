<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request ;
use App\Entity\Equipesadmin;
use App\Entity\Edition;
use App\Entity\Elevesinter;

use App\Entity\Livredor;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\HeaderUtils;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Alignment;
use Symfony\Component\Filesystem\Filesystem;
class LivredorController extends AbstractController
{     private $edition;
   
    public function __construct(SessionInterface $session)
        {
            $this->session = $session;
            $this->session->get('edition');
        }
    
    
    /**
     *  @IsGranted("ROLE_PROF")
     * @Route("/livredor/choix_equipe", name="livredor_choix_equipe")
     *  @return RedirectResponse|Response
     */
    public function choix_equipe(Request $request){
        
        $idprof=$this->getUser()->getId();
          $qb=$this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Equipesadmin')
                               ->createQueryBuilder('e')
                               ->where('e.edition =:edition')
                               ->setParameter('edition', $this->session->get('edition'))
                               ->andWhere('e.idProf1 =:prof1')
                               ->setParameter('prof1',$idprof)
                               ->orWhere('e.idProf2 =:prof2')
                               ->setParameter('prof2',$idprof)
                               ->andWhere('e.selectionnee = 1')
                               ->addOrderBy('e.lettre', 'ASC');;
           $equipes = $qb->getQuery()->getResult();
         if (count($equipes)>1){
        $form = $this->createFormBuilder()
                    ->add('equipe', EntityType::class,
                              ['class'=>Equipesadmin::class,
                                  'query_builder' => $qb,
                                'choice_label'=>'getInfoequipenat',        
                    ])
            ->add('save', SubmitType::class, ['label' => 'Valider'])
            ->getForm();
          $form->handleRequest($request); 
    if ($form->isSubmitted() && $form->isValid()){
        
        $equipe=$form->get('equipe')->getData();
         $id=$equipe->getId();
      return  $this->redirectToRoute('livredor_saisie_texte',['id'=>'equipe-'.$id]) ;
            }
             $content = $this
                 ->renderView('livredor\choix_equipe.html.twig', ['form'=>$form->createView()]);
        
       return new Response($content); 
         }
  
       return  $this->redirectToRoute('livredor_saisie_texte',['id'=>'equipe-'.$equipes[0]->getId()]) ;
       
      
        
    }
   /**
     * @IsGranted("ROLE_PROF")
     * @Route("/livredor/saisie_texte,{id}", name="livredor_saisie_texte")
     *  @return RedirectResponse|Response
     */
    public function saisie_texte(Request $request, $id) : Response
    {   
        $em=$this->getDoctrine()->getManager();
        $edition=$this->session->get('edition');
         $edition=$em->merge($edition);
         
         $form = $this->createFormBuilder();
        $user=$this->getUser();
         $ids=explode('-',$id);
        $type=$ids[0];
        $id_=$ids[1];
       
        if($type=='equipe'){
        
        $equipe=$this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Equipesadmin')->findOneById(['id'=>$id_]);   
        
        $livredor=$this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Livredor')->findOneByEquipe(['equipe'=>$equipe]);
      if($livredor != null){   
          $texteini=$livredor->getTexte();
      }
              if (!isset($texteini)) { 
             $texteini ='';
                            };
         
        $listeEleves=$this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Elevesinter')
                                ->createQueryBuilder('e')
                               ->where('e.equipe =:equipe')
                               ->setParameter('equipe', $equipe)
                               ->getQuery()->getResult();
        $noms='';
         foreach($listeEleves as $eleve){
             $noms= $noms.$eleve->getPrenom().', ';
             
         }
         $noms= substr($noms, 0, -2);
         
        }
        if(($type=='prof')or ($type=='comite') or ($type=='jury')){
            
            $prof=$this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:User')->findOneById(['id'=>$id_]); 
            $livredor=$this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Livredor')->findOneByUser(['user'=>$prof]);
      if($livredor != null){   
          $texteini=$livredor->getTexte();
         
      }
              if (!isset($texteini)) { 
             $texteini ='';
                            }
        }
       
       $form->add('texte', TextareaType::class,[
                'label' =>'Texte (1000 char. maxi)',
                'data' => $texteini,
            ])    
            ->add('save', SubmitType::class, ['label' => 'Valider']);
           $form=$form ->getForm();
        
          $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
         $texte=$form->get('texte')->getData();
        
         
         
         if(($type == 'equipe')){
       
         
        $livredor=$this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Livredor')->findOneByEquipe(['equipe'=>$equipe]);     
       if ($livredor==null){
            $livredor=new livredoreleves();
           } 
            
             $livredor->setNom($noms);
            $livredor->setTexte($texte);
            $livredor->setEquipe($equipe);
            $livredor->setEdition($edition);
           }
        
        if(($type == 'prof') or ($type == 'comite') or ($type == 'jury') ){ 
         try {       
        $livredor=$this->getDoctrine()->getManager()->getRepository('App:Livredor')
                                 ->createQueryBuilder('c')
                                 ->Where('c.edition =:edition')
                                 ->setParameter('edition',$edition)
                                 ->andWhere('c.user =:user')
                                -> setParameter('user',$user)
         ->getQuery()->getSingleResult();
                 }
         catch (\Exception $e){
             $livredor=null;
         }
         if ($livredor==null){
            $livredor=new livredor();
           }
            $livredor->setNom($user->getPrenom().' '.$user->getNom());
            $livredor->setUser($user);
            $livredor->setTexte($texte);
            $livredor->setEdition($edition);
            $livredor->setCategorie($type);
      }    
          
             $em->persist($livredor);
             $em->flush();
            return  $this->redirectToRoute('core_home') ;
            
   
   
    }
  
     if ($type=='equipe'){
        $content = $this
                          ->renderView('livredor\saisie_texte.html.twig', ['form'=>$form->createView(),'equipe' =>$equipe,'type'=>'equipe']);}
       if (($type=='prof')or ($type=='comite')or($type=='jury')){
        $content = $this
                                ->renderView('livredor\saisie_texte.html.twig', ['form'=>$form->createView(),'user' =>$this->getUser(),'type'=>$type]);}
       return new Response($content);
        
    } 
     /**
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @Route("/livredor/choix_edition,{action}", name="livredor_choix_edition")
     *  @return RedirectResponse|Response
     */
    public function choix_edition(Request $request,$action) : Response
    {   
        $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
       $qb=$repositoryEdition->createQueryBuilder('e')
                                      ->orderBy('e.ed', 'DESC');
       $repositoryLivredor= $this->getDoctrine()
		->getManager()
		->getRepository('App:Livredor');
             
       
       $Editions = $qb->getQuery()->getResult();
       
       foreach($Editions as $edition  ){
         $livredors[$edition->getid()] = $repositoryLivredor->createQueryBuilder('l')
                 ->where('l.edition =:edition')
                 ->setParameter('edition',$edition)
                 ->getQuery()->getResult();
           
           
       }
           
       
       
             return $this->render('livredor/choix_edition.html.twig', array('editions'=>$Editions,'livredors'=>$livredors,'action'=>$action));
        }
        
        
        
  
    
    /**
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @Route("/livredor/lire,{choix}", name="livredor_lire")
     *  @return RedirectResponse|Response
     */
    public function lire(Request $request,$choix) : Response
    {   
        $type=explode('-',$choix)[1];
        $idedition=explode('-',$choix)[0];
        $edition = $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition')->findOneById(['id'=>$idedition]);
        
        
        
        if ($type=='eleves'){
            $listetextes=$this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Livredor')->CreateQueryBuilder('l')
                                 ->leftJoin('l.equipe', 'eq')
                                 ->andWhere('l.edition =:edition')
                                 ->setParameter('edition', $edition)
                                 ->andWhere('l.categorie =:categorie')
                                 ->setParameter('categorie','equipe')
                                 ->addOrderBy('eq.lettre','ASC')
                                 ->getQuery()->getResult();
           
                    $content = $this
                 ->renderView('livredor\lire.html.twig', ['listetextes'=>$listetextes, 'choix'=>$type,'edition'=>$edition]);;
        }
        if ($type=='profs'){
            $listetextes=$this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Livredor')->CreateQueryBuilder('l')
                                 ->select('l')
                                 ->andWhere('l.edition =:edition')
                                  ->setParameter('edition', $edition)
                                 ->andWhere('l.categorie =:categorie')
                                 ->setParameter('categorie','prof')
                                 ->leftJoin('l.user', 'u')
                                 ->addOrderBy('u.nom','ASC')
                                 ->getQuery()->getResult();
           $equipes=$this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Equipesadmin')
                               ->createQueryBuilder('e')
                               ->Where('e.edition =:edition')
                               ->setParameter('edition', $edition)
                               ->andWhere('e.selectionnee = 1')
                               ->addOrderBy('e.lettre', 'ASC')
                                ->getQuery()
                                ->getResult();
            $i=0;
            foreach($listetextes as $texte){
                             $prof = $texte->getUser();
                             $lettres_equipes_prof[$i] ='';
                             foreach($equipes as $equipe){ 
                                 
                               
                                 if (($equipe->getIdProf1() == $prof->getId()) or ($equipe->getIdProf2() == $prof ->getId()) ){
                                     
                                      if (strlen($lettres_equipes_prof[$i])>0){
                                     $lettres_equipes_prof[$i]= $lettres_equipes_prof[$i].', '.$equipe->getLettre();
                                      }
                                      if (strlen($lettres_equipes_prof[$i])==0) { $lettres_equipes_prof[$i]=$lettres_equipes_prof[$i].$equipe->getLettre();}
                                  
                                 }
                               
                             }
                             $i=$i+1;
            }
            $content = $this
                                    ->renderView('livredor\lire.html.twig', ['listetextes'=>$listetextes, 'lettres_equipes_prof'=>$lettres_equipes_prof,'choix'=>$type,'edition'=>$edition]);
        }
          if (($type=='comite') or ( $type=='jury')){
            $listetextes=$this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Livredor')->CreateQueryBuilder('l')
                                 ->select('l')
                                 ->andWhere('l.edition =:edition')
                                  ->setParameter('edition', $edition)
                                  ->andWhere('l.categorie =:categorie')
                                 ->setParameter('categorie',$type)
                                 ->leftJoin('l.user', 'u')
                                 ->addOrderBy('u.nom','ASC')
                                 ->getQuery()->getResult();
            
                    
          
         
                    $content = $this
                                    ->renderView('livredor\lire.html.twig', ['listetextes'=>$listetextes, 'choix'=>$type, 'edition'=>$edition]);;
                    }
        return new Response($content);
    
    }
    /**
     * @IsGranted("ROLE_COMITE")
     * @Route("/livredor/editer,{choix}", name="livredor_editer")
     * 
     */
    public function editer(Request $request,$choix) {
        
        $idedition=explode('-',$choix)[0];
        $type=explode('-',$choix)[1];
        $edition = $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition')->findOneById(['id'=>$idedition]);
        
        $phpWord = new  PhpWord();
       
        $section = $phpWord->addSection();
        $paragraphStyleName = 'pStyle';
        $phpWord->addParagraphStyle($paragraphStyleName, array( 'align'  => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER, 'spaceAfter' => 100));

        $phpWord->addTitleStyle(1, array('bold' => true,  'size'=> 14 ,'spaceAfter' =>240));
        $fontTitre = 'styletitre';
                $phpWord->addFontStyle(
                    $fontTitre,
                    array('name' => 'Tahoma', 'size' => 12 , 'color' => '0000FF', 'bold' => true, 'align'=>'center')
                );
         //$fontTitre = new \PhpOffice\PhpWord\Style\Font();
          $fontTexte = 'styletexte';
                $phpWord->addFontStyle(
                    $fontTexte,
                    array('name' => 'Arial', 'size' => 12, 'color' => '000000')
                );
          
        if (($type=='prof') or($type=='comite')or($type=='jury')){
            $livredor=$this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Livredor')->createQueryBuilder('l')
                                                                                         ->leftJoin('l.user','p')
                                                                                         ->addOrderBy('p.nom','ASC')
                                                                                         ->andWhere('l.categorie =:categorie')
                                                                                         ->setParameter('categorie', $type)  
                                                                                         ->andWhere('l.edition =:edition')
                                                                                         ->setParameter('edition', $edition)  
                                                                                        ->getQuery()->getResult();
            
          if ($type=='prof') {
           $equiperepository= $this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Equipesadmin');
             $section->addText('Livre d\'or des professeurs - Edition '.$this->session->get('edition')->getEd(),  array('bold' => true,  'size'=> 14 ,'spaceAfter' =>240), 'pStyle');
              $section->addTextBreak(3);
             if ($livredor!=null){
              foreach($livredor as $texte){ 
                  $prof=$texte->getUser();
                  
                  $equipes=$equiperepository->getEquipes_prof_cn($prof);
                  if (count($equipes)>1){
                  $titreprof =$prof->getNomPrenom().'( équipes ';}
                  else{ $titreprof =$prof->getNomPrenom().'( équipe ';}
                  
                  $i=0;
                foreach($equipes as $equipe){
                    $titreprof=$titreprof.$equipe->getLettre();
                    if ($i<array_key_last($equipes))
                           $titreprof=$titreprof.', ';
                    $i++;
                }
                $titreprof=$titreprof.' )';
           $section->addText($titreprof,'styletitre');  
           $textlines = explode("\n", $texte->getTexte());

           $textrun = $section->addTextRun();
           $textrun->addText(array_shift($textlines), 'styletexte');
                foreach($textlines as $line) {
                    $textrun->addTextBreak();
                    // maybe twice if you want to seperate the text
                    // $textrun->addTextBreak(2);
                    $textrun->addText($line,null, 'styletexte');
               }
           // $section->addText($texte->getTexte(),'styletexte');  
            //$lineStyle = array('weight' => 1, 'width' => 200, 'height' => 0, 'color'=> '0000FF');
            
            $section->addTextBreak(3);
             //$section->addLine($lineStyle);
           $section->addText('------',null,'pStyle');
             }}
             }
               if (($type=='comite')or($type=='jury'))  {
              
             $categorie= $type;;
             $titrepage ='Livre d\'or du '.$categorie.' - Edition '.$this->session->get('edition')->getEd();
             
             
             $section->addText($titrepage, array('bold' => true,  'size'=> 14 ,'spaceAfter' =>240) , 'pStyle');
             $section->addTextBreak(3);
             if ($livredor!=null){
            foreach($livredor as $texte){ 
                  $titre=$texte->getNom();
                                 
           $section->addText($titre,'styletitre');  
           
            $textlines = explode("\n", $texte->getTexte());

           $textrun = $section->addTextRun();
           $textrun->addText(array_shift($textlines), 'styletexte');
                foreach($textlines as $line) {
                    $textrun->addTextBreak();
                    // maybe twice if you want to seperate the text
                    // $textrun->addTextBreak(2);
                    $textrun->addText($line, 'styletexte');
               }
            
            $section->addTextBreak(3);
             //$section->addLine($lineStyle);
           $section->addText('------',null, 'pStyle');
              }
               
             }
               }
        }
             if ($type=='equipe'){
            $livredor=$this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Livredor')
                                ->createQueryBuilder('e')
                                ->where('e.edition =:edition')
                                ->setParameter('edition',$edition)
                                ->andWhere('e.categorie =:categorie')
                                ->setParameter('categorie', $type)
                                ->leftJoin('e.equipe','eq')
                                ->orderBy('eq.lettre','ASC')
                               ->getQuery()->getResult();
                  
          if ($livredor!=null){
             $section->addText('Livre d\'or des élèves- Edition '.$this->session->get('edition')->getEd(),  array('bold' => true,  'size'=> 14 ,'spaceAfter' =>240), 'pStyle');
              $section->addTextBreak(3);
         foreach($livredor as $texte){ 
          
           $equipe= $texte->getEquipe();
           
            $titreEquipe='Equipe '.$texte->getEquipe()->getInfoequipenat().' ('.$texte->getNom().')';
           ;
           $titre= $section->addText($titreEquipe);  
           $titre->setFontStyle('styletitre');
           
           $textlines = explode("\n", $texte->getTexte());

           $textrun = $section->addTextRun();
           $textrun->addText(array_shift($textlines), 'styletexte');
                foreach($textlines as $line) {
                    $textrun->addTextBreak();
                    // maybe twice if you want to seperate the text
                    // $textrun->addTextBreak(2);
                    $textrun->addText($line, 'styletexte');
               }
            //$lineStyle = array('weight' => 1, 'width' => 200, 'height' => 0, 'color'=> '0000FF');
            $section->addTextBreak(3);
            //$section->addLine($lineStyle);   
            $texte=$section->addText('------',null,'pstyle');
             }
          }
             }
            $categorie=$type;
            $filesystem = new Filesystem();
            $fileName = $this->session->get('edition')->getEd().'livre d\'or '.$categorie.'.docx';  
         
             $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord,'Word2007');
             $objWriter ->save($this->getParameter('app.path.tempdirectory').'/'.$fileName);
             $response = new Response(file_get_contents($this->getParameter('app.path.tempdirectory').'/'.$fileName));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
                    $disposition = HeaderUtils::makeDisposition(
                                            HeaderUtils::DISPOSITION_ATTACHMENT,
                                            $fileName
                                                  );
            $response->headers->set('Content-Type','application/msword'); 
            $response->headers->set('Content-Disposition', $disposition);
            $filesystem->remove($this->getParameter('app.path.tempdirectory').'/'.$fileName);   
            return $response;   
              
                 
              
              
             
      
        
    }
    
    
 
}