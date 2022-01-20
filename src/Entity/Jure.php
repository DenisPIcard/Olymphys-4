<?php

namespace App\Entity;

use App\Repository\JureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JureRepository::class)
 */
class Jure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToOne(targetEntity=user::class, cascade={"persist", "remove"})
     */
    private ?user $etatcivil;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtatcivil(): ?user
    {
        return $this->etatcivil;
    }

    public function setEtatcivil(?user $etatcivil): self
    {
        $this->etatcivil = $etatcivil;

        return $this;
    }
}
