<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
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
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipes", inversedBy="phrases")
     * @ORM\JoinColumn(name="equipe_id",nullable=false)
     */
    private Equipes $equipe;


    /**
     * @ORM\Column(name="phrase", type="text", nullable=true)
     * @Assert\Length(min=1, minMessage="La phrase amusante doit contenir au moins {{ limit }} caractère. ")
     */
    private ?string $phrase = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Liaison")
     * @ORM\JoinColumn(name="liaison_id", nullable=true)
     */
    private ?Liaison $liaison = null;


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
     * Set equipe
     *
     * @param Equipes $equipe
     *
     * @return Phrases
     */
    public function setEquipe(Equipes $equipe): Phrases
    {
        $this->equipe = $equipe;

        return $this;
    }

    /**
     * Get equipe
     *
     * @return Equipes
     */
    public function getEquipe(): ?Equipes
    {
        return $this->equipe;
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

    public function getLiaison(): ?Liaison
    {
        return $this->liaison;
    }

    public function setLiaison(Liaison $liaison = null)
    {
        $this->liaison = $liaison;
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
