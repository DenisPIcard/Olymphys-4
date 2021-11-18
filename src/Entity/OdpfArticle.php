<?php

namespace App\Entity;

use App\Repository\OdpfArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OdpfArticleRepository::class)
 */
class OdpfArticle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $choix;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $soustitre;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $IdCategorie;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $image;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $alt_image;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $descr_image;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $texte;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $titre_objectifs;

    /**
    * @ORM\Column(type="string", nullable=true)
    */
    private ?string $texte_objectifs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChoix(): ?string
    {
        return $this->choix;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function setChoix(?string $choix): self
    {
        $this->choix = $choix;

        return $this;
    }

    public function getSoustitre(): ?string
    {
        return $this->soustitre;
    }

    public function setSoustitre(?string $soustitre): self
    {
        $this->soustitre = $soustitre;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getTitreObjectifs(): ?string
    {
        return $this->titre_objectifs;
    }

    public function setTitreObjectifs(?string $titre_objectifs): self
    {
        $this->titre_objectifs = $titre_objectifs;

        return $this;
    }

    public function getTexteObjectifs(): ?string
    {
        return $this->texte_objectifs;
    }

    public function setTexteObjectifs(?string $texte_objectifs): self
    {
        $this->texte_objectifs = $texte_objectifs;

        return $this;
    }

    public function getIdCategorie(): ?string
    {
        return $this->IdCategorie;
    }

    public function setIdCategorie(string $IdCategorie): self
    {
        $this->IdCategorie = $IdCategorie;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getAltImage(): ?string
    {
        return $this->alt_image;
    }

    public function setAltImage(?string $alt_image): self
    {
        $this->alt_image = $alt_image;

        return $this;
    }

    public function getDescrImage(): ?string
    {
        return $this->descr_image;
    }

    public function setDescrImage(?string $descr_image): self
    {
        $this->descr_image = $descr_image;

        return $this;
    }
}