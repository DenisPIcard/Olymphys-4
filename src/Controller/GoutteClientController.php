<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class GoutteClientController extends AbstractController
{
    /**
     * @Route("/goutte/client", name="goutte_client")
     */
    public function index(): Response
    {
        $url = "https://odpf.org/la-xxvie-2019.html";
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $nb_liens = $crawler->filter('ul.thesis-list li')->count();
        //dd($nb_liens);

        $i=0;
        if($nb_liens > 0)
            {
               // $text = $crawler->filter('ul.thesis-list li a')->text();
               // extrait le texte du a

               /* $nodeValues = $crawler->filter('ul.thesis-list li a')->each(function (Crawler $node) {
                    return $node->text();
                });
                dd($nodeValues);
               */
                //extrait tous les textes
               /* $class = $crawler->filter('ul.thesis-list li a')->attr('href');
                dd($class);*/
                //extrait le contenu du lien

        } else {
            $liens[0]= "Pas de liens";
        }
        //dd($liens);
        return $this->render('goutte_client/crawl.html.twig', $liens
        );
    }
}
