<?php
namespace App\EventListener;

use App\Entity\Fichiersequipes;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Service\valid_fichiers;
use App\Service\FichierNamer;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class FichiersListener
{   private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    function prePersist(Fichiersequipes $fichier,LifecycleEventArgs $event ) : void
    {

        $fichier->setEdition($equipe =$fichier->getEquipe()->getEdition());

    }
    function preUpdate(Fichiersequipes $fichier,LifecycleEventArgs $event ) : void
    {  //dd($event);
        ;

        if ((isset($event->getEntityChangeSet()['equipe'])or(isset($event->getEntityChangeSet()['typefichier'])))) {
            $namer = new FichierNamer();
            if (isset($event->getEntityChangeSet()['typefichier'])){//Pour les mémoires et les annexes, inutile pour les autres fichiers
                $newnumtypefichier=$event->getEntityChangeSet()['typefichier'][1];
                $oldnumtypefichier=$event->getEntityChangeSet()['typefichier'][0];
                $newnumtypefichier=1? 0:0;
            }
            else{

                $newnumtypefichier=$fichier->getTypefichier();
                $newnumtypefichier=1? 0:0;
                $oldnumtypefichier=$newnumtypefichier;

            }
            if (isset($event->getEntityChangeSet()['equipe'])){
                $oldequipe=$event->getEntityChangeSet()['equipe'][0];
                $newequipe=$event->getEntityChangeSet()['equipe'][1];

            }
            else{
                $newequipe=$fichier->getEquipe();
            }
            $fichierFile=new UploadedFile('fichiers/'.$this->params->get('type_fichier')[$oldnumtypefichier].'/'.$fichier->getFichier(), $fichier->getFichier());
            $ext=$fichierFile->guessExtension();
            $oldName = $event->getObject()->getFichier();
            $newName = $namer->FichierName($newequipe, $newnumtypefichier,$fichier).'.'.$ext;
            rename('fichiers/' .$this->params->get('type_fichier')[$oldnumtypefichier].'/'.$oldName, 'fichiers/' . $this->params->get('type_fichier')[$newnumtypefichier].'/'.$newName);

            $fichier->setFichier($newName);
         }




    }

}