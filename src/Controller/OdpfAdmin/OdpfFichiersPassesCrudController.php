<?php

namespace App\Controller\OdpfAdmin;

use App\Entity\Odpf\OdpfEquipesPassees;
use App\Entity\Odpf\OdpfFichierspasses;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;

class OdpfFichiersPassesCrudController extends AbstractCrudController
{   private AdminContextProvider $adminContextProvider;
    public function __construct(AdminContextProvider $adminContextProvider){
        $this->adminContextProvider= $adminContextProvider;

    }
    public static function getEntityFqcn(): string
    {
        return OdpfFichierspasses::class;
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        $context = $this->adminContextProvider->getContext();
        $typefichier = $context->getRequest()->query->get('typefichier');
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.typefichier <=:type')
            ->setParameter('type', $typefichier )
            ->leftJoin('entity.equipepassee','eq')
            ->addOrderBy('eq.edition','DESC')
            ->addOrderBy('entity.equipepassee','ASC');
        return $qb;
    }
    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
