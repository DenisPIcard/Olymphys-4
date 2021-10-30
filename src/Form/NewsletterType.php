<?php

namespace App\Form;

use App\Entity\Newsletter;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\FOSCKEditorTypeConfigurator;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class NewsletterType extends AbstractType
{/**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  $texteini=$options['textini'];
       $builder->add('name',TextType::class)
            ->add('texte', CKEditorType::class, [
            'label' => 'Saisir le texte de la newsletter',
            'data' => $texteini,
             'input_sync' => true
        ])
            ->add('destinataires', ChoiceType::class,[

                'choices'=>['Tous'=>'Tous','Elèves'=>'Eleves','Professeurs'=>'Professeurs']
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider']);

    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Newsletter::class,
            'textini'=>null,
        ));
    }




}