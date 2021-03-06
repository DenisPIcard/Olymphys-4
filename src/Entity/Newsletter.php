<?php

namespace App\Entity;

use App\Repository\NewsletterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsletterRepository::class)
 */
class Newsletter
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $texte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $envoyee;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sendAt;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $destinataires;

    public function __construct()
    {   $this->createdAt=new \DateTime('now');
        $this->envoyee=false;
        $this->newsletterUsers = new ArrayCollection();
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

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }


    public function getEnvoyee(): ?bool
    {
        return $this->envoyee;
    }

    public function setEnvoyee(bool $envoyee): self
    {
        $this->envoyee = $envoyee;

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

    public function getSendAt(): ?\DateTime
    {
        return $this->sendAt;
    }

    public function setSendAt(?\DateTimeImmutable $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getDestinataires(): ?string
    {
        return $this->destinataires;
    }

    public function setDestinataires(?string $destinataires): self
    {
        $this->destinataires = $destinataires;

        return $this;
    }
}
