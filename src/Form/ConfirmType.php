<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;




use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfirmType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('OUI', SubmitType::class, ['label' => 'OUI'])
        ->add('NON', SubmitType::class, ['label' => 'NON']);
        
    }
    
    

}

