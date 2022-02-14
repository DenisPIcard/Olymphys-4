<?php

namespace App\Entity;

use App\Service\ImagesCreateThumbs;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ImagickException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id=0;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipesadmin")
     * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id",onDelete="CASCADE" )
     */
    private Equipesadmin $equipe;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     * @Assert\Unique
     */
    private ?string $photo = null;

    /**
     *
     * @var File
     * @Vich\UploadableField(mapping="photos", fileNameProperty="photo")
     *
     */
    private ?File $photoFile;


    /**
     * @ORM\Column(type="string", length=125,  nullable=true)
     */
    private ?string $coment = null;

    /**
     * @ORM\Column(type="boolean",  nullable=true)
     *
     * @var boolean
     */
    private ?bool $national = false;


    /**
     *
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=Edition::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Edition $edition;

    public function __construct()
    {
        $this->setUpdatedAt(new DateTime('now'));


    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    /**
     * @param File|null $photoFile
     */
    public function setPhotoFile(File $photoFile = null): void

    {
        $this->photoFile = $photoFile;
        if ($this->photoFile instanceof UploadedFile) {
            $this->updatedAt = new DateTime('now');
        }
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost

    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto($photo): Photos
    {
        $this->photo = $photo;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function personalNamer(): ?string    //permet à vichuploeder et à easyadmin de renommer le fichier, ne peut pas être utilisé directement
    {
        $ed = $this->getEdition()->getEd();
        $equipe = $this->getEquipe();
        $centre = ' ';
        $lettre_equipe = '';
        if ($equipe->getCentre()) {
            $centre = $equipe->getCentre()->getCentre();

        }
        $numero_equipe = $equipe->getNumero();
        if ($equipe->getLettre()) {
            $lettre_equipe = $equipe->getLettre();
        }
        $national = $this->getNational();
        $nom_equipe = $equipe->getTitreProjet();
        $nom_equipe = str_replace("à", "a", $nom_equipe);
        $nom_equipe = str_replace("ù", "u", $nom_equipe);
        $nom_equipe = str_replace("è", "e", $nom_equipe);
        $nom_equipe = str_replace("é", "e", $nom_equipe);
        $nom_equipe = str_replace("ë", "e", $nom_equipe);
        $nom_equipe = str_replace("ê", "e", $nom_equipe);
        $nom_equipe = str_replace("ô", "o", $nom_equipe);
        $nom_equipe = str_replace("?", "", $nom_equipe);
        $nom_equipe = str_replace("ï", "i", $nom_equipe);
        $nom_equipe = str_replace(" ", "_", $nom_equipe);
        $nom_equipe = str_replace(":", "-", $nom_equipe);
        setLocale(LC_CTYPE, 'fr_FR');


        $nom_equipe = iconv('UTF-8', 'ASCII//TRANSLIT', $nom_equipe);
        //$nom_equipe= str_replace("'","",$nom_equipe);
        //$nom_equipe= str_replace("`","",$nom_equipe);

        //$nom_equipe= str_replace("?","",$nom_equipe);
        if ($national == FALSE) {
            $fileName = $ed . '-' . $centre . '-eq-' . $numero_equipe . '-' . $nom_equipe . '.' . uniqid();
        }
        if ($national == TRUE) {
            $fileName = $ed . '-CN-eq-' . $lettre_equipe . '-' . $nom_equipe . '.' . uniqid();
        }

        return $fileName;
    }

    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    public function setEdition($edition): Photos
    {
        $this->edition = $edition;
        return $this;
    }

    public function getEquipe(): ?Equipesadmin
    {
        return $this->equipe;
    }

    public function setEquipe($equipe): Photos
    {
        $this->equipe = $equipe;
        return $this;
    }

    public function getNational(): ?bool
    {
        return $this->national;
    }

    public function setNational($national): Photos
    {
        $this->national = $national;
        return $this;
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire.
     */
    public function refreshUpdated()
    {
        $this->setUpdatedAt(new DateTime());
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($date): Photos
    {
        $this->updatedAt = $date;

        return $this;
    }

    public function getComent(): ?string
    {
        return $this->coment;
    }

    public function setComent($coment): Photos
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

    /**
     * @throws ImagickException
     */
    public function createThumbs(): Photos
    {

        $imagesCreateThumbs = new ImagesCreateThumbs();
        $imagesCreateThumbs->createThumbs($this);
        return $this;

    }

}

