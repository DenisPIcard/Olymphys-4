<?php
namespace App\EventListener;

use App\Entity\Fichiersequipes;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

class FichiersListener
{
    function prePersist(Fichiersequipes $fichier,LifecycleEventArgs $event ) : void
    {
        $fichier->setEdition($equipe =$fichier->getEquipe()->getEdition());

    }




}