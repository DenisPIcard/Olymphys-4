<?php
// src/EventListener/PhotosThumbsListener.php
namespace App\EventListener;

use App\Entity\Photos;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use App\Service\PhotoNamer;

class PhotosThumbsListener
{
// the entity listener methods receive two arguments:
// the entity instance and the lifecycle event

    public function postUpdate(Photos $photo,LifecycleEventArgs $event ) : void
    {
        $this->CreateThumb($photo);


    }
    public function preUpdate(Photos $photo,LifecycleEventArgs $event ):void
    {

        if (isset($event->getEntityChangeSet()['equipe'])) {
            $namer = new PhotoNamer();
            $oldName = $event->getObject()->getPhoto();
            $newName = $namer->PhotoName($event->getEntityChangeSet()['equipe'][1], $photo);
            rename('upload/photos/' . $oldName, 'upload/photos/' . $newName);
            rename('upload/photos/thumbs/' . $oldName, 'upload/photos/thumbs/' . $newName);
            $photo->setPhoto($newName);
        }

    }
    public function postPersist(Photos $photo,LifecycleEventArgs  $event): void
        {
            $this->CreateThumb($photo);


        }
    public function CreateThumb($photo)
    {
        if (null !== $photo->getPhotoFile()) {
            $headers = exif_read_data($photo->getPhotoFile());

            $image = imagecreatefromjpeg($photo->getPhotoFile());
            list($width_orig, $height_orig) = getimagesize($photo->getPhotoFile());


            if (isset($headers['Orientation'])) {
                if (($headers['Orientation'] == '6') and ($width_orig > $height_orig)) {
                    $image = imagerotate($image, 270, 0);

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

                imagecopy($image_opt, $image, 0, 0, $Xorig, $Yorig, $width_opt, $height_orig);
                $width_orig = $width_opt;
            } else {
                $image_opt = $image;
            }
            $dim = max($width_orig, $height_orig);
            $percent = 200 / $height_orig;
            $new_width = $width_orig * $percent;
            $new_height = $height_orig * $percent;

            $thumb = imagecreatetruecolor($new_width, $new_height);
            $paththumb = $photo->getPhotoFile()->getPath() . '/thumbs';
            imagecopyresampled($thumb, $image_opt, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
            imagejpeg($thumb, $paththumb . '/' . $photo->getPhoto());
        }
    }

}
