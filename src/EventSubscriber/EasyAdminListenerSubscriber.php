<?php

declare(strict_types=1);

namespace App\EventSubscriber;
use App\Entity\Fichiersequipes;
use App\Entity\Photos;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;


final class EasyAdminListenerSubscriber implements EventSubscriberInterface
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
            BeforeEntityPersistedEvent::class=>['persistvalidFichier'],
            BeforeEntityUpdatedEvent::class=>['updatevalidFichier']

        ];
    }

    public function flashMessageAfterPersist(AfterEntityPersistedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Fichiersequipes) {

            if ($this->flashBag->getAlert()!= []) {

                 $this->flashBag->addAlert('Veuillez déposer un fichier du bon format ! non non');
            } else {

                $this->flashBag->addSuccess('Le fichier a bien été déposé oui oui');
            }

        }
    }




    public function beforePersistPhotos(BeforeEntityPersistedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Photos) {
            dd($event);


        }
    }
    public function updatevalidFichier( BeforeEntityUpdatedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Fichiersequipes) {

            $validFichier=new valid_fichiers($this->validator,$this->parameterBag, $this->session);
            //$file = new UploadedFile($this->parameterBag->get('app.path.fichiers').'/'.$this->parameterBag->get('type_fichier')[$event->getEntityInstance()->getTypeFichier()].'/'.$event->getEntityInstance()->getFichier(),$event->getEntityInstance()->getFichier());


            $message = $validFichier->validation_fichiers($event->getEntityInstance()->getFichierfile(),$event->getEntityInstance()->getTypefichier(),$event->getEntityInstance()->getId());


                $this->session->set('messageeasy',$message);



        }
    }
    public function persistvalidFichier( BeforeEntityPersistedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Fichiersequipes) {

            $validFichier=new valid_fichiers($this->validator,$this->parameterBag, $this->session);

            $message = $validFichier->validation_fichiers($event->getEntityInstance()->getFichierFile(),$event->getEntityInstance()->getTypefichier(),$event->getEntityInstance()->getId());
            if ($message!==null){
                $this->session->set('messageeasy',$message);
            //http_redirect('');

            }

        }
        /*if ($event->getEntityInstance() instanceof Photos) {

            $validFichier=new valid_fichiers($this->validator,$this->parameterBag, $this->session);

            $message = $validFichier->validation_fichiers($event->getEntityInstance()->getPhotoFile(),8,null);
            if ($message!==null){
                    $this->session->set('messageeasy',$message);


            }

        }*/



    }
}
