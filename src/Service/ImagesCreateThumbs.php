<?php

namespace App\Service;

use App\Entity\Odpf\OdpfImagescarousels;
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
        if ($image instanceof OdpfImagescarousels ) {

            $path='odpf-images/imagescarousels/';
            $fileImage = $path.$image->getName();
            /*    dd($image);
            $imcarousel=true;
             $imagejpg = imagecreatefromjpeg($image->getImageFile());

             $imageOrigpath = $path . $image->getName();
            try{
                $headers = exif_read_data($image->getImageFile());

            }
            catch(\Exception $error ){
                $widthOrig=imagesx($imagejpg);
                $heightOrig=imagesy($imagejpg);

            }*/
            $imagetmp=new Imagick($image->getName());
            dd($fileImage);
            $imagetmp->readImage($fileImage);
            $heightOrig=$imagetmp->getImageHeight();
            $widthOrig=$imagetmp->getImageWidth();
            $percent=200/$heightOrig;
            $imagetmp->cropThumbnailImage($widthOrig*$percent, 200);
            $imagetmp->writeImage($fileImage);
         }


        if ($image instanceof Photos) {
            $imcarousel=false;
            $imagejpg = imagecreatefromjpeg($image->getPhotoFile());
            $path = 'odpf-archives/' . $image->getEdition()->getEd() . '/photoseq/';
            $pathThumb = $path . 'thumbs/';
            $imageOrigpath = $path . $image->getPhoto();

            try{
                $headers = exif_read_data($image->getPhotoFile());

                   }
            catch(\Exception $error ){
                   $widthOrig=imagesx($imagejpg);
                   $heightOrig=imagesy($imagejpg);

                }
         }



            if ((isset($headers['COMPUTED'])) and !isset($headers['Orientation']) ){
                $imcarousel== false?$imageOrig = imagecreatefromjpeg($image->getPhotoFile()):$imageOrig = imagecreatefromjpeg($image->getImageFile());
                $widthOrig=$headers['COMPUTED']['Width'];
                $heightOrig=$headers['COMPUTED']['Height'];
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
                $imcarousel==false?imagejpeg($thumb, $pathThumb.'/'.$image->getPhoto()):imagejpeg($thumb, $pathThumb);
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
                $imcarousel==false?$imageOrig->writeImage($pathThumb . $image->getPhoto()):$imageOrig->writeImage($pathThumb . $image->getName());
            }

        }





}