<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Palmares
 *
 * @ORM\Table(name="palmares")
 * @ORM\Entity(repositoryClass="App\Repository\PalmaresRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Palmares
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
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=255)
     */
    private string $categorie;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private $a;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $b;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $c;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $d;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $e;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $f;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $g;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $h;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $i;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $j;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $k;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $l;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $m;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $n;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $o;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $p;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $q;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $r;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $s;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $t;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $u;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $v;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $w;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $x;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $y;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $z;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get categorie
     *
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set categorie
     *
     * @param string $categorie
     *
     * @return Palmares
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getA()
    {
        return $this->a;
    }

    public function setA($a): Palmares
    {
        $this->a = $a;

        return $this;
    }

    public function getB(): Prix
    {
        return $this->b;
    }

    public function setB($b): Palmares
    {
        $this->b = $b;

        return $this;
    }

    public function getC(): Prix
    {
        return $this->c;
    }

    public function setC($c): Palmares
    {
        $this->c = $c;

        return $this;
    }

    public function getD(): Prix
    {
        return $this->d;
    }

    public function setD($d): Palmares
    {
        $this->d = $d;

        return $this;
    }

    public function getE(): Prix
    {
        return $this->e;
    }

    public function setE($e): Palmares
    {
        $this->e = $e;

        return $this;
    }

    public function getF(): Prix
    {
        return $this->f;
    }

    public function setF($f): Palmares
    {
        $this->f = $f;

        return $this;
    }

    public function getG(): Prix
    {
        return $this->g;
    }

    public function setG($g): Palmares
    {
        $this->g = $g;

        return $this;
    }

    public function getH(): Prix
    {
        return $this->h;
    }

    public function setH($h): Palmares
    {
        $this->h = $h;

        return $this;
    }

    public function getI(): Prix
    {
        return $this->i;
    }

    public function setI($i): Palmares
    {
        $this->i = $i;

        return $this;
    }

    public function getJ(): Prix
    {
        return $this->j;
    }

    public function setJ($j): Palmares
    {
        $this->j = $j;

        return $this;
    }

    public function getK(): Prix
    {
        return $this->k;
    }

    public function setK($k): Palmares
    {
        $this->k = $k;

        return $this;
    }

    public function getL(): Prix
    {
        return $this->l;
    }

    public function setL($l): Palmares
    {
        $this->l = $l;

        return $this;
    }

    public function getM(): Prix
    {
        return $this->m;
    }

    public function setM($m): Palmares
    {
        $this->m = $m;

        return $this;
    }

    public function getN(): Prix
    {
        return $this->n;
    }

    public function setN($n)
    {
        $this->n = $n;

        return $this;
    }

    public function getO(): Prix
    {
        return $this->o;
    }

    public function setO($o): Palmares
    {
        $this->o = $o;

        return $this;
    }

    public function getP(): Prix
    {
        return $this->p;
    }

    public function setP($p): Palmares
    {
        $this->p = $p;

        return $this;
    }

    public function getQ()
    {
        return $this->q;
    }

    public function setQ($q): Palmares
    {
        $this->q = $q;

        return $this;
    }

    public function getR()
    {
        return $this->r;
    }

    public function setR($r): Palmares
    {
        $this->r = $r;

        return $this;
    }

    public function getS(): Prix
    {
        return $this->s;
    }

    public function setS($s): Palmares
    {
        $this->s = $s;

        return $this;
    }

    public function getT(): Prix
    {
        return $this->t;
    }

    public function setT($t): Palmares
    {
        $this->t = $t;

        return $this;
    }

    public function getU(): Prix
    {
        return $this->u;
    }

    public function setU($u): Palmares
    {
        $this->u = $u;

        return $this;
    }

    public function getV(): Prix
    {
        return $this->v;
    }

    public function setV($v): Palmares
    {
        $this->v = $v;

        return $this;
    }

    public function getW(): Prix
    {
        return $this->w;
    }

    public function setW($w): Palmares
    {
        $this->w = $w;

        return $this;
    }

    public function getX(): Prix
    {
        return $this->x;
    }

    public function setX($x): Palmares
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): Prix
    {
        return $this->y;
    }

    public function setY($y): Palmares
    {
        $this->y = $y;

        return $this;
    }

    public function getZ(): Prix
    {
        return $this->z;
    }

    public function setZ($z): Palmares
    {
        $this->z = $z;

        return $this;
    }

}
