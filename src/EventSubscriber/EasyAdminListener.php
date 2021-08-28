<?php

declare(strict_types=1);

namespace App\EventSubscriber;
use App\Entity\Fichiersequipes;
use App\Service\MessageFlashBag;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


final class EasyAdminListener implements EventSubscriberInterface
{   private $session;
    private $flashBag;
    public function __construct( SessionInterface $session,MessageFlashBag $flashBag)
    {
        $this->session=$session;
        $this->flashBag= $flashBag;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => ['flashMessageAfterPersist'],
            AfterEntityUpdatedEvent::class => ['flashMessageAfterUpdate'],
            AfterEntityDeletedEvent::class => ['flashMessageAfterDelete'],
        ];
    }

    public function flashMessageAfterPersist(AfterEntityPersistedEvent $event)
    {
        if ($event->getEntityInstance() instanceof Fichiersequipes) {

            $this->flashBag->addSuccess('Le fichier a bien été déposé');
            //dd($this->session);
        }
    }

    public function flashMessageAfterUpdate(AfterEntityUpdatedEvent $event): void
    {
        $this->session->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.update', [
            '%name%' => (string) $event->getEntityInstance()->getFichier(),
        ], 'admin'));
    }

    public function flashMessageAfterDelete(AfterEntityDeletedEvent $event): void
    {
        $this->session->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.delete', [
            '%name%' => (string) $event->getEntityInstance()->getFichier(),
        ], 'admin'));
    }
}
