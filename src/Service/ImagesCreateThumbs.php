<?php

namespace App\Service;

use App\Entity\Odpf\OdpfImagescarousels;
use App\Entity\Photos;
use EasyCorp;
use Exception;
use Imagick;
use ImagickException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImagesCreateThumbs
{
    /**
     * @throws ImagickException
     */
    public function createThumbs($image)
    {
        if ($image instanceof OdpfImagescarousels ) {

            $path='odpf-images/imagescarousels/';
            $fileImage = $image->getImageFile();
            $imagetmp=new Imagick($fileImage);
            try {
                $imagetmp->readImage('odpf-images/imagescarousels/' . $image->getName());
                $heightOrig = $imagetmp->getImageHeight();
                $widthOrig = $imagetmp->getImageWidth();
                $percent = 200 / $heightOrig;
                $nllwidth = $widthOrig * $percent;
                $nllheight = 200;
                if ($widthOrig * $percent > 230) {
                    $nllwidth = 230;
                    $nllheight = $heightOrig * 230 / $widthOrig;
                }
                $imagetmp->cropThumbnailImage($nllwidth, $nllheight);
                $imagetmp->writeImage($fileImage);
                if ($widthOrig * $percent > 230) {
                    $y = (200 - $nllheight) / 2;
                    $imagetmp->readImage('odpf-images/imagescarousels/' . $image->getName());
                    $fondnoir = new Imagick('images/fond_noir_carousel.jpg');
                    $fondnoir->compositeImage($imagetmp, imagick::COMPOSITE_REPLACE, 0, $y);
                    $fondnoir->writeImage($fileImage);
                }
            }
            catch(\Exception $e){


            }
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