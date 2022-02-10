<?php
// src/Entity/Adminsite.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adminsite
 *
 * @ORM\Table(name="adminsite")
 * @ORM\Entity(repositoryClass="App\Repository\AdminsiteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Adminsite
{
    /**
     * @var \datetime
     * @ORM\Column(name="datelimite_cia", type="datetime", nullable=true)
     */
    protected \datetime $datelimcia;
    /**
     * @var \datetime
     * @ORM\Column(name="datelimite_nat", type="datetime",nullable=true)
     */
    protected \datetime $datelimnat;
    /**
     * @var \datetime
     * @ORM\Column(name="concours_cia", type="datetime",nullable=true)
     */
    protected \datetime $concourscia;
    /**
     * @var \datetime
     * @ORM\Column(name="concours_cn", type="datetime",nullable=true)
     */
    protected \datetime $concourscn;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id=0;
    /**
     * @var string
     * @ORM\Column(name="session", type="string", nullable=true)
     */
    private $session=0;
    private $requestStack;

    public function getId(): int
    {
        return $this->id;
    }

    public function getSession(): string
    {
        return $this->session;
    }

    public function setSession($session)
    {
        $this->requestStack = $session;
    }

    public function getDatelimcia(): \datetime
    {
        return $this->datelimcia;
    }

    public function setDatelimcia($Date)
    {
        $this->datelimcia = $Date;
    }

    public function getDatelimnat(): \datetime
    {
        return $this->datelimnat;
    }

    public function setDatelimnat($Date)
    {
        $this->datelimnat = $Date;
    }

    public function getConcourscia(): \datetime
    {
        return $this->concourscia;
    }

    public function setConcourscia($Date)
    {
        $this->concourscia = $Date;
    }

    public function getConcourscn(): \datetime
    {
        return $this->concourscn;
    }

    public function setConcourscn($Date)
    {
        $this->concourscn = $Date;
    }


}

