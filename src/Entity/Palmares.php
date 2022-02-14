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
    private ?int $id=0;

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
    * @ORM\PostPersist
    */ 
    public function attributionsPrix()
    {
        $repositoryEquipes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('App:Equipes');
    
    foreach (range('A','Z') as $i)
        {
        // On récupère le nom du getter correspondant à l'attribut.
        $method = 'get'.ucfirst($i);

        // Si le getter correspondant existe.
        if (method_exists($this, $method))
        {
        // On appelle le setter.
        $prix = $this->$method();
            if($prix)
            {
                $equipe = $repositoryEquipes->findOneByLettre($i);
                $equipe->setPrix($prix);

            } 
        }
        }
    }


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
     * @return Palmares
     */
    public function setA($a): Palmares
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
     * @param $b
     * @return Palmares
     */
    public function setB($b): Palmares
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
     * @param $c
     * @return Palmares
     */
    public function setC($c): Palmares
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
     * @param $d
     * @return Palmares
     */
    public function setD($d): Palmares
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
     * @param $e
     * @return Palmares
     */
    public function setE($e): Palmares
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
     * @param $f
     * @return Palmares
     */
    public function setF($f): Palmares
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
     * @param $g
     * @return Palmares
     */
    public function setG($g): Palmares
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
     * @param $h
     * @return Palmares
     */
    public function setH($h): Palmares
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
     * @param $i
     * @return Palmares
     */
    public function setI($i): Palmares
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
     * @param $j
     * @return Palmares
     */
    public function setJ($j): Palmares
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
     * @param $k
     * @return Palmares
     */
    public function setK($k): Palmares
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
     * @param $l
     * @return Palmares
     */
    public function setL($l): Palmares
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
     * @param $m
     * @return Palmares
     */
    public function setM($m): Palmares
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
     * @param $n
     * @return Palmares
     */
    public function setN($n): Palmares
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
     * @param $o
     * @return Palmares
     */
    public function setO($o): Palmares
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
     * @param $p
     * @return Palmares
     */
    public function setP($p): Palmares
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
     * @param $q
     * @return Palmares
     */
    public function setQ($q): Palmares
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
     * @param $r
     * @return Palmares
     */
    public function setR($r): Palmares
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
     * @param $s
     * @return Palmares
     */
    public function setS($s): Palmares
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
     * @param $t
     * @return Palmares
     */
    public function setT($t): Palmares
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
     * @param $u
     * @return Palmares
     */
    public function setU($u): Palmares
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
     * @param $v
     * @return Palmares
     */
    public function setV($v): Palmares
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
     * @param $w
     * @return Palmares
     */
    public function setW($w): Palmares
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
     * @param $x
     * @return Palmares
     */
    public function setX($x): Palmares
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
     * @param $y
     * @return Palmares
     */
    public function setY($y): Palmares
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
     * @param $z
     * @return Palmares
     */
    public function setZ($z): Palmares
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
