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
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="lettre", type="string", length=255, unique=true)
     */
    private string $lettre;

    /**
     * @var string
     *
     * @ORM\Column(name="titreProjet", type="string", length=255, unique=true)
     */
    private string $titreProjet;
    
     /**
     * @var int
     *
     * @ORM\Column(name="ordre", type="smallint",nullable=true)
     */
    private int $ordre;
    
    /**
     * @var string
     *
     * @ORM\Column(name="heure", type="string", length=255, nullable=true)
     */
    private string $heure;
    
     /**
     * @var string
     *
     * @ORM\Column(name="salle", type="string", length=255, nullable=true)
     */
    private string $salle;
    
     /**
     * @var int
     *
     * @ORM\Column(name="total", type="smallint", nullable=true)
     */
    private int $total;

    /**
     * @var string
     * @ORM\Column(name="classement", type="string", length=255, nullable=true)
     */
    private string $classement;

    /**
     * @var int
     * @ORM\Column(name="rang", type="smallint", nullable=true)
     */
    private int $rang;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Visites",cascade={"persist"})
     * @ORM\JoinColumn(name="visite_id", nullable=true)
     */
    private Visites $visite;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Cadeaux", cascade={"persist"})
     */
    private Cadeaux $cadeau;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Phrases", cascade={"persist"})
     */
    private Phrases $phrases;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Liaison", cascade={"persist"})
     */
    private Liaison $liaison;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $prix;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Equipesadmin", cascade={"persist"})
     */
    private Equipesadmin $infoequipe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Eleves", mappedBy="lettreEquipe")
     */
    private ArrayCollection $eleves;  // notez le "s" : une equipe est liée à plusieurs eleves.

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notes", mappedBy="equipe")
     */
    private ArrayCollection $notess;  // notez le "s" : une equipe est liée à plusieurs lignes de "notes".

    /**
     * @ORM\Column(name="nb_notes", type="integer")
     */
    private int $nbNotes=0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $sallesecours;

    /**
     * @ORM\ManyToOne(targetEntity=user::class)
     */
    private ?user $hote;

    /**
     * @ORM\ManyToOne(targetEntity=user::class)
     */
    private ?user $interlocuteur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $code;

    /**
     * @ORM\ManyToOne(targetEntity=user::class)
     */
    private ?user $observateur;
  
    
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

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set lettre
     *
     * @param string $lettre
     *
     * @return Equipes
     */
    public function setLettre(string $lettre): Equipes
    {
        $this->lettre = $lettre;

        return $this;
    }

    /**
     * Get lettre
     *
     * @return string
     */
    public function getLettre(): string
    {
        return $this->lettre;
    }

    /**
     * Set titreProjet
     *
     * @param string $titreProjet
     *
     * @return Equipes
     */
    public function setTitreProjet(string $titreProjet): Equipes
    {
        $this->titreProjet = $titreProjet;

        return $this;
    }

    /**
     * Get titreProjet
     *
     * @return string
     */
    public function getTitreProjet(): string
    {
        return $this->titreProjet;
    }

    /**
     * Set ordre
     *
     * @param string $ordre
     *
     * @return Equipes
     */
    public function setOrdre(string $ordre): Equipes
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return int
     */
    public function getOrdre(): int
    {
        return $this->ordre;
    }

    /**
     * Set heure
     *
     * @param string $heure
     *
     * @return Equipes
     */
    public function setHeure(string $heure): Equipes
    {
        $this->heure = $heure;

        return $this;
    }

    /**
     * Get heure
     *
     * @return string
     */
    public function getHeure()
    {
        return $this->heure;
    }
    
        /**
     * Set salle
     *
     * @param string $salle
     *
     * @return Equipes
     */
    public function setSalle($salle)
    {
        $this->salle = $salle;

        return $this;
    }

    /**
     * Get salle
     *
     * @return string
     */
    public function getSalle()
    {
        return $this->salle;
    }
    
    /**
     * Set visite
     *
     * @param Visites $visite
     *
     * @return Equipes
     */
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

    /**
     * Get visite
     *
     * @return Visites
     */
    public function getVisite(): Visites
    {
        return $this->visite;
    }

    /**
     * Add notess
     *
     * @param Notes $notess
     *
     * @return Equipes
     */
    public function addNotess(Notes $notess): Equipes
    {
        $this->notess[] = $notess;

        //On relie l'équipe à "une ligne note"
        $notess->setEquipe($this);

        return $this;
    }

    /**
     * Remove notess
     *
     * @param Notes $notess
     */
    public function removeNotess(Notes $notess)
    {
        $this->notess->removeElement($notess);
    }

    /**
     * Get notess
     *
     * @return Collection
     */
    public function getNotess()
    {
        return $this->notess;
    }

    /**
     * Set nbNotes
     *
     * @param integer $nbNotes
     *
     * @return Equipes
     */
    public function setNbNotes(int $nbNotes): Equipes
    {
        $this->nbNotes = $nbNotes;

        return $this;
    }

    /**
     * Get nbNotes
     *
     * @return integer
     */
    public function getNbNotes(): int
    {
        return $this->nbNotes;
    }

    /**
     * Set cadeau
     *
     * @param Cadeaux|null $cadeau
     *
     * @return Equipes
     */
    public function setCadeau(Cadeaux $cadeau = null): Equipes
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

    /**
     * Get cadeau
     *
     * @return Cadeaux
     */
    public function getCadeau(): Cadeaux
    {
        return $this->cadeau;
    }

    /**
     * Set phrases
     *
     * @param Phrases|null $phrases
     *
     * @return Equipes
     */
    public function setPhrases(Phrases $phrases = null): Equipes
    {
        $this->phrases = $phrases;

        return $this;
    }

    /**
     * Get phrases
     *
     * @return Phrases
     */
    public function getPhrases(): Phrases
    {
        return $this->phrases;
    }

    /**
     * Set liaison
     *
     * @param Liaison|null $liaison
     *
     * @return Phrases
     */
    public function setLiaison(Liaison $liaison = null): Phrases
    {
    $this->liaison = $liaison;

    return $this;
    }

    /**
    * Get liaison
    *
    * @return Liaison
    */
    public function getLiaison(): Liaison
    {
    return $this->liaison;
    }

    /**
     * Set total
     *
     * @param integer $total
     *
     * @return Equipes
     */
    public function setTotal(int $total): Equipes
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Set classement
     *
     * @param integer $classement
     *
     * @return Equipes
     */
    public function setClassement(int $classement): Equipes
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Get classement
     *
     * @return string
     */
    public function getClassement()
    {
        return $this->classement;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return Equipes
     */
    public function setRang(int $rang): Equipes
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer
     */
    public function getRang(): int
    {
        return $this->rang;
    }

    /**
     * Set prix
     *
     * @param Prix|null $prix
     *
     * @return Equipes
     */
    public function setPrix(Prix $prix = null)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getPrix(): Prix
    {
        return $this->prix;
    }

    /**
     * Set infoequipe
     *
     * @param Equipesadmin|null $infoequipe
     *
     * @return Equipes
     */
    public function setInfoequipe(Equipesadmin $infoequipe = null): Equipes
    {
        $this->infoequipe = $infoequipe;

        return $this;
    }

    /**
     * Get infoequipe
     *
     * @return Equipesadmin
     */
    public function getInfoequipe(): Equipesadmin
    {
        return $this->infoequipe;
    }

    /**
     * @return Collection|Eleves[]
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addEleve(Eleves $eleve): self
    {
        if (!$this->eleves->contains($eleve)) {
            $this->eleves[] = $eleve;
            $eleve->setEquipeleves($this);
        }

        return $this;
    }

    public function removeEleve(Eleves $eleve): self
    {
        if ($this->eleves->contains($eleve)) {
            $this->eleves->removeElement($eleve);
            // set the owning side to null (unless already changed)
            if ($eleve->getEquipeleves() === $this) {
                $eleve->setEquipeleves(null);
            }
        }

        return $this;
    }

    

    public function addElefe(Eleves $elefe): self
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves[] = $elefe;
            $elefe->setLettreEquipe($this);
        }

        return $this;
    }

    public function removeElefe(Eleves $elefe): self
    {
        if ($this->eleves->contains($elefe)) {
            $this->eleves->removeElement($elefe);
            // set the owning side to null (unless already changed)
            if ($elefe->getLettreEquipe() === $this) {
                $elefe->setLettreEquipe(null);
            }
        }

        return $this;
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
}
