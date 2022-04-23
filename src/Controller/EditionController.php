<?php
namespace App\Controller;
use App\Entity\Edition;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Form\FormInterface;




class EditionController extends EasyAdminController
{
    // ...

    protected function createFiltersForm(string $entityName): FormInterface
    {    
        $form = parent::createFiltersForm($entityName);
        $form->add('ed', EntityType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('u')
            ->orderBy('u.ed', 'DESC');
    },
    'choice_label' => 'ed',
        ]);

        return $form;
    }
}

