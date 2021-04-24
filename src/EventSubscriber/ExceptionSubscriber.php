<?php
// src/EventSubscriber/ExceptionSubscriber.php
namespace App\EventSubscriber;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;


class ExceptionSubscriber implements EventSubscriberInterface


{     private $twig;
     
      public function __construct( Environment $environment){
           
            $this->twig=$environment;
   }
    
    
    
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 10],
                ['logException', 0],
                ['notifyException', -10],
            ],
        ];
    }

    public function processException(ExceptionEvent $event)
    {    
                    
        
    }

    public function logException(ExceptionEvent $event)
    {     
       
    }

    public function notifyException(ExceptionEvent $event)
    {    
      //return new RedirectResponse($this->urlGenerator->generate('https://www.olymphys.fr'));
    }
}