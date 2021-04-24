<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Rne;
use App\Entity\Equipesadmin;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Mailer
{   private $session;
    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig, SessionInterface $session)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session =$session;
    }

        public function sendMessage(User $user, Rne $rne_obj)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('info@olymphys.fr'))
            ->to('olymphys-11d237@inbox.mailtrap.io')
            ->subject('Inscription d\'un nouvel utilisateur')
            ->htmlTemplate('email/nouvel_utilisateur.html.twig')
            ->context([
                'user' => $user,
                'rne'=>$rne_obj
            ]);
        $this->mailer->send($email);
        return $email;
    }
    public function SendVerifEmail(User $user)
    {   $email = (new TemplatedEmail())
    ->from('info@olymphys.fr')
    ->to('olymphys-11d237@inbox.mailtrap.io')//new Address($user->getEmail())
    ->subject('Olymphys-Confirmation de votre inscription')

    // path of the Twig template to render
    ->htmlTemplate('email/bienvenue.html.twig')

    // pass variables (name => value) to the template
    ->context([
        'expiration_date' => new \DateTime('+24 hours'),
        'user' =>$user
    ]);
      $this->mailer->send($email);
          return $email;
    }



    public function sendConfirmFile(Equipesadmin $equipe, $type_fichier ){
     $email=(new Email())
                    ->from('info@olymphys.fr')
                    ->to('olymphys-11d237@inbox.mailtrap.io') //'webmestre2@olymphys.fr', 'Denis'
                    ->subject('Depot du '.$type_fichier.'de l\'équipe '.$equipe->getInfoequipe())
                    ->text('L\'equipe '. $equipe->getInfoequipe().' a déposé un fichier : '.$type_fichier);

       $this->mailer->send($email);
        return $email;

    }
     public function sendConfirmeInscriptionEquipe(Equipesadmin $equipe,User $user, $modif ){
     if($modif==false){
         $email=(new Email())
                    ->from('info@olymphys.fr')
                    ->to('olymphys-11d237@inbox.mailtrap.io') //'webmestre2@olymphys.fr', 'Denis'
                    ->subject('Inscription de l\'équipe  '.$equipe->getNumero().' par '.$user->getPrenomNom())
                    ->html('Bonjour<br>
                            Nous confirmons que '.$equipe->getIdProf1()->getPrenomNom().'(<a href="'.$user->getEmail().'">'.$user->getEmail().
                            '</a>) du lycée '.$equipe->getNomLycee().' de '.$equipe->getLyceeLocalite().' a inscrit une nouvelle équipe denommée : '.$equipe->getTitreProjet().
                            '<br> <br>Le comité national des Olympiades de Physique');
     }
      if($modif==true){
         $email=(new Email())
                    ->from('info@olymphys.fr')
                    ->to('olymphys-11d237@inbox.mailtrap.io') //'webmestre2@olymphys.fr', 'Denis'
                    ->subject('Modification de l\'équipe '.$equipe->getTitreProjet().' par '.$user->getPrenomNom())
                    ->html('Bonjour<br>'.
                           $equipe->getIdProf1()->getPrenomNom().'(<a href="'.$user->getEmail().'">'.$user->getEmail().
                            '</a>)  du lycée '.$equipe->getNomLycee().' de '.$equipe->getLyceeLocalite().'a modifié l\'équipe denommée : '.$equipe->getTitreProjet().
                            '<br> <br>Le comité national des Olympiades de Physique');
     }     
     
     
     
     
     
       $this->mailer->send($email);
        return $email;
    
    }
    
    
}

