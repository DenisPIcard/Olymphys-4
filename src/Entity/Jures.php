<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private int $id=0;


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
    private int $i ;

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
    private int $n ;

    /**
     * @var int
     *
     * @ORM\Column(name="O", type="smallint", nullable=true)
     */
    private int $o ;

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
    private int $u ;

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
    private int $w ;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notes", mappedBy="jure")
     */
    private ?collection $notesj;

    /**
     * @ORM\Column(name ="X",type="smallint", nullable=true)
     */
    private int $x;

    /**
     * @ORM\Column(name="Y",type="smallint", nullable=true)
     */
    private int $y;

    /**
     * @ORM\Column(name="Z",type="smallint", nullable=true)
     */
    private int $z ;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notesj = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get initialesJure
     *
     * @return string
     */
    public function getInitialesJure(): string
    {
        return $this->initialesJure;
    }

    /**
     * Set initialesJure
     *
     * @param string $initialesJure
     *
     * @return Jures
     */
    public function setInitialesJure(string $initialesJure): Jures
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

    /**
     * Set a
     *
     * @param integer $a
     *
     * @return Jures
     */
    public function setA(int $a): Jures
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
    public function setB(int $b): Jures
    {
        $this->b = $b;

        return $this;
    }

    /**
     * Get c
     *
     * @return int
     */
    public function getC(): int
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
    public function setC(int $c): Jures
    {
        $this->c = $c;

        return $this;
    }

    /**
     * Get d
     *
     * @return int
     */
    public function getD(): int
    {
        return $this->d;
    }

    /**
     * Set d
     *
     * @param integer $d
     *
     * @return Jures
     */
    public function setD(int $d): Jures
    {
        $this->d = $d;

        return $this;
    }

    /**
     * Get e
     *
     * @return int
     */
    public function getE(): int
    {
        return $this->e;
    }

    /**
     * Set e
     *
     * @param integer $e
     *
     * @return Jures
     */
    public function setE(int $e): Jures
    {
        $this->e = $e;

        return $this;
    }

    /**
     * Get f
     *
     * @return int
     */
    public function getF(): int
    {
        return $this->f;
    }

    /**
     * Set f
     *
     * @param integer $f
     *
     * @return Jures
     */
    public function setF(int $f): Jures
    {
        $this->f = $f;

        return $this;
    }

    /**
     * Get g
     *
     * @return int
     */
    public function getG(): int
    {
        return $this->g;
    }

    /**
     * Set g
     *
     * @param integer $g
     *
     * @return Jures
     */
    public function setG(int $g): Jures
    {
        $this->g = $g;

        return $this;
    }

    /**
     * Get h
     *
     * @return int
     */
    public function getH(): int
    {
        return $this->h;
    }

    /**
     * Set h
     *
     * @param integer $h
     *
     * @return Jures
     */
    public function setH(int $h): Jures
    {
        $this->h = $h;

        return $this;
    }

    /**
     * Get i
     *
     * @return int
     */
    public function getI(): int
    {
        return $this->i;
    }

    /**
     * Set i
     *
     * @param integer $i
     *
     * @return Jures
     */
    public function setI(int $i): Jures
    {
        $this->i = $i;

        return $this;
    }

    /**
     * Get j
     *
     * @return int
     */
    public function getJ(): int
    {
        return $this->j;
    }

    /**
     * Set j
     *
     * @param integer $j
     *
     * @return Jures
     */
    public function setJ(int $j): Jures
    {
        $this->j = $j;

        return $this;
    }

    /**
     * Get k
     *
     * @return int
     */
    public function getK(): int
    {
        return $this->k;
    }

    /**
     * Set k
     *
     * @param integer $k
     *
     * @return Jures
     */
    public function setK(int $k): Jures
    {
        $this->k = $k;

        return $this;
    }

    /**
     * Get l
     *
     * @return int
     */
    public function getL(): int
    {
        return $this->l;
    }

    /**
     * Set l
     *
     * @param integer $l
     *
     * @return Jures
     */
    public function setL(int $l): Jures
    {
        $this->l = $l;

        return $this;
    }

    /**
     * Get m
     *
     * @return int
     */
    public function getM(): int
    {
        return $this->m;
    }

    /**
     * Set m
     *
     * @param integer $m
     *
     * @return Jures
     */
    public function setM(int $m): Jures
    {
        $this->m = $m;

        return $this;
    }

    /**
     * Get n
     *
     * @return int
     */
    public function getN(): int
    {
        return $this->n;
    }

    /**
     * Set n
     *
     * @param integer $n
     *
     * @return Jures
     */
    public function setN(int $n): Jures
    {
        $this->n = $n;

        return $this;
    }

    /**
     * Get o
     *
     * @return int
     */
    public function getO(): int
    {
        return $this->o;
    }

    /**
     * Set o
     *
     * @param integer $o
     *
     * @return Jures
     */
    public function setO(int $o): Jures
    {
        $this->o = $o;

        return $this;
    }

    /**
     * Get p
     *
     * @return int
     */
    public function getP(): int
    {
        return $this->p;
    }

    /**
     * Set p
     *
     * @param integer $p
     *
     * @return Jures
     */
    public function setP(int $p): Jures
    {
        $this->p = $p;

        return $this;
    }

    /**
     * Get q
     *
     * @return int
     */
    public function getQ(): int
    {
        return $this->q;
    }

    /**
     * Set q
     *
     * @param integer $q
     *
     * @return Jures
     */
    public function setQ(int $q): Jures
    {
        $this->q = $q;

        return $this;
    }

    /**
     * Get r
     *
     * @return int
     */
    public function getR(): int
    {
        return $this->r;
    }

    /**
     * Set r
     *
     * @param integer $r
     *
     * @return Jures
     */
    public function setR(int $r): Jures
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Get s
     *
     * @return int
     */
    public function getS(): int
    {
        return $this->s;
    }

    /**
     * Set s
     *
     * @param integer $s
     *
     * @return Jures
     */
    public function setS(int $s): Jures
    {
        $this->s = $s;

        return $this;
    }

    /**
     * Get t
     *
     * @return int
     */
    public function getT(): int
    {
        return $this->t;
    }

    /**
     * Set t
     *
     * @param integer $t
     *
     * @return Jures
     */
    public function setT(int $t): Jures
    {
        $this->t = $t;

        return $this;
    }

    /**
     * Get u
     *
     * @return int
     */
    public function getU(): int
    {
        return $this->u;
    }

    /**
     * Set u
     *
     * @param integer $u
     *
     * @return Jures
     */
    public function setU(int $u): Jures
    {
        $this->u = $u;

        return $this;
    }

    /**
     * Get v
     *
     * @return int
     */
    public function getV(): int
    {
        return $this->v;
    }

    /**
     * Set v
     *
     * @param integer $v
     *
     * @return Jures
     */
    public function setV(int $v): Jures
    {
        $this->v = $v;

        return $this;
    }

    /**
     * Get w
     *
     * @return int
     */
    public function getW(): int
    {
        return $this->w;
    }

    /**
     * Set w
     *
     * @param integer $w
     *
     * @return Jures
     */
    public function setW(int $w): Jures
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

    /**
     * Add notesj
     *
     * @param Notes $notesj
     *
     * @return Jures
     */
    public function addNotesj(Notes $notesj): Jures
    {
        $this->notesj[] = $notesj;

        //On relie l'équipe à "une ligne note"
        $notesj->setJure($this);

        return $this;
    }

    /**
     * Remove notesj
     *
     * @param Notes $notesj
     */
    public function removeNotesj(Notes $notesj)
    {
        $this->notess->removeElement($notesj);
    }

    /**
     * Get notesj
     *
     * @return Collection
     */
    public function getNotesj()
    {
        return $this->notesj;
    }

    public function getNom(): string
    {
        return $this->getNomJure() . ' ' . $this->getPrenomJure();
    }

    /**
     * Get nomJure
     *
     * @return string
     */
    public function getNomJure(): string
    {
        return $this->nomJure;
    }

    /**
     * Set nomJure
     *
     * @param string $nomJure
     *
     * @return Jures
     */
    public function setNomJure(string $nomJure): Jures
    {
        $this->nomJure = $nomJure;

        return $this;
    }

    /**
     * Get prenomJure
     *
     * @return string
     */
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
    public function setPrenomJure(string $prenomJure): Jures
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


