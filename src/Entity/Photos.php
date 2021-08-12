<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


use Symfony\Component\HttpFoundation\File\File;

use Vich\UploaderBundle\Mapping\Annotation as Vich;


use App\Entity\Edition;

/**
 * photos
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
        *
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
      * @ORM\ManyToOne(targetEntity="App\Entity\Edition"))
      * 
      * @ORM\JoinColumn(name="edition_id",  referencedColumnName="id" )
      */
     private $edition;
     
    
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
       * @var \DateTime
       */
    private $updatedAt;
    
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
    
    public function setPhoto(?string $photo)
    {   
        $this->photo = $photo;
         if ($photo) {


             list($width_orig, $height_orig) = getimagesize($this->getPhotoFile());
                         //$headers = exif_read_data($photo->getPhotoFile());
                         $dim=max($width_orig, $height_orig);


                         $percent = 200/$height_orig;



                         $new_width = $width_orig * $percent;
                         $new_height = $height_orig * $percent;
                          $image =imagecreatefromjpeg($this->getPhotoFile());
                            // Resample
                            $thumb = imagecreatetruecolor($new_width, $new_height);
                          // $filesystem=new Filesystem();
                            $paththumb ='upload/photos/thumbs';
                           //dd(getcwd());
                            imagecopyresampled($thumb,$image, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);


                          //dd($thumb);
                          imagejpeg($thumb, getcwd().'/'.$paththumb.'/'.$photo);

        }
        return $this;
    }

    
    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $photoFile
     */
      public function setPhotoFile(?File $photoFile = null) : void
            
    {  
        $this->photoFile=$photoFile;
        if ($this->photoFile instanceof UploadedFile){
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
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
 {         $editionEd=$this->equipe->getEdition()->getEd();
           $this->edition=$this->equipe->getEdition();
           $equipe=$this->equipe;
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
           $nom_equipe= str_replace("à","a",$nom_equipe);
           $nom_equipe= str_replace("ù","u",$nom_equipe);
           $nom_equipe= str_replace("è","e",$nom_equipe);
           $nom_equipe= str_replace("é","e",$nom_equipe);
           $nom_equipe= str_replace("ë","e",$nom_equipe);
           $nom_equipe= str_replace("ê","e",$nom_equipe);
           $nom_equipe= str_replace("ô","o",$nom_equipe);
           $nom_equipe= str_replace("?","",$nom_equipe);
           $nom_equipe= str_replace("ï","i",$nom_equipe);
            setLocale(LC_CTYPE,'fr_FR');
           
           
           $nom_equipe = iconv('UTF-8','ASCII//TRANSLIT',$nom_equipe);
           //$nom_equipe= str_replace("'","",$nom_equipe);
           //$nom_equipe= str_replace("`","",$nom_equipe);
            
           //$nom_equipe= str_replace("?","",$nom_equipe);     
           if ($national == FALSE){
           $fileName=$editionEd.'-'.$centre.'-eq-'.$numero_equipe.'-'.$nom_equipe.'.'.uniqid();
           }
           if ($national == TRUE){
           $fileName=$editionEd.'-CN-eq-'.$lettre_equipe.'-'.$nom_equipe.'.'.uniqid();
           }
           
           return $fileName;
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


   
    
}

