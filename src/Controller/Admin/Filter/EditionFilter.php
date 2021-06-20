<?php

namespace App\Controller\Admin\Filter;

use Doctrine\ORM\QueryBuilder;
use App\Form\Type\Admin\EditionFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Edition ;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;



class EditionFilter implements FilterInterface
{   use FilterTrait;
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(EditionFilterType::class);
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        if ('today' === $filterDataDto->getValue()) {
            $queryBuilder->andWhere(sprintf('%s.%s = :today', $filterDataDto->getEntityAlias(), $filterDataDto->getProperty()))
                ->setParameter('today', (new \DateTime('today'))->format('Y-m-d'));
        }

        // ...
    }


}



