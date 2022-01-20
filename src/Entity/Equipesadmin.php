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
    protected $inscrite = 1;
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
     * @ORM\Column(name="lettre", type="string", length=1, nullable= true)
     */
    private ?string $lettre;
    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="smallint", nullable=true)
     */
    private ?int $numero;
    /**
     * @var boolean
     * @ORM\Column(name="selectionnee", type="boolean", nullable=true)
     */
    private ?bool $selectionnee;
    /**
     * @var string
     *
     * @ORM\Column(name="titreProjet", type="string", length=255, nullable=true)
     */
    private ?string $titreProjet;
    /**
     * @var string
     *
     * @ORM\Column(name="nom_lycee", type="string", length=255, nullable=true)
     */
    private ?string $nomLycee;
    /**
     * @var string
     *
     * @ORM\Column(name="denomination_lycee", type="string", length=255, nullable=true)
     */
    private ?string $denominationLycee;
    /**
     * @var string
     *
     * @ORM\Column(name="lycee_localite", type="string", length=255, nullable=true)
     */
    private ?string $lyceeLocalite;
    /**
     * @var string
     *
     * @ORM\Column(name="lycee_academie", type="string", length=255, nullable=true)
     */
    private ?string $lyceeAcademie;
    /**
     * @var string
     *
     * @ORM\Column(name="prenom_prof1", type="string", length=255, nullable=true)
     */
    private ?string $prenomProf1;
    /**
     * @var string
     *
     * @ORM\Column(name="nom_prof1", type="string", length=255, nullable=true)
     */
    private ?string $nomProf1;
    /**
     * @var string
     *
     * @ORM\Column(name="prenom_prof2", type="string", length=255, nullable=true)
     */
    private ?string $prenomProf2;
    /**
     * @var string
     *
     * @ORM\Column(name="nom_prof2", type="string", length=255, nullable=true)
     */
    private ?string $nomProf2;
    /**
     * @var string
     *
     * @ORM\Column(name="rne", type="string", length=255, nullable=true)
     */

    private ?string $rne;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rne")
     *
     */
    private Rne $rneId;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Centrescia")
     */
    private Centrescia $centre;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Edition"))
     */
    private Edition $edition;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $contribfinance;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $origineprojet;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $recompense;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $partenaire;
    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;
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
    private ?string $description;
    /**
     * @ORM\Column(type="integer")
     */
    private ?int $nbeleves;

    /**
     * @ORM\ManyToMany(targetEntity=Professeurs::class, mappedBy="equipes")
     */
    private ArrayCollection $equipesstring;

    public function __construct()
    {
        $this->equipesstring = new ArrayCollection();
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
    public function getId()
    {
        return $this->id;
    }

    public function getInfoequipe()
    {
        $nomcentre = '';
        $Numero = $this->getNumero();

        $edition = $this->getEdition();
        if ($centre = $this->getCentre()) {
            $nomcentre = $this->getCentre()->getCentre() . '-';
        }


        $nom_equipe = $this->getTitreProjet();
        $ville = $this->getLyceeLocalite();

        $infoequipe = $edition->getEd() . '-' . $nomcentre . 'Eq ' . $Numero . ' - ' . $nom_equipe . '-' . $ville;
        return $infoequipe;
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

    public function getEdition(): ?edition
    {
        return $this->edition;
    }

    public function setEdition(?edition $edition)
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
    public function getTitreProjet()
    {
        return $this->titreProjet;
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

        $this->createdAt = new \DateTime('now');
        $this->titreProjet = $titreProjet;

        return $this;
    }

    public function getLyceeLocalite(): ?string
    {
        return $this->lyceeLocalite;
    }

    public function setLyceeLocalite(string $lyceeLocalite): string
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


        }
        return $infoequipe;


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
     * Get lettre
     *
     * @return string
     */
    public function getLettre()
    {
        return $this->lettre;
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

    public function getRneId(): Rne
    {
        return $this->rneId;
    }

    public function setRneId($rne_id)
    {
        $this->rneId = $rne_id;
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
     * Set denominationLycee
     *
     * @param string $denominationLycee
     *
     * @return string
     */
    public function setDenominationLycee($denominationLycee): ?string
    {
        $this->denominationLycee = $denominationLycee;

        return $this;
    }

    public function getLyceeAcademie(): ?string
    {
        return $this->lyceeAcademie;
    }

    public function setLyceeAcademie(string $lyceeAcademie): Equipesadmin
    {
        $this->lyceeAcademie = $lyceeAcademie;

        return $this;
    }

    public function getRne(): string
    {
        return $this->rne;
    }

    public function setRne($rne): Equipesadmin
    {
        $this->rne = $rne;
        return $this;
    }

    public function getLycee(): string
    {
        return $this->getNomLycee() . ' de  ' . $this->getLyceeLocalite();
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

    public function getProf1(): string
    {

        return $this->getPrenomProf1() . ' ' . $this->getNomProf1();
    }

    public function getPrenomProf1(): ?string
    {
        return $this->prenomProf1;
    }

    public function setPrenomProf1(string $prenomProf1): string
    {
        $this->prenomProf1 = $prenomProf1;

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

    public function setNomProf1(string $nomProf1): Equipesadmin
    {
        $this->nomProf1 = $nomProf1;

        return $this;
    }

    public function getProf2(): string
    {

        return $this->getPrenomProf2() . ' ' . $this->getNomProf2();
    }

    public function getPrenomProf2(): ?string
    {
        return $this->prenomProf2;
    }

    public function setPrenomProf2($prenomProf2): Equipesadmin
    {
        $this->prenomProf2 = $prenomProf2;

        return $this;
    }

    public function getNomProf2(): ?string
    {
        return $this->nomProf2;
    }

    public function setNomProf2($nomProf2): Equipesadmin
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


}