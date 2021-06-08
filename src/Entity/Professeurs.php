<?php

namespace App\Entity;

use App\Repository\ProfesseursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfesseursRepository::class)
 */
class Professeurs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=user::class)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=equipesadmin::class)
     */
    private $equipes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $equipesstring;

    public function __construct()
    {
        $this->equipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|equipesadmin[]
     */
    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipe(equipesadmin $equipe): self
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes[] = $equipe;
        }

        return $this;
    }

    public function removeEquipe(equipesadmin $equipe): self
    {
        $this->equipes->removeElement($equipe);

        return $this;
    }

    public function getEquipesstring(): ?string
    {
        return $this->equipesstring;
    }

    public function setEquipesstring(?string $equipesstring): self
    {
        $this->equipesstring = $equipesstring;

        return $this;
    }

    public function getRne(): ?string
    {
        return $this->user->getRne();
    }
    public function getNomlycee(): ?string
    {
        return $this->user->getRneId()->getNom();
    }
    public function getCommunelycee(): ?string
    {
        return $this->user->getRneId()->getCommune();
    }
}
