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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id=null;

    /**
     * @ORM\Column(name="categorie", type="string", length=255)
     */
    private ?string $categorie = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private Prix $a;

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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set categorie
     *
     * @param string $categorie
     *
     * @return Palmares
     */
    public function setCategorie(string $categorie): Palmares
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string
     */
    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    /**
     * Set a
     * @param Prix|null $a
     * @return Palmares
     */
    public function setA(?Prix $a): Palmares
    {
        $this->a = $a;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getA(): ?Prix
    {
        return $this->a;
    }

    /**
     * Set b
     *
     * @param Prix|null $b
     * @return Palmares
     */
    public function setB(?Prix $b): Palmares
    {
        $this->b = $b;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getB(): ?Prix
    {
        return $this->b;
    }

    /**
     * Set c
     *
     * @param Prix|null $c
     * @return Palmares
     */
    public function setC(?Prix $c): Palmares
    {
        $this->c = $c;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getC(): ?Prix
    {
        return $this->c;
    }

    /**
     * Set d
     *
     * @param Prix|null $d
     * @return Palmares
     */
    public function setD(?Prix $d): Palmares
    {
        $this->d = $d;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getD(): ?Prix
    {
        return $this->d;
    }

    /**
     * Set e
     *
     * @param Prix|null $e
     * @return Palmares
     */
    public function setE(?Prix $e): Palmares
    {
        $this->e = $e;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getE(): ?Prix
    {
        return $this->e;
    }

    /**
     * Set f
     *
     * @param Prix|null $f
     * @return Palmares
     */
    public function setF(?Prix $f): Palmares
    {
        $this->f = $f;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getF(): ?Prix
    {
        return $this->f;
    }

    /**
     * Set g
     *
     * @param Prix|null $g
     * @return Palmares
     */
    public function setG(?Prix $g): Palmares
    {
        $this->g = $g;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getG(): ?Prix
    {
        return $this->g;
    }

    /**
     * Set h
     *
     * @param Prix|null $h
     * @return Palmares
     */
    public function setH(?Prix $h): Palmares
    {
        $this->h = $h;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getH(): ?Prix
    {
        return $this->h;
    }

    /**
     * Set i
     *
     * @param Prix|null $i
     * @return Palmares
     */
    public function setI(?Prix $i): Palmares
    {
        $this->i = $i;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getI(): ?Prix
    {
        return $this->i;
    }

    /**
     * Set j
     *
     * @param Prix|null $j
     * @return Palmares
     */
    public function setJ(?Prix $j): Palmares
    {
        $this->j = $j;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getJ(): ?Prix
    {
        return $this->j;
    }

    /**
     * Set k
     *
     * @param Prix|null $k
     * @return Palmares
     */
    public function setK(?Prix $k): Palmares
    {
        $this->k = $k;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getK(): ?Prix
    {
        return $this->k;
    }

    /**
     * Set l
     *
     * @param Prix|null $l
     * @return Palmares
     */
    public function setL(?Prix $l): Palmares
    {
        $this->l = $l;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getL(): ?Prix
    {
        return $this->l;
    }

    /**
     * Set m
     *
     * @param Prix|null $m
     * @return Palmares
     */
    public function setM(?Prix $m): Palmares
    {
        $this->m = $m;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getM(): ?Prix
    {
        return $this->m;
    }

    /**
     * Set n
     *
     * @param Prix|null $n
     * @return Palmares
     */
    public function setN(?Prix $n): Palmares
    {
        $this->n = $n;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getN(): ?Prix
    {
        return $this->n;
    }

    /**
     * Set o
     *
     * @param Prix|null $o
     * @return Palmares
     */
    public function setO(?Prix $o): Palmares
    {
        $this->o = $o;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getO(): ?Prix
    {
        return $this->o;
    }

    /**
     * Set p
     *
     * @param Prix|null $p
     * @return Palmares
     */
    public function setP(?Prix $p): Palmares
    {
        $this->p = $p;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getP(): ?Prix
    {
        return $this->p;
    }

    /**
     * Set q
     *
     * @param Prix|null $q
     * @return Palmares
     */
    public function setQ(?Prix $q): Palmares
    {
        $this->q = $q;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getQ(): ?Prix
    {
        return $this->q;
    }

    /**
     * Set r
     *
     * @param Prix|null $r
     * @return Palmares
     */
    public function setR(?Prix $r): Palmares
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getR(): ?Prix
    {
        return $this->r;
    }

    /**
     * Set s
     *
     * @param Prix|null $s
     * @return Palmares
     */
    public function setS(?Prix $s): Palmares
    {
        $this->s = $s;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getS(): ?Prix
    {
        return $this->s;
    }

    /**
     * Set t
     *
     * @param Prix|null $t
     * @return Palmares
     */
    public function setT(?Prix $t): Palmares
    {
        $this->t = $t;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getT(): ?Prix
    {
        return $this->t;
    }

    /**
     * Set u
     *
     * @param Prix|null $u
     * @return Palmares
     */
    public function setU(?Prix $u): Palmares
    {
        $this->u = $u;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getU(): ?Prix
    {
        return $this->u;
    }

    /**
     * Set v
     *
     * @param Prix|null $v
     * @return Palmares
     */
    public function setV(?Prix $v): Palmares
    {
        $this->v = $v;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getV(): ?Prix
    {
        return $this->v;
    }

    /**
     * Set w
     *
     * @param Prix|null $w
     * @return Palmares
     */
    public function setW(?Prix$w): Palmares
    {
        $this->w = $w;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getW(): ?Prix
    {
        return $this->w;
    }

    /**
     * Set x
     *
     * @param Prix|null $x
     * @return Palmares
     */
    public function setX(?Prix $x): Palmares
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getX(): ?Prix
    {
        return $this->x;
    }

    /**
     * Set y
     *
     * @param Prix|null $y
     * @return Palmares
     */
    public function setY(?Prix $y): Palmares
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getY(): ?Prix
    {
        return $this->y;
    }

    /**
     * Set z
     *
     * @param Prix|null $z
     * @return Palmares
     */
    public function setZ(?Prix $z): Palmares
    {
        $this->z = $z;

        return $this;
    }

    /**
     * Get prix
     *
     * @return Prix
     */
    public function getZ(): ?Prix
    {
        return $this->z;
    }
    
}
