<?php

namespace App\Service;

use App\Entity\Photos;
use EasyCorp;
use Exception;
use Imagick;
use ImagickException;


class ImagesCreateThumbs
{
    /**
     * @throws ImagickException
     */
    public function createThumbs($image)
    {
        /* if ($image instanceof OdpfImagescarousels ) {
             $imagejpg = imagecreatefromjpeg($image->getImageFile());
             $headers = exif_read_data($image->getImageFile());
             $path='odpf-images/imagescarousels/';
             $paththumb = $path.$image->getName();
         }*/


        if ($image instanceof Photos) {
            $imagejpg = imagecreatefromjpeg($image->getPhotoFile());
            $path = 'odpf-archives/'.$image->getEdition()->getEd().'/photoseq/';
            $pathThumb = $path . 'thumbs/';
            $imageOrigpath = $path . $image->getPhoto();
            $testHeader=true;
           try{
            $headers = exif_read_data($image->getPhotoFile());

               }
           catch(\Exception $error ){
               $testHeader=false;


            }



            if (((isset($headers['COMPUTED'])) and !isset($headers['Orientation'])) or ($testHeader==false) ) {
                $imageOrig = imagecreatefromjpeg($image->getPhotoFile());
                if($testHeader==true){
                    $widthOrig = $headers['COMPUTED']['Width'];
                    $heightOrig = $headers['COMPUTED']['Height'];
                }
                else {
                    $widthOrig=imagesx($imagejpg);
                    $heightOrig=imagesy($imagejpg);
                 }
                $percent=200/$heightOrig;
                if($heightOrig/$widthOrig<0.866){
                    $widthOpt=$heightOrig/0.866;
                    $Xorig=($widthOrig-$widthOpt)/2;
                    $Yorig=0;
                    $image_opt= imagecreatetruecolor( $widthOpt,$heightOrig);

                    imagecopy($image_opt,$imageOrig,0,0,$Xorig,$Yorig,$widthOpt,$heightOrig);
                    $widthOrig=$widthOpt;
                }
                else{
                    $image_opt =$imageOrig;
                }
                $new_width = $widthOrig * $percent;
                $new_height = $heightOrig * $percent;

                $thumb = imagecreatetruecolor($new_width, $new_height);

                imagecopyresampled($thumb,$image_opt, 0, 0, 0, 0, $new_width, $new_height, $widthOrig, $heightOrig);
                imagejpeg($thumb, $pathThumb.'/'.$image->getPhoto());
            }

            elseif ((isset($headers['COMPUTED'])) and isset($headers['Orientation']) ) {

                $imageOrig = new Imagick($imageOrigpath);

                $exif = $imageOrig->getImageProperties("exif:*");
                //dd($exif);

                if (isset($exif['exif:PixelXDimension'])){
                    $widthOrig = $exif['exif:PixelXDimension'];
                    $heightOrig = $exif['exif:PixelYDimension'];

                }
                else{
                    $widthOrig=imagesx($imagejpg);
                    $heightOrig=imagesy($imagejpg);
                }
                if($heightOrig/$widthOrig<0.866){
                    $widthOpt=$heightOrig/0.866;
                    $Xorig=($widthOrig-$widthOpt)/2;
                    $Yorig=0;
                    $widthOrig=$widthOpt;
                }
                if (isset($exif['exif:Orientation'])) {
                    $orientationOrig = $exif['exif:Orientation'];


                    $dim = max($widthOrig, $heightOrig);
                    try {
                        switch ($orientationOrig) {
                            case  1 : // la photo n'a pas de rotationn
                                $percent = 200 / $heightOrig; // on impose une hauteur du thumb de 200 px
                                $imageOrig->cropThumbnailImage($widthOrig * $percent, 200);

                                break;
                            case  6 : //la photo est en mode portrait la mais la +grande dimension est considéré comme la hauteur avec une rotation de 90°
                                $percent = 200 / $widthOrig;
                                $imageOrig->cropThumbnailImage(200, $heightOrig * $percent);
                                $imageOrig->setImageProperty('exif:Orientation', '6');
                                $imageOrig->rotateImage("black", 90);
                                break;
                            case  8 : //la photo est en mode portrait la +grande dimension est la hauteur  avec une rotation de 270°
                                $percent = 200 / $widthOrig;
                                $imageOrig->cropThumbnailImage(200, $heightOrig * $percent);
                                $imageOrig->rotateImage("black", 270);
                                break;

                        }
                    } catch (Exception $e) {

                    }
                }
                else {

                    $widthOrig > $heightOrig? $percent = 200 / $widthOrig:$percent = 200 / $heightOrig;
                    $imageOrig->cropThumbnailImage($widthOrig * $percent, 200);

                }
                $imageOrig->writeImage($pathThumb . $image->getPhoto());
            }

        }


    }


}