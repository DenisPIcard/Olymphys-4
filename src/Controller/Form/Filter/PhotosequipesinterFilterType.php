<?php

namespace App\Controller\Form\Filter;

use Doctrine\ORM\QueryBuilder;
use  EasyCorp\Bundle\EasyAdminBundle\Form\Type\FiltersFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class PhotosequipesinterFilterType extends FiltersFormType
{
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;

    }

    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata)
    {


        $datas = $form->getParent()->getData();
        //dd( $datas);
        if (null !== $datas['edition']) {

            $queryBuilder->andWhere('entity.edition =:edition')
                ->andWhere('entity.national =:national')
                ->setParameter('national', 'FALSE')
                ->setParameter('edition', $datas['edition']);
            $this->session->set('edition_titre', $datas['edition']->getEd());
        }
        if (null !== $datas['centre']) {

            $queryBuilder->andWhere('eq.centre=:centre')
                ->setParameter('centre', $datas['centre'])
                ->andWhere('entity.national =:national')
                ->setParameter('national', 'FALSE');

        }


        if (null != $datas['equipe']) {

            $queryBuilder->andWhere('entity.equipe =:equipe')
                ->setParameter('edition', $datas['equipe']->getEdition())
                ->setParameter('equipe', $datas['equipe']);
            $this->session->set('edition_titre', $datas['equipe']->getEdition()->getEd());

        }


        return $queryBuilder;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_label' => [
                'Edition' => 'edition',
                'Equipe' => 'equipe'
                // ...
            ],
        ]);

    }

    public function getParent()
    {
        return EntityType::class;
    }


}


