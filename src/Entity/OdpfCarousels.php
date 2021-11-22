<?php

namespace App\Entity;

use App\Repository\CarouselsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CarouselsRepository::class)
 */
class OdpfCarousels
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
    private $name;

    /**
     * @ORM\Column(type="datetime_immutable",nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $categorie;

    /**
     * @ORM\ManyToMany(targetEntity=Imagescarousels::class)
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategorie(): ?int
    {
        return $this->categorie;
    }

    public function setCategorie(int $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection|Imagescarousels[]
     */
    public function getFile(): Collection
    {
        return $this->images;
    }

    public function addFile(Imagescarousels $file): self
    {
        if (!$this->images->contains($file)) {
            $this->images[] = $file;
        }

        return $this;
    }

    public function removeFile(Imagescarousels $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }

    /**
     * @return Collection|Imagescarousels[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Imagescarousels $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Imagescarousels $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }
}
