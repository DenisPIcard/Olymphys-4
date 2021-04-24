<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EditionRepository")
 */
class Edition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ed;

    /**
     * @ORM\Column(type="datetime",  nullable=true)
     */
    private $date;

   

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $lieu;
    
     /**
        * @var \datetime
        * @ORM\Column(name="datelimite_cia", type="datetime", nullable=true)
        */    
        protected $datelimcia;
    
       /**
        * @var \datetime
        *  @ORM\Column(name="datelimite_nat", type="datetime",nullable=true)
        */    
        protected $datelimnat;
    
       /**
        *  @var \datetime
        *  @ORM\Column(name="date_ouverture_site", type="datetime",nullable=true)
        */    
        protected $dateouverturesite;
     
        
        /**
        * @var \datetime
        *  @ORM\Column(name="concours_cia", type="date",nullable=true)
        */    
        protected $concourscia;
       
        
         /**
        * @var \datetime
        *  @ORM\Column(name="concours_cn", type="date",nullable=true)
        */    
        protected $concourscn;

        /**
         * @ORM\Column(type="datetime")
         */
        private $dateclotureinscription;

       
        
  
       

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEd(): ?string
    {
        return $this->ed;
    }

    public function setEd(string $ed): self
    {
        $this->ed = $ed;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

   

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }
     public function setDatelimcia($Date)
    {
        $this->datelimcia = $Date;
    }

    public function getDatelimcia()
    {
        return $this->datelimcia;
    }
    
     public function setDatelimnat($Date)
    {
        $this->datelimnat = $Date;
    }

    public function getDatelimnat()
    {
        return $this->datelimnat;
    }
    
    public function setDateouverturesite($Date)
    {
        $this->dateouverturesite = $Date;
    }

    public function getDateouverturesite()
    {
        return $this->dateouverturesite;
    }
      public function setConcourscia($Date)
    {
        $this->concourscia = $Date;
    }

    public function getConcourscia()
    {
        return $this->concourscia;
    }
     public function setConcourscn($Date)
    {
        $this->concourscn = $Date;
    }

    public function getConcourscn()
    {
        return $this->concourscn;
    }

    public function getEncours(): ?bool
    {
        return $this->encours;
    }

    public function setEncours(?bool $encours): self
    {
        $this->encours = $encours;

        return $this;
    }

    public function getDateclotureinscription(): ?\DateTimeInterface
    {
        return $this->dateclotureinscription;
    }

    public function setDateclotureinscription(\DateTimeInterface $dateclotureinscription): self
    {
        $this->dateclotureinscription = $dateclotureinscription;

        return $this;
    }
    
    
}
