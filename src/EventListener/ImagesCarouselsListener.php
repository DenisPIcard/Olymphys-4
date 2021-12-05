<?php
namespace App\EventListener;





use App\Entity\OdpfImagescarousels;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ImagesCarouselsListener
{
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this listener only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof OdpfImagescarousels) {
            return;
        }
        $entity->createThumbs();

    }
}
