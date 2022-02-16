<?php

namespace App\Entity\Odpf;

use App\Repository\OdpfCategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OdpfCategorieRepository::class)
 */
class OdpfCategorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Nom;

    /**
     * @ORM\OneToMany(targetEntity=OdpfArticle::class, mappedBy="idCategorie")
     */
    private $odpfArticles;

    public function __construct()
    {
        $this->odpfArticles = new ArrayCollection();
    }
    public function __toString(){

        return $this->getNom();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    /**
     * @return Collection|OdpfArticle[]
     */
    public function getOdpfArticles(): Collection
    {
        return $this->odpfArticles;
    }

    public function addOdpfArticle(OdpfArticle $odpfArticle): self
    {
        if (!$this->odpfArticles->contains($odpfArticle)) {
            $this->odpfArticles[] = $odpfArticle;
            $odpfArticle->setIdCategorie($this);
        }

        return $this;
    }

    public function removeOdpfArticle(OdpfArticle $odpfArticle): self
    {
        if ($this->odpfArticles->removeElement($odpfArticle)) {
            // set the owning side to null (unless already changed)
            if ($odpfArticle->getIdCategorie() === $this) {
                $odpfArticle->setIdCategorie(null);
            }
        }

        return $this;
    }
}
