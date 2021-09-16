<?php

declare(strict_types=1);

namespace App\EventSubscriber;
use App\Entity\Fichiersequipes;
use App\Service\MessageFlashBag;
use App\Service\valid_fichiers;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use PHPUnit\Runner\BeforeTestHook;
use PHPUnit\Util\Xml\Validator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response ;



final class EasyAdminListener implements EventSubscriberInterface
{   private $session;
    private $flashBag;
    private $validator;
    private $parameterBag;

    public function __construct( SessionInterface $session,MessageFlashBag $flashBag,ValidatorInterface $validator,ParameterBagInterface $parameterBag)
    {
        $this->session=$session;
        $this->flashBag= $flashBag;
        $this->validator=$validator;
        $this->parameterBag=$parameterBag;

    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => ['flashMessageAfterPersist'],
            BeforeTestHook::class=>['beforePersistPhotos'],
            BeforeEntityPersistedEvent::class=>['validFichier'],
            BeforeEntityUpdatedEvent::class=>['validFichier']
        ];
    }

    public function flashMessageAfterPersist(AfterEntityPersistedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Fichiersequipes) {

            $this->flashBag->addSuccess('Le fichier a bien été déposé');
            //dd($this->session);
        }
    }




    public function beforePersistPhotos(BeforeEntityPersistedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Photos) {
            dd($event);


        }
    }
    public function validFichier( BeforeEntityUpdatedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Fichiersequipes) {

            $validFichier=new valid_fichiers($this->validator,$this->parameterBag);

            $message = $validFichier->validation_fichiers($event->getEntityInstance()->getFichierFile(),$event->getEntityInstance()->getTypefichier(),new \DateTime('now'));
            if ($message!==null){


            }

        }
    }

}
