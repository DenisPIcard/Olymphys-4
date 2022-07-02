<?php

namespace App\Entity;

use App\Entity\Odpf\OdpfEditionsPassees;
use App\Entity\Odpf\OdpfEquipesPassees;
use App\Service\ImagesCreateThumbs;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use App\Service\FileUploader;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\PropertyNamer;
use App\Entity\Edition;

/**
 * Photos
 * @Vich\Uploadable
 * @ORM\Table(name="photos")
 * @ORM\Entity(repositoryClass="App\Repository\PhotosRepository")
 *
 */



class Photos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipesadmin")
     * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id",onDelete="CASCADE" )
     */
    private $equipe;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     * @Assert\Unique
     * @var string
     */
    private $photo;

    /**
     *
     *  @var File
     *  @Vich\UploadableField(mapping="photos", fileNameProperty="photo")
     *
     */
    private $photoFile;




    /**
     * @ORM\Column(type="string", length=125,  nullable=true)
     *
     * @var string
     */
    private $coment;

    /**
     * @ORM\Column(type="boolean",  nullable=true)
     *
     * @var boolean
     */
    private $national;


    /**
     *
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Edition::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $edition;

    /**
     * @ORM\ManyToOne(targetEntity=OdpfEditionsPassees::class, inversedBy="photos")
     */
    private ?OdpfEditionsPassees $editionspassees=null;

    /**
     * @ORM\ManyToOne(targetEntity=OdpfEquipesPassees::class)
     */
    private ?OdpfEquipesPassees  $equipepassee;

    public function __construct(){
        $this->setUpdatedAt(new DateTime('now'));



    }
    public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition($edition)
    {
        $this->edition=$edition;
        return $this;
    }

    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }


    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $photoFile
     */
    public function setPhotoFile(?File $photoFile = null) : void

    {
        $this->photoFile=$photoFile;
        if($this->photoFile instanceof UploadedFile){
            $this->updatedAt = new DateTime('now');
        }
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost

    }



    public function getId()
    {
        return $this->id;
    }

    public function getEquipe()
    {
        return $this->equipe;
    }

    public function setEquipe($equipe)
    {
        $this->equipe = $equipe;
        return $this;
    }

    public function getNational()
    {
        return $this->national;
    }

    public function setNational($national)
    {
        $this->national = $national;
        return $this;
    }

    public function personalNamer()    //permet à vichuploeder et à easyadmin de renommer le fichier, ne peut pas être utilisé directement
    {         $ed=$this->getEdition()->getEd();
        $equipe=$this->getEquipe();
        $centre=' ';
        $lettre_equipe='';
        if ($equipe->getCentre()){
            $centre=$equipe->getCentre()->getCentre();

        }
        $numero_equipe=$equipe->getNumero();
        if ($equipe->getLettre()){
            $lettre_equipe=$equipe->getLettre();
        }
        $national=$this->getNational();
        $nom_equipe=$equipe->getTitreProjet();
        $slugger = new AsciiSlugger();
        $nom_equipe=$slugger->slug($nom_equipe)->toString();
      /*  $nom_equipe= str_replace("à","a",$nom_equipe);
        $nom_equipe= str_replace("ä","a",$nom_equipe);
        $nom_equipe= str_replace("â","a",$nom_equipe);
        $nom_equipe= str_replace("ù","u",$nom_equipe);
        $nom_equipe= str_replace("è","e",$nom_equipe);
        $nom_equipe= str_replace("é","e",$nom_equipe);
        $nom_equipe= str_replace("ë","e",$nom_equipe);
        $nom_equipe= str_replace("ê","e",$nom_equipe);
        $nom_equipe= str_replace("ô","o",$nom_equipe);
        $nom_equipe= str_replace("?","",$nom_equipe);
        $nom_equipe= str_replace("ï","i",$nom_equipe);
        $nom_equipe= str_replace(" ","_",$nom_equipe);
        $nom_equipe= str_replace(",","_",$nom_equipe);
        $nom_equipe= str_replace(":","_",$nom_equipe);

        setLocale(LC_CTYPE,'fr_FR');


        $nom_equipe = iconv('UTF-8','ASCII//TRANSLIT',$nom_equipe);
        //$nom_equipe= str_replace("'","",$nom_equipe);
        //$nom_equipe= str_replace("`","",$nom_equipe);

        //$nom_equipe= str_replace("?","",$nom_equipe);*/
        if ($national == FALSE){
            $fileName=$ed.'-'.$centre.'-eq-'.$numero_equipe.'-'.$nom_equipe.'.'.uniqid();
        }
        if ($national == TRUE){
            $fileName=$ed.'-CN-eq-'.$lettre_equipe.'-'.$nom_equipe.'.'.uniqid();
        }

        return $fileName;
    }
    public function directoryName(): string
    {  $path='/';
        if ($this->edition!==null){
            $path= $this->edition->getEd().'/photoseq/';
        }

        return $path;
    }





    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire.
     */
    public function refreshUpdated()
    {
        $this->setUpdatedAt(new DateTime());
    }


    public function setUpdatedAt($date)
    {
        $this->updatedAt = $date;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    public function getComent()
    {
        return $this->coment;
    }

    public function setComent($coment)
    {
        $this->coment = $coment;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function createThumbs( ){

        $imagesCreateThumbs=new ImagesCreateThumbs();
        $imagesCreateThumbs->createThumbs($this);
        return $this;

    }

    public function getEditionspassees(): ?OdpfEditionsPassees
    {
        return $this->editionspassees;
    }

    public function setEditionspassees(?OdpfEditionsPassees $editionspassees): self
    {
        $this->editionspassees = $editionspassees;

        return $this;
    }

    public function getEquipepassee(): ?OdpfEquipesPassees
    {
        return $this->equipepassee;
    }

    public function setEquipepassee(?OdpfEquipesPassees $equipepassee): self
    {
        $this->equipepassee = $equipepassee;

        return $this;
    }

}

