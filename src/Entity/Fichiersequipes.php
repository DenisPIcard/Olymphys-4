<?php

namespace App\Entity;
use App\Repository\ElevesinterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileUploader;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\PropertyNamer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\UnicodeString;
/**
 * Memoires
 * @Vich\Uploadable
 * @ORM\Table(name="fichiersequipes")
 * @ORM\Entity(repositoryClass="App\Repository\FichiersequipesRepository")
 * 
 */



class Fichiersequipes //extends BaseMedia
{                   

  
    /**
     * @var int
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
      private $id;
       /**
       *  
       * @ORM\ManyToOne(targetEntity="App\Entity\Edition")
       * @ORM\JoinColumn(name="edition_id",  referencedColumnName="id", nullable=true)
       */
      private $edition;
      /**
       *  
       * @ORM\ManyToOne(targetEntity="App\Entity\Equipesadmin")
       * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id",onDelete="CASCADE" )
       */
      private $equipe;
      
      /**
        * @ORM\Column(type="string", length=255,  nullable=true)
        * @var string
        */
      private $fichier;
     
     
    /**
     *  
     *  @var File 
     *  @Vich\UploadableField(mapping="fichiersequipes", fileNameProperty="fichier")
     *  
     *          
     */
     private $fichierFile;
      /**
       *  
       * @ORM\Column(type="integer", nullable=true)
       * @var int
       */
      private $typefichier;
     
     
       
      
      /**
       *  
       * @ORM\Column(type="boolean", nullable=true)
       * @var boolean
       */
      private $national;
      
    
     /**
       * 
       * 
       * @ORM\Column(type="datetime", nullable=true)
       * @var \DateTime
       */
    private $updatedAt;
    
    /**
       * 
       * 
       * @ORM\OneToOne(targetEntity="App\Entity\Elevesinter")
       * @ORM\JoinColumn(name="eleve_id",  referencedColumnName="id", nullable=true )
       */
    private $eleve;
    
    /**
       * 
       * 
       * @ORM\OneToOne(targetEntity="App\Entity\User")
       * @ORM\JoinColumn(name="user_id",  referencedColumnName="id", nullable=true )
       */
    private $prof;
    
     /**
       * 
       * 
       *@ORM\Column(type="string", length=255,  nullable=true, )
       * @var string
       */
    private $nomautorisation;
    
   
   
    
    
      public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition($edition)
    {
        $this->edition = $edition;
        return $this;
    }
    
    public function getFichierFile()
    {    
            
        return $this->fichierFile;
    }
    
    public function getFichier()
    {    return $this->fichier;
    }
    
    public function setFichier($fichier)
    {   
        $this->fichier = $fichier;
        if ($fichier) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
        if ($this->typefichier==6){
            $citoyen=$this->getEleve();
            if (!$citoyen){
                $citoyen=$this->getProf();              
            }
          
           $citoyen->setAutorisationphotos($this);
            
        }
        return $this;
    }

    public function setFichierFile(File $fichierFile = null)
            
    {  
       
        //$nom=$this->getFichier();
    
        $this->fichierFile=$fichierFile;
       if($this->fichierFile instanceof UploadedFile){
                        $this->updatedAt = new \DateTime('now');
              }
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        //$this->fichier=$nom;
       
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getEquipe()
    {
        return $this->equipe;
    }

    public function setEquipe($equipe)
    {
        $this->equipe = $equipe;
        return $this;
    }
    public function getNomautorisation()
    {
        return $this->nomautorisation;
    }

    public function setNomautorisation($nom)
    {    $nom= $this->code($nom);
        
        $this->nomautorisation = $nom;
        return $this;
    }

    
public function personalNamer()    //permet à easyadmin de renonnmer le fichier, ne peut pas être utilisé directement
 {
           
           $edition=$this->getEdition()->getEd();  
           $equipe=$this->getEquipe();
          
          if ($equipe){
           $lettre=$equipe->getLettre();
           $libel_equipe=$lettre;
           if ($this->getNational()==0){
               
               $libel_equipe=$equipe->getNumero();
           }
           $nom_equipe=$equipe->getTitreProjet();
          $nom_equipe=$this->code($nom_equipe);
           
            //$nom_equipe= str_replace("'","",$nom_equipe);
           //$nom_equipe= str_replace("`","",$nom_equipe);
            
           //$nom_equipe= str_replace("?","",$nom_equipe);
          }
          else{
              $libel_equipe='prof';
              
          }
       if ($this->getTypefichier()==0)
               {
                $fileName=$edition.'-eq-'.$libel_equipe.'-memoire-'.$nom_equipe;
                }
         if ($this->getTypefichier()==1)
               
           {
               $fileName=$edition.'-eq-'.$libel_equipe.'-Annexe';
           }
          if ($this->getTypefichier()==2){
               $fileName=$edition.'-eq-'.$libel_equipe.'-Resume-'.$nom_equipe;
              
          }
           if ($this->getTypefichier()==4){
                $fileName=$edition.'-eq-'.$libel_equipe.'-Fichesecur-'.$nom_equipe;
                }  
                
            if ($this->getTypefichier()==3){
            $fileName=$edition.'-eq-'.$libel_equipe.'-Presentation-'.$nom_equipe;
            }
           
            if ($this->getTypefichier()==5){
            $fileName=$edition.'-eq-'.$libel_equipe.'-diaporama-'.$nom_equipe;
            }
          
            if ($this->getTypefichier()==6){
              $nom=$this->getNomautorisation();
                 
            
            $fileName=$edition.'-eq-'.$libel_equipe.'-autorisation photos-'.$nom.'-'.uniqid();
            }
             if ($this->getTypefichier()==7){
                           
            
            $fileName=$edition.'-eq-'.$libel_equipe.'-questionnaire equipe-'.$nom_equipe.'-'.uniqid();
            }
           return $fileName;
 }
    
    public function code($nom){
           $nom= str_replace("à","a",$nom);
           $nom= str_replace("ù","u",$nom);
           $nom= str_replace("è","e",$nom);
           $nom= str_replace("é","e",$nom);
           $nom= str_replace("ë","e",$nom);
           $nom= str_replace("ê","e",$nom);
           $nom= str_replace("?"," ",$nom);
           $nom= str_replace("ï","i",$nom);
           $nom= str_replace(":","_",$nom);
            setLocale(LC_CTYPE,'fr_FR');
           $nom = iconv('UTF-8','ASCII//TRANSLIT',$nom);
        
        
        return $nom;
    }
 



   /**
    * Updates the hash value to force the preUpdate and postUpdate events to fire.
    */
   public function refreshUpdated()
   {
      $this->setUpdated(new \DateTime());
   }
    
        
   public function setUpdated($date)
   {
      $this->updated = $date;

        return $this;
   }
   

   public function getUpdatedAt()
   {
       return $this->updatedAt;
   }
    public function getUpdatedannexeAt()
   {
       return $this->updatedannexeAt;
   }
   
   
   public function getTypefichier()
   {
       return $this->typefichier;
   }
    public function setTypefichier($typefichier)
   {
      $this->typefichier=$typefichier;
   }
   public function getNational()
   {
       return $this->national;
   }
    public function setNational($national)
   {  
      $this->national=$national;
     
      return $this;
   }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function directoryName(): string
     {  
        if (($this->getTypefichier()==0) or ($this->getTypefichier()==1)){
             $path= '/memoires/';
         }
         
          if ($this->getTypefichier()==2){
             $path= '/resumes/';
         }
          if ($this->getTypefichier()==4){
             $path= '/fichessecur/';
         }
          if ($this->getTypefichier()==3){
             $path= '/presentation/';
         }
         
          if ($this->getTypefichier()==5){
             $path= '/diaporamas/';
         }
          if ($this->getTypefichier()==6){
             $path= '/autorisations/';
         }
          if ($this->getTypefichier()==7){
             $path= '/questionnaires/';
         }
          return $path;
         
     }
     
     public function getEleve()
   {
       return $this->eleve;
   }
    public function setEleve($eleve)
   {
      $this->eleve=$eleve;
   }
    public function getProf()
   {
       return $this->prof;
   }
    public function setProf($prof)
   {
      $this->prof=$prof;
   } 
     
     
     
      public function getInfoequipenat()
    {   
        if ($this->getEquipe()->getSelectionnee()==TRUE){
         
        $lettre=$this->getEquipe()->getLettre();
        
        if ($lettre) 
        {
        $Lettre=$this->getEquipe()->getLettre();
        
        $nom_equipe=$this->getEquipe()->getTitreProjet() ;
        $ville=$this->getEquipe()->getRneId()->getCommune();
        
        $infoequipe= 'Eq '.$Lettre.' - '.$nom_equipe.'-'.$ville;   
        }   
        }
        if (isset($infoequipe)){
        return $infoequipe;
        }
    }
    
     
}

