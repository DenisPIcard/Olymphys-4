<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Phrases
 *
 * @ORM\Table(name="phrases")
 * @ORM\Entity(repositoryClass="App\Repository\PhrasesRepository")
 */
class Phrases
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = 0;

    /**
     * @ORM\Column(name="phrase", type="text", nullable=true)
     * @Assert\Length(min=1, minMessage="La phrase amusante doit contenir au moins {{ limit }} caractère. ")
     */
    private ?string $phrase = null;

    /**
     * @ORM\Column(name="prix", type="text", nullable=true)
     * @Assert\Length(min=1, minMessage="L'intitulé du prix amusant doit contenir au moins {{ limit }} caractère. ")
     */
    private  ?string $prix = null;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get phrase
     *
     * @return string
     */
    public function getPhrase(): ?string
    {
        return $this->phrase;
    }

    /**
     * Set phrase
     *
     * @param string $phrase
     *
     * @return Phrases
     */
    public function setPhrase(string $phrase): Phrases
    {
        $this->phrase = $phrase;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix(): ?string
    {
        return $this->prix;
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return Phrases
     */
    public function setPrix(string $prix): Phrases
    {
        $this->prix = $prix;

        return $this;
    }

}
