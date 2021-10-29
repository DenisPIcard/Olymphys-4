<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Entity\User;
use App\Form\NewsletterType;
use App\Message\SendNewsletterMessage;
use App\Service\Mailer;
use App\Service\SendNewsletterService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Address;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class NewsletterController extends AbstractController
{   private $em;
    private $session;
    public function __construct(EntityManagerInterface $em, SessionInterface $session){
        $this->em=$em;
        $this->session=$session;
    }
     /**
     * @Route("/newsletter/write,{id}", name="newsletter_write")
     * @IsGranted ("ROLE_SUPER_ADMIN")
     */
    public function write(Request $request,$id)
    {
        if ( $id==0) {
            $newsletter = new Newsletter();
            $textini='';
        }
        else{

            $newsletter=$this->em->getRepository('App:Newsletter')->find(['id'=>$id]);
            if ($newsletter->getEnvoyee() == false){
             $this->redirectToRoute('newsletter_liste');

            }
            $textini=$newsletter->getTexte();

        }
        $form=$this->createForm(NewsletterType::class,$newsletter,['textini'=>$textini]);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){


            $this->em->persist($newsletter);
            $this->em->flush();

            return $this->redirectToRoute('newsletter_liste');
        }

        return $this->render('newsletter/write.html.twig',['form'=>$form->createView()]);

    }

    /**
    * @Route("/newsletter/delete", name="newsletter_delete")
    * @IsGranted ("ROLE_SUPER_ADMIN")
    */
    public function delete(Request $request)

    {

        $id= $request->query->get('myModalID');
        $newsletter=$this->getDoctrine()->getRepository('App:Newsletter')->find(['id'=>$id]) ;
        if($newsletter){
            $this->em->remove($newsletter);
            $this->em->flush();

        }

        return $this->redirectToRoute('newsletter_liste');


    }



    /**
     * @Route("/newsletter/liste", name="newsletter_liste")
     * @IsGranted ("ROLE_SUPER_ADMIN")
     */
    public function liste(Request $request)
    {   $newsletters=[];

      $newsletters=$this->em->getRepository('App:Newsletter')->createQueryBuilder('n')
                            ->select()
                            ->orderBy('n.createdAt','DESC')
                            ->getQuery()->getResult();

      return $this->render('newsletter/liste.html.twig',['newsletters'=>$newsletters]);

    }
    /**
     * @Route("/newsletter/send,{id}", name="newsletter_send")
     * @IsGranted ("ROLE_SUPER_ADMIN")
     */
    public function send(Request $request,int $id,  MessageBusInterface $messageBus)
    {
        $newsletter=$this->em->getRepository('App:Newsletter')->find(['id'=>$id]);

        $newsletter->setSendAt(new \DateTimeImmutable('now'));
        //$newsletter->setEnvoyee(true);
        $this->em->persist($newsletter);
        $this->em->flush();
        $repositoryUser=$this->em->getRepository('App:User');
        $qb=$repositoryUser->createQueryBuilder('p');
        $qb1=$this->em->getRepository('App:User')->createQueryBuilder('u')
            ->where('u.newsletter = 1')
            ->addOrderBy('u.nom','ASC');
        $listeProfs=$qb1->getQuery()->getResult();

        foreach($listeProfs as $prof){
                //$messageBus->dispatch($newsletterSend->send($prof->getId(), $newsletter->getId()));
            $messageBus->dispatch(new SendNewsletterMessage($prof->getId(), $newsletter->getId()));
                // system('"dir"');

        }

        return $this->redirectToRoute('newsletter_liste');


    }
    public function messengerConsume(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'messenger:consume async',

        ]);

        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()

        // return new Response(""), if you used NullOutput()
        return new Response();
    }
    /**
     *
     * @Route ("/newsletter/desinscription,{userid}", name="newsletter_desinscription")
     */
    public function desinscription(Request $request,User $userid,MailerInterface $mailer)
    {

        $token = hash('sha256', uniqid());

        $userid->setToken($token);
        $em=$this->getDoctrine()->getManager();
        $em->persist($userid);
        $em->flush();
        $email = (new TemplatedEmail())
            ->from('info@olymphys.fr')
            ->to($userid->getEmail())
            ->subject('Désincription de la newsletter OdPF')
            ->htmlTemplate('newsletter/desinscription_newsletter.html.twig')
            ->context(['token'=>$token, 'user'=>$userid]);

        $mailer->send($email);
        $request->getSession()->getFlashBag()->add('alert', "un mail vient de vous être envoyé pour que vous confirmiez votre désinscription aux newletter des OdPF");

        return $this->redirectToRoute('core_home');
    }

    /**
     *
     * @Route ("/newsletter/confirmDesinscription/{token}/{userid}", name="newsletter_confirm_desinscription")
     */
    public function confirmDesinscription(Request $request,$token,User $userid)
    {

        if ($userid->getToken()==$token){
            $userid->setNewsletter(false);
            $userid->setToken(null);
            $em=$this->getDoctrine()->getManager();
            $em->persist($userid);
            $em->flush();
        }
        $request->getSession()->getFlashBag()->add('success', "Vous êtes désinscrit(e) de la newsletter des OdPF");
        return $this->redirectToRoute('core_home');
    }


}
