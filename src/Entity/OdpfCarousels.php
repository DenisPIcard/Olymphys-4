<?php

namespace App\Entity;

use App\Repository\OdpfCarouselsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=OdpfCarouselsRepository::class)
 * @Vich\Uploadable
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
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $categorie;

    /**
     * @ORM\ManyToMany(targetEntity=OdpfImagescarousels::class,cascade={"persist"})
     */
    private $images;
public function __toString(){

    return $this->name;

}

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->createdAt=new \DateTime('now');
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

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
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
     * @return Collection|OdpfImagescarousels[]
     */
    public function getFile(): Collection
    {
        return $this->images;
    }

    public function addFile(OdpfImagescarousels $file): self
    {
        if (!$this->images->contains($file)) {
            $this->images[] = $file;
        }

        return $this;
    }

    public function removeFile(OdpfImagescarousels $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }

    /**
     * @return Collection|OdpfImagescarousels[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(OdpfImagescarousels $image): self
    {

        $this->images[] = $image;
        return $this;
    }

    public function removeImage(OdpfImagescarousels $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }
}
