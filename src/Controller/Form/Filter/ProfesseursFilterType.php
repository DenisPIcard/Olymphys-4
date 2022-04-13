<?php

namespace App\Controller\Form\Filter;

use Doctrine\ORM\QueryBuilder;
use  EasyCorp\Bundle\EasyAdminBundle\Form\Type\FiltersFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ProfesseursFilterType extends FiltersFormType
{


    private $ession;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;

    }

    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata)
    {

        $datas = $form->getParent()->getData();


        if (isset($datas['edition'])) {

            $queryBuilder
                ->select('App:Edition')
                ->where('entite.edition =:edition')
                ->setParameter('edition', $datas['edition']);

        }


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_label' => [
                'Edition' => 'edition',

                // ...
            ],
        ]);

    }

    public function getParent()
    {
        return EntityType::class;
    }


}



