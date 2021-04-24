<?php
// src/EventListener/LoginListener.php
namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginSubscriber   implements EventSubscriberInterface
{
  private $em;
  private $session;
   public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session=$session;
    }

  public static function getSubscribedEvents()
    {
        return [
            
            
           SecurityEvents::INTERACTIVE_LOGIN=> [
              [ 'onSecurityAuthenticationSuccess', 10]
                 ],
            ];
    }

  public function onSecurityAuthenticationSuccess(InteractiveLoginEvent $event=null) {
    $user = $event->getAuthenticationToken()->getUser();
    
    if ($user instanceof User) {
      $lastVisit= $user->getLastVisit();
      if ($lastVisit == null){
        $this->session->set('resetpwd', true);
          
      }
       if ($lastVisit != null){
        $this->session->set('resetpwd', null);
        $user->setLastVisit(new \Datetime());
     
      
      
      $this->em->persist($user);
       $this->em->flush();}
    }
  }
}



