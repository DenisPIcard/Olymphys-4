<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Equipes
 * @Vich\Uploadable
 * @ORM\Table(name="equipes")
 * @ORM\Entity(repositoryClass="App\Repository\EquipesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Equipes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id=0;


    
     /**
     * @var int
     *
     * @ORM\Column(name="ordre", type="smallint",nullable=true)
     */
    private ?int $ordre=null;
    
    /**
     * @var string
     *
     * @ORM\Column(name="heure", type="string", length=255, nullable=true)
     */
    private ?string $heure=null;
    
     /**
     * @var string
     *
     * @ORM\Column(name="salle", type="string", length=255, nullable=true)
     */
    private ?string $salle=null;
    
     /**
     * @var int
     *
     * @ORM\Column(name="total", type="smallint", nullable=true)
     */
    private ?int $total=null;

    /**
     * @var string
     * @ORM\Column(name="classement", type="string", length=255, nullable=true)
     */
    private ?string $classement=null;

    /**
     * @var int
     * @ORM\Column(name="rang", type="smallint", nullable=true)
     */
    private ?int $rang=null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Visites")
     * @ORM\JoinColumn(name="visite_id", nullable=true)
     */
    private ?Visites $visite=null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Cadeaux")
     * @ORM\JoinColumn(name="cadeau_id", nullable=true)
     */
    private ?Cadeaux $cadeau=null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Phrases")
     * @ORM\JoinColumn(name="phrases_id", nullable=true)
     */
    private ?Phrases $phrases=null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Liaison")
     * @ORM\JoinColumn(name="liaison_id", nullable=true)
     */
    private ?Liaison $liaison=null;

    /**
     * @ORM\ManyToOne(targetEntity=Prix::class)
     * @ORM\JoinColumn(name="prix_id", nullable=true)
     */
    private ?Prix $prix=null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Equipesadmin")
     * @ORM\JoinColumn(name="equipeinter_id", nullable=true)
     */
    private ?Equipesadmin $equipeinter=null;

     // notez le "s" : une equipe est liée à plusieurs eleves.

     // notez le "s" : une equipe est liée à plusieurs lignes de "notes".

    /**
     * @ORM\Column(name="nb_notes", type="integer")
     */
    private int $nbNotes=0;



    /**
     * @ORM\ManyToOne(targetEntity=user::class)
     */
    private ?user $observateur=null;

    /**
     * @ORM\OneToMany(targetEntity=Notes::class, mappedBy="equipe")
     *
     */
    private  $notess;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $salleZoom=null;
  
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notess = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        
    }
    

   public function increaseNbNotes()
   {
       $this->nbNotes++;
   }


    public function getId(): int
    {
        return $this->id;
    }




    

    public function setSalle($salle)
    {
        $this->salle = $salle;

        return $this;
    }


    public function getSalle()
    {
        return $this->salle;
    }
    

    public function setVisite(Visites $visite = null): Equipes
    {    $visiteini=$this->visite; 
         if ($visite != null){
            $visite->setAttribue(true);
           
        }
        else{
            
            if($visiteini!=null){
            $visiteini->setAttribue(false);}
        }
        $this->visite = $visite;

        return $this;
    }


    public function getVisite()
    {
        return $this->visite;
    }


    public function addNotess(Notes $notess): Equipes
    {
        $this->notess[] = $notess;

        //On relie l'équipe à "une ligne note"
        $notess->setEquipe($this);

        return $this;
    }


    public function removeNotess(Notes $notess)
    {
        $this->notess->removeElement($notess);
    }


    public function getNotess()
    {
        return $this->notess;
    }


    public function setNbNotes(int $nbNotes)
    {
        $this->nbNotes = $nbNotes;

        return $this;
    }


    public function getNbNotes(): int
    {
        return $this->nbNotes;
    }


    public function setCadeau(Cadeaux $cadeau = null)
    {      
        $cadeauini=$this->cadeau;
        if ($cadeau != null){
            $cadeau->setAttribue(true);
           
        }
        else{
        if ($cadeauini!=null){
            $cadeauini->setAttribue(false);
        }
        }
        $this->cadeau = $cadeau;

        return $this;
    }


    public function getCadeau()
    {
        return $this->cadeau;
    }


    public function setPhrases(Phrases $phrases = null)
    {
        $this->phrases = $phrases;

        return $this;
    }


    public function getPhrases()
    {
        return $this->phrases;
    }


    public function setLiaison(Liaison $liaison = null)
    {
    $this->liaison = $liaison;


    }


    public function getLiaison()
    {
    return $this->liaison;
    }


    public function setTotal(int $total)
    {
        $this->total = $total;

        return $this;
    }


    public function getTotal(): int
    {
        return $this->total;
    }


    public function setClassement(string $classement)
    {
        $this->classement = $classement;

        return $this;
    }


    public function getClassement()
    {
        return $this->classement;
    }


    public function setRang(int $rang): Equipes
    {
        $this->rang = $rang;

        return $this;
    }


    public function getRang(): int
    {
        return $this->rang;
    }


    public function setPrix( $prix)
    {
        $this->prix = $prix;


    }


    public function getPrix()
    {
        return $this->prix;
    }


    public function setEquipeinter( $equipeinter)
    {
        $this->equipeinter = $equipeinter;

    }


    public function getEquipeinter(): Equipesadmin
    {
        return $this->equipeinter;
    }

   public function getClassementEquipe(): string
   {
       $string=$this->classement.' prix'.' : '.$this->lettre.' - '.$this->infoequipe->getTitreProjet().' '.$this->infoequipe->getLyceeLocalite();
       
       Return $string;
       
       
   }

   public function getSallesecours(): ?string
   {
       return $this->sallesecours;
   }

   public function setSallesecours(?string $sallesecours): self
   {
       $this->sallesecours = $sallesecours;

       return $this;
   }

   public function getHote(): ?user
   {
       return $this->hote;
   }

   public function setHote(?user $hote): self
   {
       $this->hote = $hote;

       return $this;
   }

   public function getInterlocuteur(): ?user
   {
       return $this->interlocuteur;
   }

   public function setInterlocuteur(?user $interlocuteur): self
   {
       $this->interlocuteur = $interlocuteur;

       return $this;
   }

   public function getCode(): ?string
   {
       return $this->code;
   }

   public function setCode(?string $code): self
   {
       $this->code = $code;

       return $this;
   }

   public function getObservateur(): ?user
   {
       return $this->observateur;
   }

   public function setObservateur(?user $observateur): self
   {
       $this->observateur = $observateur;

       return $this;
   }

   public function getOrdre()
   {
       return $this->ordre;
   }

   public function setOrdre(?int $ordre): self
   {
       $this->ordre = $ordre;

       return $this;
   }

   public function getHeure(): ?string
   {
       return $this->heure;
   }

   public function setHeure(?string $heure): self
   {
       $this->heure = $heure;

       return $this;
   }

   public function getSalleZoom(): ?string
   {
       return $this->salleZoom;
   }

   public function setSalleZoom(?string $salleZoom): self
   {
       $this->salleZoom = $salleZoom;

       return $this;
   }
}
