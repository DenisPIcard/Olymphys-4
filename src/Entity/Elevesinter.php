<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eleves
 *
 * @ORM\Table(name="elevesinter")
 * @ORM\Entity(repositoryClass="App\Repository\ElevesinterRepository")
 */
class Elevesinter
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
     * @var int
     *
     * @ORM\Column(name="numsite", type="integer", nullable=true)
     *
     */
    private ?int $numsite;
    //numsite est l'id de l'élève sur le site odpf.org


    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private ?string $nom;
    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     */
    private ?string $prenom;
    /**
     * @var string
     * @ORM\Column(name="genre", type="string", length=1, nullable=true)
     */
    private ?string $genre;


    /**
     * @var string
     *
     * @ORM\Column(name="classe", type="string", length=255, nullable=true)
     */
    private ?string $classe;


    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipesadmin")
     * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id" )
     */
    private Equipesadmin $equipe;

    /**
     * @var string
     *
     * @ORM\Column(name="courriel", type="string",length=60, nullable=true)
     */
    private ?string $courriel;

    /**
     * @ORM\OneToOne(targetEntity=fichiersequipes::class, cascade={"persist", "remove"})
     *
     */
    private Fichiersequipes $autorisationphotos;


    public function __toString()
    {
        return $this->getNomPrenomlivre();

    }

    public function getNomPrenomlivre()
    {
        if ($this->equipe->getSelectionnee() == true) {
            $NomPrenom = $this->equipe->getNumero() . '-' . $this->equipe->getLettre() . '-' . $this->nom . ' ' . $this->prenom;
        }
        if ($this->equipe->getSelectionnee() == false) {
            $NomPrenom = $this->equipe->getNumero() . '-' . $this->nom . ' ' . $this->prenom;
        }
        return $NomPrenom;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get numsite
     *
     * @return integer
     */
    public function getNumsite()
    {
        return $this->numsite;
    }

    /**
     * Set numsite
     *
     * @var integer
     */
    public function setNumsite($numsite)
    {
        $this->numsite = $numsite;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Elevesinter
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get classe
     *
     * @return string
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set classe
     *
     * @param string $classe
     *
     * @return string
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }

    public function getEquipe()
    {
        return $this->equipe;
    }

    public function setEquipe($Equipe)
    {
        $this->equipe = $Equipe;

        return $this;
    }

    public function getCourriel()
    {
        return $this->courriel;
    }

    public function setCourriel($courriel)
    {
        $this->courriel = $courriel;

        return $this;
    }

    public function getGenre()
    {
        return $this->genre;
    }

    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAutorisationphotos()
    {
        return $this->autorisationphotos;
    }

    public function setAutorisationphotos($autorisation)
    {
        $this->autorisationphotos = $autorisation;

        return $this;
    }

    public function getNomPrenom()
    {

        $NomPrenom = $this->nom . ' ' . $this->prenom;

        return $NomPrenom;
    }


}