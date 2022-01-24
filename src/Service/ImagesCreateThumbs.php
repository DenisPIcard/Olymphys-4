<?php

namespace App\Service;

use App\Entity\Odpf\OdpfImagescarousels;
use App\Entity\Photos;
use EasyCorp;


class ImagesCreateThumbs
{
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


            $headers = exif_read_data($image->getPhotoFile());
            $path = 'upload/photos/thumbs/';
            $pathThumb = $path . $image->getPhoto();
        } elseif ($image instanceof OdpfImagescarousels) {
            $imagejpg = imagecreatefromjpeg($image->getImageFile());
            $headers = exif_read_data($image->getImageFile());
            $path = 'odpf-images/imagescarousels/';
            $pathThumb = $path . $image->getName();
        }
        //dd($headers);//si la photo a été retouchée,l'EXIF risque d'être incomplet
        isset($headers['ExifImageWidth']) ? $width_orig = $headers['ExifImageWidth'] : $width_orig = imagesx($imagejpg);
        isset($headers['ExifImageLength']) ? $height_orig = $headers['ExifImageLength'] : $height_orig = imagesy($imagejpg);


        if (isset($headers['Orientation'])) {
            if (($headers['Orientation'] == '6') and ($width_orig > $height_orig)) {
                $image = imagerotate($imagejpg, 270, 0);
                $widthtmp = $width_orig;
                $width_orig = $height_orig;
                $height_orig = $widthtmp;
            }
            if (($headers['Orientation'] == '8') and ($width_orig > $height_orig)) {
                $image = imagerotate($image, 90, 0);
                $widthtmp = $width_orig;
                $width_orig = $height_orig;
                $height_orig = $widthtmp;
            }
        }
        if ($height_orig / $width_orig < 0.866) {
            $width_opt = $height_orig / 0.866;
            $Xorig = ($width_orig - $width_opt) / 2;
            $Yorig = 0;
            $image_opt = imagecreatetruecolor($width_opt, $height_orig);
            imagecopy($image_opt, $imagejpg, 0, 0, $Xorig, $Yorig, $width_opt, $height_orig);
            $width_orig = $width_opt;
        } else {
            $image_opt = $imagejpg;
        }
        $dim = max($width_orig, $height_orig);
        $percent = 200 / $height_orig;
        $new_width = $width_orig * $percent;
        $new_height = $height_orig * $percent;
        $thumb = imagecreatetruecolor($new_width, $new_height);


        imagecopyresampled($thumb, $image_opt, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
        imagejpeg($thumb, $pathThumb);
        if (isset($_REQUEST['crudAction'])) {
            return;
        }

    }


}