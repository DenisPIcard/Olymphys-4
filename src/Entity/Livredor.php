<?php

namespace App\Entity;

use App\Repository\LivredorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * livredor
 * @ORM\Table(name="livredor")
 * @ORM\Entity(repositoryClass=LivredorRepository::class)
 */
class Livredor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id=0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $nom = null;

    /**
     * @ORM\Column(type="text", length=1000,nullable=true)
     */
    private ?string $texte = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Edition")
     * @ORM\JoinColumn(name="edition_id",  referencedColumnName="id", nullable=true)
     */
    private ?int $edition = 0;
    
     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $categorie = null;
   
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User") 
     *  @ORM\JoinColumn(name="user_id",  referencedColumnName="id", nullable=true,)
     */
    private user $user;

    /**
     * @ORM\OneToOne(targetEntity=Equipesadmin::class, cascade={"remove"})
     */
    private Equipesadmin $equipe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getEdition(): ?int
    {
        return $this->edition;
    }

    public function setEdition($edition): Livredor
    {
        $this->edition = $edition;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser( $user): Livredor
    {
        $this->user = $user;

        return $this;
    }
    
     public function getCategorie(): ?string
     {
        return $this->categorie;
    }

    public function setCategorie( $categorie): Livredor
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getEquipe(): ?equipesadmin
    {
        return $this->equipe;
    }

    public function setEquipe(?equipesadmin $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }
}
