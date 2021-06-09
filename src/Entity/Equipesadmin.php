<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Service\FileUploader;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * equipesadmin
 * @Vich\Uploadable
 * @ORM\Table(name="equipesadmin")
 * @ORM\Entity(repositoryClass="App\Repository\EquipesadminRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Equipesadmin
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
     * @var string
     *
     * @ORM\Column(name="lettre", type="string", length=1, nullable= true)
     */
    private $lettre;
     
     /**
     * @var int
     *
     * @ORM\Column(name="numero", type="smallint", nullable=true)
     */
    private $numero;
          
 
    
    /**
     * @var boolean
     * @ORM\Column(name="selectionnee", type="boolean", nullable=true)
     */
    private $selectionnee;

    /**
     * @var string
     *
     * @ORM\Column(name="titreProjet", type="string", length=255, nullable=true)
     */
    private $titreProjet;

   /**
     * @var string
     *
     * @ORM\Column(name="nom_lycee", type="string", length=255, nullable=true)
     */
    private $nomLycee;

    /**
     * @var string
     *
     * @ORM\Column(name="denomination_lycee", type="string", length=255, nullable=true)
     */
    private $denominationLycee;

    /**
     * @var string
     *
     * @ORM\Column(name="lycee_localite", type="string", length=255, nullable=true)
     */
    private $lyceeLocalite;

    /**
     * @var string
     *
     * @ORM\Column(name="lycee_academie", type="string", length=255, nullable=true)
     */
    private $lyceeAcademie;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_prof1", type="string", length=255, nullable=true)
     */
    private $prenomProf1;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_prof1", type="string", length=255, nullable=true)
     */
    private $nomProf1;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_prof2", type="string", length=255, nullable=true)
     */
    private $prenomProf2;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_prof2", type="string", length=255, nullable=true)
     */
    private $nomProf2; 
     /**
     * @var string
     *
     * @ORM\Column(name="rne", type="string", length=255, nullable=true)
     */
    
    private $rne; 
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rne")
     * 
     */
    private $rneId;

   
    
  

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Centrescia")
     */
    private $centre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Edition"))
     */
    private $edition;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contribfinance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $origineprojet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recompense;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $partenaire;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

  

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $idProf1;

    /**
     * @ORM\ManyToOne(targetEntity=user::class)
     */
    private $idProf2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $inscrite = 1;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbeleves;




    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titreProjetinter
     *
     * @param string $titreProjetinter
     *
     * @return Equipesadmin
     */
    public function setTitreProjet($titreProjet)
    {
       
        $this->createdAt= new \DateTime('now');
        $this->titreProjet = $titreProjet;

        return $this;
    }

    /**
     * Get titreProjetinter
     *
     * @return string
     */
    public function getTitreProjet()
    {
        return $this->titreProjet;
    }

        /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Equipesadmin
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }
    
     /**
     * Set lettre
     *
     * @param string $lettre
     *
     * @return Equipesadmin
     */
    public function setLettre($lettre)
    {
        $this->lettre = $lettre;

        return $this;
    }

    /**
     * Get lettre
     *
     * @return string
     */
    public function getLettre()
    {
        return $this->lettre;
    }
      
    
    public function getInfoequipe()
    {   
        $nomcentre='';
        $Numero=$this->getNumero();
        
        $edition=$this->getEdition();
        If ($centre =$this->getCentre()){
        $nomcentre =$this->getCentre()->getCentre().'-';}
               
        
        $nom_equipe=$this->getTitreProjet() ;
        $ville=$this->getLyceeLocalite();
        
        $infoequipe= $edition->getEd().'-'.$nomcentre.'Eq '.$Numero.' - '.$nom_equipe.'-'.$ville;        
        return $infoequipe;
    }
    public function getInfoequipenat()
    {   
    $edition=$this->getEdition();
        if ($this->getSelectionnee()=='1'){
          
        $lettre=$this->getLettre();
        
       
        
        $nom_equipe=$this->getTitreProjet() ;
        $infoequipe=$lettre.' - '.$nom_equipe;
        if ($this->getRneId()){
        $infoequipe=$infoequipe.'-'.$this->getRneId()->getCommune();
        }
       
        
       
        
        return $infoequipe;
        
        }
    }
    

    

   
     
  
    public function getSelectionnee()
    {
        return $this->selectionnee;
    }

    public function setSelectionnee($selectionnee)
    {
        $this->selectionnee = $selectionnee;

        return $this;
    }
    /**
     * Set nomLycee
     *
     * @param string $nomLycee
     *
     * @return Equipesadmin
     */
    public function setNomLycee($nomLycee)
    {
        $this->nomLycee = $nomLycee;

        return $this;
    }

    /**
     * Get nomLycee
     *
     * @return string
     */
    public function getNomLycee()
    {
        return $this->nomLycee;
    }

    /**
     * Set denominationLycee
     *
     * @param string $denominationLycee
     *
     * @return Equipesadmin
     */
    public function setDenominationLycee($denominationLycee)
    {
        $this->denominationLycee = $denominationLycee;

        return $this;
    }

    /**
     * Get denominationLycee
     *
     * @return string
     */
    public function getDenominationLycee()
    {
        return $this->denominationLycee;
    }

    /**
     * Set lyceeLocalite
     *
     * @param string $lyceeLocalite
     *
     * @return Equipesadmin
     */
    public function setLyceeLocalite($lyceeLocalite)
    {
        $this->lyceeLocalite = $lyceeLocalite;

        return $this;
    }

    /**
     * Get lyceeLocalite
     *
     * @return string
     */
    public function getLyceeLocalite()
    {
        return $this->lyceeLocalite;
    }

    /**
     * Set lyceeAcademie
     *
     * @param string $lyceeAcademie
     *
     * @return Equipesadmin
     */
    public function setLyceeAcademie($lyceeAcademie)
    {
        $this->lyceeAcademie = $lyceeAcademie;

        return $this;
    }

    /**
     * Get lyceeAcademie
     *
     * @return string
     */
    public function getLyceeAcademie()
    {
        return $this->lyceeAcademie;
    }

    /**
     * Set prenomProf1
     *
     * @param string $prenomProf1
     *
     * @return Equipesadmin
     */
    public function setPrenomProf1($prenomProf1)
    {
        $this->prenomProf1 = $prenomProf1;

        return $this;
    }

    /**
     * Get prenomProf1
     *
     * @return string
     */
    public function getPrenomProf1()
    {
        return $this->prenomProf1;
    }

    /**
     * Set nomProf1
     *
     * @param string $nomProf1
     *
     * @return Equipesadmin
     */
    public function setNomProf1($nomProf1)
    {
        $this->nomProf1 = $nomProf1;

        return $this;
    }

    /**
     * Get nomProf1
     *
     * @return string
     */
    public function getNomProf1()
    {
        return $this->nomProf1;
    }

    /**
     * Set prenomProf2
     *
     * @param string $prenomProf2
     *
     * @return Equipesadmin
     */
    public function setPrenomProf2($prenomProf2)
    {
        $this->prenomProf2 = $prenomProf2;

        return $this;
    }

    /**
     * Get prenomProf2
     *
     * @return string
     */
    public function getPrenomProf2()
    {
        return $this->prenomProf2;
    }

    /**
     * Set nomProf2
     *
     * @param string $nomProf2
     *
     * @return Equipesadmin
     */
    public function setNomProf2($nomProf2)
    {
        $this->nomProf2 = $nomProf2;

        return $this;
    }

    /**
     * Get nomProf2
     *
     * @return string
     */
    public function getNomProf2()
    {
        return $this->nomProf2;
    }
    /**
     * Get rne
     *
     * @return string
     */
    public function getRne()
    {
       return  $this->rne;
    }
    /**
     * Set rne
     *
     * @param string rne
     *
     * @return Equipesadmin
     */
    public function setRne($rne)
    {
        $this->rne=$rne;
        return $this;
    }
    
     /**
     * Get rneId
     *
     * 
     */
    public function getRneId()
    {
       return  $this->rneId;
    }
    /**
     * Set rne
     *
     * 
     *
     * @return Equipesadmin
     */
    public function setRneId($rne_id)
    {
        $this->rneId=$rne_id;
        return $this;
    }
    
   public function getLycee()
   {
       return $this->getNomLycee().' de  '.$this->getLyceeLocalite();
   } 
   public function getProf1()
   {
       
       return $this->getPrenomProf1().' '.$this->getNomProf1();
   }
   public function getProf2()
   {
       
       return $this->getPrenomProf2().' '.$this->getNomProf2();
   }



  
   
   

   public function getCentre(): ?centrescia
   {
       return $this->centre;
   }

   public function setCentre(?centrescia $centre): self
   {
       $this->centre = $centre;

       return $this;
   }

   public function getEdition(): ?edition
   {
       return $this->edition;
   }

   public function setEdition(?edition $edition)
   {
       $this->edition = $edition;

       return $this;
   }

   public function getContribfinance(): ?string
   {
       return $this->contribfinance;
   }

   public function setContribfinance(?string $contribfinance): self
   {
       $this->contribfinance = $contribfinance;

       return $this;
   }

   public function getOrigineprojet(): ?string
   {
       return $this->origineprojet;
   }

   public function setOrigineprojet(?string $origineprojet): self
   {
       $this->origineprojet = $origineprojet;

       return $this;
   }

   public function getRecompense(): ?string
   {
       return $this->recompense;
   }

   public function setRecompense(?string $recompense): self
   {
       $this->recompense = $recompense;

       return $this;
   }

   public function getPartenaire(): ?string
   {
       return $this->partenaire;
   }

   public function setPartenaire(?string $partenaire): self
   {
       $this->partenaire = $partenaire;

       return $this;
   }

   public function getCreatedAt(): ?\DateTimeInterface
   {
       return $this->createdAt;
   }

   public function setCreatedAt(\DateTimeInterface $createdAt): self
   {
       $this->createdAt = $createdAt;

       return $this;
   }


   public function getIdProf1(): ?user
   {
       return $this->idProf1;
   }

   public function setIdProf1(?user $idProf1): self
   {
       $this->idProf1 = $idProf1;

       return $this;
   }

   public function getIdProf2(): ?user
   {
       return $this->idProf2;
   }

   public function setIdProf2(?user $idProf2): self
   {
       $this->idProf2 = $idProf2;

       return $this;
   }

   public function getDescription(): ?string
   {
       return $this->description;
   }

   public function setDescription(?string $description): self
   {
       $this->description = $description;

       return $this;
   }

   public function getInscrite(): ?bool
   {
       return $this->inscrite;
   }

   public function setInscrite(bool $inscrite): self
   {
       $this->inscrite = $inscrite;

       return $this;
   }

   public function getNbeleves(): ?int
   {
       return $this->nbeleves;
   }

   public function setNbeleves(int $nbeleves): self
   {
       $this->nbeleves = $nbeleves;

       return $this;
   }







}
