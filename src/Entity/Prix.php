<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prix
 *
 * @ORM\Table(name="prix")
 * @ORM\Entity(repositoryClass="App\Repository\PrixRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Prix
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id=0;

    /**
     * @ORM\Column(name="prix", type="string", length=255, nullable=true)
     */
    private ?string $prix=null;

    /**
     * @var string
     *
     * @ORM\Column(name="classement", type="string", length=255, nullable=true)
     */
    private ?string $classement=null;

    // les constantes de classe 
    const PREMIER = 600; 
    const DEUXIEME = 400; 
    const TROISIEME = 200;

        /**
     * @var boolean
     *
     * @ORM\Column(name="attribue", type="boolean")
     */
    private ?bool $attribue=false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $voix=null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $intervenant=null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $remisPar=null;
    
    /**
     * Get id
     *
     * @return string
     */
    public function __toString():?string
    {

       return $this->classement.'-'.$this->prix;

    }
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return Prix
     */
    public function setPrix(string $prix): Prix
    {
        $this->prix = $prix;

        return $this;
    }


    public function getPrix(): ?string
    {
        return $this->prix;
    }

    /**
     * Set classement
     *
     * @param string $classement
     *
     * @return Prix
     */
    public function setClassement(string $classement): Prix
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Get classement
     *
     * @return string
     */
    public function getClassement(): ?string
    {
        return $this->classement;
    }



    public function setAttribue(bool $attribue): Prix
    {
        $this->attribue = $attribue;

        return $this;
    }

    /**
     * Get attribue
     *
     * @return boolean
     */
    public function getAttribue(): ?bool
    {
        return $this->attribue;
    }

    public function getVoix(): ?string
    {
        return $this->voix;
    }

    public function setVoix(?string $voix): self
    {
        $this->voix = $voix;

        return $this;
    }
    /**
     * Get intervenant
     *
     * 
     */
    public function getIntervenant(): ?string
    {
        return $this->intervenant;
    }

    public function setIntervenant($intervenant): Prix
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    public function getRemisPar(): ?string
    {
        return $this->remisPar;
    }

    public function setRemisPar(?string $remisPar): self
    {
        $this->remisPar = $remisPar;

        return $this;
    }
}
