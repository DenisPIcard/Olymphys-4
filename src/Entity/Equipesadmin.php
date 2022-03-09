<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\Column(type="boolean")
     */
    protected int $inscrite = 1;
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(name="lettre", type="string", length=1, nullable= true)
     */
    private ?string $lettre;
    /**
     * @ORM\Column(name="numero", type="smallint", nullable=true)
     */
    private ?int $numero = 0;
    /**
     * @var boolean
     * @ORM\Column(name="selectionnee", type="boolean", nullable=true)
     */
    private ?bool $selectionnee = false;
    /**
     * @ORM\Column(name="titreProjet", type="string", length=255, nullable=true)
     */
    private ?string $titreProjet;
    /**
     * @ORM\Column(name="nom_lycee", type="string", length=255, nullable=true)
     */
    private ?string $nomLycee;
    /**
     * @ORM\Column(name="denomination_lycee", type="string", length=255, nullable=true)
     */
    private ?string $denominationLycee;
    /**
     * @ORM\Column(name="lycee_localite", type="string", length=255, nullable=true)
     */
    private ?string $lyceeLocalite;
    /**
     * @ORM\Column(name="lycee_academie", type="string", length=255, nullable=true)
     */
    private ?string $lyceeAcademie;
    /**
     * @ORM\Column(name="prenom_prof1", type="string", length=255, nullable=true)
     */
    private ?string $prenomProf1;
    /**
     * @ORM\Column(name="nom_prof1", type="string", length=255, nullable=true)
     */
    private ?string $nomProf1;
    /**
     * @ORM\Column(name="prenom_prof2", type="string", length=255, nullable=true)
     */
    private ?string $prenomProf2;
    /**
     * @ORM\Column(name="nom_prof2", type="string", length=255, nullable=true)
     */
    private ?string $nomProf2;
    /**
     * @ORM\Column(name="rne", type="string", length=255, nullable=true)
     */
    private ?string $rne;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rne")
     */
    private ?rne $rneId;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Centrescia")
     */
    private ?centrescia $centre;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Edition"))
     */
    private ?edition $edition;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $contribfinance = null;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $origineprojet = null;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $recompense = null;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $partenaire = null;
    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $createdAt = null;
    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?user $idProf1;
    /**
     * @ORM\ManyToOne(targetEntity=user::class)
     */
    private ?user $idProf2;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;
    /**
     * @ORM\Column(type="integer")
     */
    private ?int $nbeleves = null;

    /**
     * @ORM\ManyToMany(targetEntity=Professeurs::class, mappedBy="equipes")
     */
    private Collection $equipesstring;

    /**
     * @ORM\OneToMany(targetEntity=Phrases::class, mappedBy="equipe")
     */
    private $phrases;

    public function __construct()
    {
        $this->equipesstring = new ArrayCollection();
        $this->phrases = new ArrayCollection();
    }

    public function __toString()
    {

        if ($this->lettre != null) {
            return $this->edition->getEd() . '-' . $this->lettre . '-' . $this->titreProjet;
        } else {
            return $this->edition->getEd() . '-' . $this->numero . '-' . $this->titreProjet;
        }
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

    public function getInfoequipe(): ?string
    {
        $nomcentre = '';
        $Numero = $this->getNumero();

        $edition = $this->getEdition();
        if ($centre = $this->getCentre()) {
            $nomcentre = $this->getCentre()->getCentre() . '-';
        }


        $nom_equipe = $this->getTitreProjet();
        $ville = $this->getLyceeLocalite();

        return $edition->getEd() . '-' . $nomcentre . 'Eq ' . $Numero . ' - ' . $nom_equipe . '-' . $ville;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero(): ?int
    {
        return $this->numero;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Equipesadmin
     */
    public function setNumero(int $numero): Equipesadmin
    {
        $this->numero = $numero;

        return $this;
    }

    public function getEdition(): ?edition
    {
        return $this->edition;
    }

    public function setEdition(?edition $edition): Equipesadmin
    {
        $this->edition = $edition;

        return $this;
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

    /**
     * Get titreProjetinter
     *
     * @return string
     */
    public function getTitreProjet(): ?string
    {
        return $this->titreProjet;
    }

    /**
     * Set titreProjetinter
     *
     * @param $titreProjet
     * @return Equipesadmin
     */
    public function setTitreProjet($titreProjet): Equipesadmin
    {

        $this->createdAt = new DateTime('now');
        $this->titreProjet = $titreProjet;

        return $this;
    }

    /**
     * Get lyceeLocalite
     *
     * @return string
     */
    public function getLyceeLocalite(): ?string
    {
        return $this->lyceeLocalite;
    }

    /**
     * Set lyceeLocalite
     *
     * @param string $lyceeLocalite
     *
     * @return Equipesadmin
     */
    public function setLyceeLocalite(string $lyceeLocalite): Equipesadmin
    {
        $this->lyceeLocalite = $lyceeLocalite;

        return $this;
    }

    public function getInfoequipenat()
    {
        $edition = $this->getEdition();
        if ($this->getSelectionnee() == '1') {

            $lettre = $this->getLettre();


            $nom_equipe = $this->getTitreProjet();
            $infoequipe = $lettre . ' - ' . $nom_equipe;
            if ($this->getRneId()) {
                $infoequipe = $infoequipe . '-' . $this->getRneId()->getCommune();
            }


            return $infoequipe;

        }
    }

    public function getSelectionnee(): ?bool
    {
        return $this->selectionnee;
    }

    public function setSelectionnee($selectionnee): Equipesadmin
    {
        $this->selectionnee = $selectionnee;

        return $this;
    }

    /**
     * Get lettre
     *
     * @return string
     */
    public function getLettre(): ?string
    {
        return $this->lettre;
    }

    /**
     * Set lettre
     *
     * @return Equipesadmin
     */
    public function setLettre(?string $lettre): ?Equipesadmin
    {
        $this->lettre = $lettre;

        return $this;
    }

    /**
     * Get rneId
     *
     *
     */
    public function getRneId(): ?rne
    {
        return $this->rneId;
    }

    /**
     * Set rne
     *
     *
     *
     * @param $rne_id
     * @return Equipesadmin
     */
    public function setRneId($rne_id): ?Equipesadmin
    {
        $this->rneId = $rne_id;
        return $this;
    }

    /**
     * Get denominationLycee
     *
     * @return string
     */
    public function getDenominationLycee(): ?string
    {
        return $this->denominationLycee;
    }

    /**
     * Set denominationLycee
     *
     * @param string $denominationLycee
     *
     * @return Equipesadmin
     */
    public function setDenominationLycee(string $denominationLycee): Equipesadmin
    {
        $this->denominationLycee = $denominationLycee;

        return $this;
    }

    /**
     * Get lyceeAcademie
     *
     * @return string
     */
    public function getLyceeAcademie(): ?string
    {
        return $this->lyceeAcademie;
    }

    /**
     * Set lyceeAcademie
     *
     * @param string $lyceeAcademie
     *
     * @return Equipesadmin
     */
    public function setLyceeAcademie(string $lyceeAcademie): Equipesadmin
    {
        $this->lyceeAcademie = $lyceeAcademie;

        return $this;
    }

    /**
     * Get rne
     *
     * @return string
     */
    public function getRne(): ?string
    {
        return $this->rne;
    }

    /**
     * Set rne
     *
     * @param string rne
     *
     * @return Equipesadmin
     */
    public function setRne($rne): Equipesadmin
    {
        $this->rne = $rne;
        return $this;
    }

    public function getLycee(): ?string
    {
        return $this->getNomLycee() . ' de  ' . $this->getLyceeLocalite();
    }

    /**
     * Get nomLycee
     *
     * @return string
     */
    public function getNomLycee(): ?string
    {
        return $this->nomLycee;
    }

    /**
     * Set nomLycee
     *
     * @param string $nomLycee
     *
     * @return Equipesadmin
     */
    public function setNomLycee(string $nomLycee): Equipesadmin
    {
        $this->nomLycee = $nomLycee;

        return $this;
    }

    public function getProf1(): ?string
    {

        return $this->getPrenomProf1() . ' ' . $this->getNomProf1();
    }

    /**
     * Get prenomProf1
     *
     * @return string
     */
    public function getPrenomProf1(): ?string
    {
        return $this->prenomProf1;
    }

    /**
     * Set prenomProf1
     *
     * @param string $prenomProf1
     *
     * @return Equipesadmin
     */
    public function setPrenomProf1(string $prenomProf1): Equipesadmin
    {
        $this->prenomProf1 = $prenomProf1;

        return $this;
    }

    /**
     * Get nomProf1
     *
     * @return string
     */
    public function getNomProf1(): ?string
    {
        return $this->nomProf1;
    }

    /**
     * Set nomProf1
     *
     * @param string $nomProf1
     *
     * @return Equipesadmin
     */
    public function setNomProf1(string $nomProf1): Equipesadmin
    {
        $this->nomProf1 = $nomProf1;

        return $this;
    }

    public function getProf2(): ?string
    {

        return $this->getPrenomProf2() . ' ' . $this->getNomProf2();
    }

    /**
     * Get prenomProf2
     *
     * @return string
     */
    public function getPrenomProf2(): ?string
    {
        return $this->prenomProf2;
    }

    /**
     * Set prenomProf2
     *
     * @return Equipesadmin
     */
    public function setPrenomProf2(?string $prenomProf2): Equipesadmin
    {
        $this->prenomProf2 = $prenomProf2;

        return $this;
    }

    /**
     * Get nomProf2
     *
     * @return string
     */
    public function getNomProf2(): ?string
    {
        return $this->nomProf2;
    }

    /**
     * Set nomProf2
     *
     * @param string|null $nomProf2
     *
     * @return Equipesadmin
     */
    public function setNomProf2(?string $nomProf2): Equipesadmin
    {
        $this->nomProf2 = $nomProf2;

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

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
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

    /**
     * @return Collection|Professeurs[]
     */
    public function getEquipesstring(): Collection
    {
        return $this->equipesstring;
    }

    public function addEquipesstring(Professeurs $equipesstring): self
    {
        if (!$this->equipesstring->contains($equipesstring)) {
            $this->equipesstring[] = $equipesstring;
            $equipesstring->addEquipe($this);
        }

        return $this;
    }

    public function removeEquipesstring(Professeurs $equipesstring): self
    {
        if ($this->equipesstring->removeElement($equipesstring)) {
            $equipesstring->removeEquipe($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Phrases>
     */
    public function getPhrases(): Collection
    {
        return $this->phrases;
    }

    public function addPhrase(Phrases $phrase): self
    {
        if (!$this->phrases->contains($phrase)) {
            $this->phrases[] = $phrase;
            $phrase->setEquipe($this);
        }

        return $this;
    }

    public function removePhrase(Phrases $phrase): self
    {
        if ($this->phrases->removeElement($phrase)) {
            // set the owning side to null (unless already changed)
            if ($phrase->getEquipe() === $this) {
                $phrase->setEquipe(null);
            }
        }

        return $this;
    }


}
