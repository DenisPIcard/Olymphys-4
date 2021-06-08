<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserRegistrationFormType;
use App\Form\ProfileType;
use App\Form\ResettingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Mailer;

class SecurityController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session=$session;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
       // dd($lastUsername);
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \Exception('Sera intercepté avant d\'en arriver là !');
    }
   
    protected function renderLogin(array $data)
    {
        return $this->render('security/login.html.twig', $data);
    }
    
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $rneRepository=$this->getDoctrine()->getManager()->getRepository('App:Rne');
        
        // création du formulaire
        $user = new User();
        // instancie le formulaire avec les contraintes par défaut, + la contrainte registration pour que la saisie du mot de passe soit obligatoire
        $form = $this->createForm(UserRegistrationFormType::class, $user,[
           'validation_groups' => array('User', 'registration'),
        ]);        
        $form->handleRequest($request);  
        if ($form->isSubmitted() && $form->isValid()) {

            $rne=$form->get('rne')->getData();

            if ($rneRepository->findOneBy(['rne'=>$rne])==null){
                $request->getSession()
                                ->getFlashBag()
                                ->add('alert', 'Ce n° RNE n\'est pas valide !') ;   
          
                return   $this->redirectToRoute('register');
            }
            $rneId=$rneRepository->findBy(['rne'=>$rne]);
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRne($rne);
            $user->setRneId($rneId[0]);
            //inactive l'User en attente de la vérification du mail
            $user->setIsActive(0);
            $user->setToken($tokenGenerator->generateToken());
            // enregistrement de la date de création du token
            $user->setPasswordRequestedAt(new \Datetime());
            $user->setCreatedAt(new \Datetime());
           /* if ($this->session->get('resetpwd')==true){
                $user->setLastVisit(new \datetime('now'));
                $this->session->set('resetpwd',null);
            }
            */
            // Enregistre le membre en base
            $em = $this->getDoctrine()->getManager();
            $em->persist($user); 
            $em->flush();
            $mailer->sendVerifEmail($user);
            $request->getSession()->getFlashBag()->add('success', "Un mail va vous être envoyé afin que vous puissiez finaliser votre inscription. Le lien que vous recevrez sera valide 24h.");

            return $this->redirectToRoute("core_home");

        }
        return $this->render('register/register.html.twig',
            array('form' => $form->createView())
        );
    }

         // si supérieur à 24h, retourne false
    // sinon retourne false
    private function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null)
        {
            return false;        
        }
        
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 60 * 24;
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }
    
   
    /**
     * 
     * @Route("/verif_mail/{id}/{token}", name="verif_mail")
     * 
     */
    public function verifMail(User $user, Request $request, Mailer $mailer, string $token)
    {
        $rneRepository=$this->getDoctrine()->getManager()->getRepository('App:Rne');
        $rne=$user->getRne();
        // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 24h
      
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            $this->redirectToRoute('login');
        }
        
            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setIsActive(1);
            $user->setUpdatedAt(new \Datetime());
            $user->setLastVisit(new \Datetime());
            $user->setRoles(['ROLE_PROF']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $rne=$user->getRne();
            $rne_obj=$rneRepository->findOneByRne(['rne'=>$rne]);
            $mailer->sendMessage($user,$rne_obj);
            $request->getSession()->getFlashBag()->add('success', "Votre inscription est terminée, vous pouvez vous connecter.");

            return $this->redirectToRoute('login');

        
    }
    
     /**
     * @Route("/forgottenPassword", name="forgotten_password")
     */
    public function forgottenPassword(Request $request, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ]
            ])
            ->getForm();
            $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findOneByEmail($form->getData()['email']);
          
            // aucun email associé à ce compte.
            if (!$user) {
                $request->getSession()->getFlashBag()->add('alert', 'Cet email ne correspond pas à un compte.');
                
                return $this->redirectToRoute('forgotten_password');
            } 

            // création du token
            $user->setToken($tokenGenerator->generateToken());
            // enregistrement de la date de création du token
            $user->setPasswordRequestedAt(new \Datetime());
            $em->persist($user);
            $em->flush();

            $email=(new TemplatedEmail())
                    ->from(new Address('info@olymphys.fr','Équipe Olymphys'))
                    ->to(new Address($user->getEmail(), $user->getNom()))
                    ->subject('Renouvellement du mot de passe')
                    ->htmlTemplate('email/password_mail.html.twig')
                    ->context([
                        'user' => $user,
                        ]);
            $mailer->send($email);
            $request->getSession()->getFlashBag()->add('success', "Un mail va vous être envoyé afin que vous puissiez renouveler votre mot de passe. Le lien que vous recevrez sera valide 24h.");

            return $this->redirectToRoute("core_home");
        }

        return $this->render('security/password_request.html.twig', [
            'passwordRequestForm' => $form->createView(),'resetpwd'=>$this->session->get('resetpwd')
        ]);
    }
    
    /**
     * @Route("/reset_password/{id}/{token}", name="reset_password")
     */
    public function resetPassword(User $user, Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
 
       // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form['plainPassword']->getData()));
            $plainPassword = $form->getData();

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setUpdatedAt(new \datetime('now'));
            $user->setLastVisit(new \datetime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->session->set('resetpwd',null);
            $request->getSession()->getFlashBag()->add('success', "Votre mot de passe a été renouvelé.");

            return $this->redirectToRoute('login');

        }

        return $this->render('security/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView()
        ]);
    }


}
