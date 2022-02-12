<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;


class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class,['required'=> true,'label'=>'Votre pseudo'])
            ->add('email', RepeatedType::class, ['required'=> true,
                'mapped' => true,
                'type' => EmailType::class,
                'first_options' => array('label' => 'Votre courriel'),
                'second_options' => array('label' => 'Vérification du courriel'),
            ])
            ->add('nom', TextType::class,['required'=> true,'label'=>'Votre nom'])
            ->add('prenom', TextType::class,['required'=> true,'label'=>'Votre prénom'])
            ->add('plainPassword', RepeatedType::class, array('required'=> true,
                'mapped' => true,
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmer le mot de passe'),))
            //->add('nom',TextType::class)
            ->add('rne', TextType::class, ['required'=> true,
                'label' => 'Le code UAI de votre établissement, de la forme 0123456A)'])
            ->add('adresse', null, ['required'=> true, 'label'=>'Votre adresse (numéro +rue)'])
            ->add('ville', null, ['required'=> true, 'label'=>'Votre ville'])
            ->add('code', null, ['required'=> true,'label'=>'Votre code'])
            ->add('phone', null, ['required'=> true, 'label'=>'Votre téléphone, portable, de préférence',])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'required'=> true,
                        'label' => 'J\'accepte l\'enregistrement de ces données par le site Olymphys.'
                    ])
        ->add('Inscription',SubmitType::class,['label'=>'Vous inscrire']);
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Article|null $data */
                $data = $event->getData();

                if (!$data) {
                    return;
                }
                //dd($data);
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
