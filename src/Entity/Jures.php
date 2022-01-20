<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Jures
 *
 * @ORM\Table(name="jures")
 * @ORM\Entity(repositoryClass="App\Repository\JuresRepository")
 */
class Jures
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;


    /**
     * @ORM\OneToOne(targetEntity=user::class, cascade={ "remove"})
     */
    private ?user $iduser;

    /**
     * @var string
     *
     * @ORM\Column(name="prenomJure", type="string", length=255)
     */
    private string $prenomJure;

    /**
     * @var string
     *
     * @ORM\Column(name="nomJure", type="string", length=255)
     */
    private string $nomJure;

    /**
     * @var string
     *
     * @ORM\Column(name="initialesJure", type="string", length=255)
     */
    private string $initialesJure;

    /**
     * @var int
     *
     * @ORM\Column(name="A", type="smallint", nullable=true)
     */
    private int $a;

    /**
     * @var int
     *
     * @ORM\Column(name="B", type="smallint", nullable=true)
     */
    private int $b;

    /**
     * @var int
     *
     * @ORM\Column(name="C", type="smallint", nullable=true)
     */
    private int $c;

    /**
     * @var int
     *
     * @ORM\Column(name="D", type="smallint", nullable=true)
     */
    private int $d;

    /**
     * @var int
     *
     * @ORM\Column(name="E", type="smallint", nullable=true)
     */
    private int $e;

    /**
     * @var int
     *
     * @ORM\Column(name="F", type="smallint", nullable=true)
     */
    private int $f;

    /**
     * @var int
     *
     * @ORM\Column(name="G", type="smallint", nullable=true)
     */
    private int $g;

    /**
     * @var int
     *
     * @ORM\Column(name="H", type="smallint", nullable=true)
     */
    private int $h;

    /**
     * @var int
     *
     * @ORM\Column(name="I", type="smallint", nullable=true)
     */
    private int $i;

    /**
     * @var int
     *
     * @ORM\Column(name="J", type="smallint", nullable=true)
     */
    private int $j;

    /**
     * @var int
     *
     * @ORM\Column(name="K", type="smallint", nullable=true)
     */
    private int $k;

    /**
     * @var int
     *
     * @ORM\Column(name="L", type="smallint", nullable=true)
     */
    private int $l;

    /**
     * @var int
     *
     * @ORM\Column(name="M", type="smallint", nullable=true)
     */
    private int $m;

    /**
     * @var int
     *
     * @ORM\Column(name="N", type="smallint", nullable=true)
     */
    private int $n;

    /**
     * @var int
     *
     * @ORM\Column(name="O", type="smallint", nullable=true)
     */
    private int $o;

    /**
     * @var int
     *
     * @ORM\Column(name="P", type="smallint", nullable=true)
     */
    private int $p;

    /**
     * @var int
     *
     * @ORM\Column(name="Q", type="smallint", nullable=true)
     */
    private int $q;

    /**
     * @var int
     *
     * @ORM\Column(name="R", type="smallint", nullable=true)
     */
    private int $r;

    /**
     * @var int
     *
     * @ORM\Column(name="S", type="smallint", nullable=true)
     */
    private int $s;

    /**
     * @var int
     *
     * @ORM\Column(name="T", type="smallint", nullable=true)
     */
    private int $t;

    /**
     * @var int
     *
     * @ORM\Column(name="U", type="smallint", nullable=true)
     */
    private int $u;

    /**
     * @var int
     *
     * @ORM\Column(name="V", type="smallint", nullable=true)
     */
    private int $v;

    /**
     * @var int
     *
     * @ORM\Column(name="W", type="smallint", nullable=true)
     */
    private int $w;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notes", mappedBy="jure")
     */
    private ArrayCollection $notesj;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $x;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $y;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $z;

    public function __construct()
    {
        $this->notesj = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getInitialesJure(): string
    {
        return $this->initialesJure;
    }

    public function setInitialesJure($initialesJure): Jures
    {
        $this->initialesJure = $initialesJure;

        return $this;
    }

    /**
     * Get a
     *
     * @return int
     */
    public function getA(): int
    {
        return $this->a;
    }

    public function setA($a): Jures
    {
        $this->a = $a;

        return $this;
    }

    /**
     * Get b
     *
     * @return int
     */
    public function getB(): int
    {
        return $this->b;
    }

    /**
     * Set b
     *
     * @param integer $b
     *
     * @return Jures
     */
    public function setB($b): Jures
    {
        $this->b = $b;

        return $this;
    }

    public function getC()
    {
        return $this->c;
    }

    /**
     * Set c
     *
     * @param integer $c
     *
     * @return Jures
     */
    public function setC($c): Jures
    {
        $this->c = $c;

        return $this;
    }

    public function getD(): int
    {
        return $this->d;
    }

    public function setD($d)
    {
        $this->d = $d;

        return $this;
    }

    public function getE(): int
    {
        return $this->e;
    }

    public function setE($e): Jures
    {
        $this->e = $e;

        return $this;
    }

    public function getF(): int
    {
        return $this->f;
    }

    public function setF($f): Jures
    {
        $this->f = $f;

        return $this;
    }

    public function getG(): int
    {
        return $this->g;
    }

    public function setG($g): Jures
    {
        $this->g = $g;

        return $this;
    }

    public function getH(): int
    {
        return $this->h;
    }

    public function setH($h): Jures
    {
        $this->h = $h;

        return $this;
    }

    public function getI(): int
    {
        return $this->i;
    }

    public function setI($i): Jures
    {
        $this->i = $i;

        return $this;
    }

    public function getJ(): int
    {
        return $this->j;
    }

    public function setJ($j): Jures
    {
        $this->j = $j;

        return $this;
    }

    public function getK(): int
    {
        return $this->k;
    }

    public function setK($k): Jures
    {
        $this->k = $k;

        return $this;
    }

    public function getL(): int
    {
        return $this->l;
    }

    public function setL($l): Jures
    {
        $this->l = $l;

        return $this;
    }

    public function getM(): int
    {
        return $this->m;
    }

    public function setM($m): Jures
    {
        $this->m = $m;

        return $this;
    }

    public function getN(): int
    {
        return $this->n;
    }

    public function setN($n): Jures
    {
        $this->n = $n;

        return $this;
    }

    public function getO(): int
    {
        return $this->o;
    }

    public function setO($o): Jures
    {
        $this->o = $o;

        return $this;
    }

    public function getP(): int
    {
        return $this->p;
    }

    public function setP($p): Jures
    {
        $this->p = $p;

        return $this;
    }

    public function getQ(): int
    {
        return $this->q;
    }

    public function setQ($q): Jures
    {
        $this->q = $q;

        return $this;
    }

    public function getR(): int
    {
        return $this->r;
    }

    public function setR($r): Jures
    {
        $this->r = $r;

        return $this;
    }

    public function getS(): int
    {
        return $this->s;
    }

    public function setS($s): Jures
    {
        $this->s = $s;

        return $this;
    }

    public function getT(): int
    {
        return $this->t;
    }

    public function setT($t): Jures
    {
        $this->t = $t;

        return $this;
    }

    public function getU(): int
    {
        return $this->u;
    }

    public function setU($u): Jures
    {
        $this->u = $u;

        return $this;
    }

    public function getV(): int
    {
        return $this->v;
    }

    public function setV($v): Jures
    {
        $this->v = $v;

        return $this;
    }

    public function getW(): int
    {
        return $this->w;
    }

    public function setW($w): Jures
    {
        $this->w = $w;

        return $this;
    }

    public function getAttributions(): array
    {
        $attribution = array();

        foreach (range('A', 'Z') as $i) {
            // On récupère le nom du getter correspondant à l'attribut.
            $method = 'get' . ucfirst($i);


            // Si le getter correspondant existe.
            if (method_exists($this, $method)) {
                // On appelle le setter.
                $statut = $this->$method();
                if ($statut == 1) {
                    $attribution[$i] = 1;
                } elseif (is_int($statut)) {
                    $attribution[$i] = 0;
                }
            }

        }
        return $attribution;

    }

    public function addNotesj(\App\Entity\Notes $notesj): Jures
    {
        $this->notesj[] = $notesj;

        //On relie l'équipe à "une ligne note"
        $notesj->setJure($this);

        return $this;
    }

    public function removeNotesj(\App\Entity\Notes $notesj)
    {
        $this->notess->removeElement($notesj);
    }

    public function getNotesj(): ArrayCollection
    {
        return $this->notesj;
    }

    public function getNom()
    {
        return $this->getNomJure() . ' ' . $this->getPrenomJure();
    }

    public function getNomJure(): string
    {
        return $this->nomJure;
    }

    public function setNomJure($nomJure): Jures
    {
        $this->nomJure = $nomJure;

        return $this;
    }

    public function getPrenomJure(): string
    {
        return $this->prenomJure;
    }

    /**
     * Set prenomJure
     *
     * @param string $prenomJure
     *
     * @return Jures
     */
    public function setPrenomJure($prenomJure)
    {
        $this->prenomJure = $prenomJure;

        return $this;
    }

    public function getIduser(): ?user
    {
        return $this->iduser;
    }

    public function setIduser(?user $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(?int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(?int $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getZ(): ?int
    {
        return $this->z;
    }

    public function setZ(?int $z): self
    {
        $this->z = $z;

        return $this;
    }


}

