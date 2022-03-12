<?php

namespace App\Controller\Admin;

use App\Entity\Edition;
use App\Entity\Odpf\OdpfEditionsPassees;
use App\Entity\Odpf\OdpfEquipesPassees;
use App\Entity\Odpf\OdpfMemoires;
use App\Service\CreatePageEdPassee;
use App\Service\MessageFlashBag;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminsiteCrudController extends AbstractCrudController
{
    private RequestStack $requestStack;
    private EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entitymanager, MessageFlashBag $flashBag, ParameterBagInterface $parameterBag)
    {
        $this->requestStack = $requestStack;
        $this->em = $entitymanager;

    }


    public static function getEntityFqcn(): string
    {
        return Edition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Réglage du site')
            ->setSearchFields(['id', 'ed', 'ville', 'lieu'])
            ->setPaginatorPageSize(30);
    }

    public function configureFields(string $pageName): iterable
    {
        $ed = TextField::new('ed');
        $ville = TextField::new('ville');
        $date = DateTimeField::new('date');
        $lieu = TextField::new('lieu');
        $dateouverturesite = DateTimeField::new('dateouverturesite');
        $dateclotureinscription = DateTimeField::new('dateclotureinscription');
        $datelimcia = DateTimeField::new('datelimcia');
        $datelimnat = DateTimeField::new('datelimnat');
        $concourscia = DateField::new('concourscia');
        $concourscn = DateField::new('concourscn');
        $nomParrain=TextField::new('nomParrain','Parrain');
        $titreParrain=TextField::new('titreParrain','titre parrain');
        $id = IntegerField::new('id', 'ID');
        $annee=TextField::new('annee','année');
        if (Crud::PAGE_INDEX === $pageName) {
            return [$ed, $ville, $date, $lieu, $dateouverturesite, $dateclotureinscription, $datelimcia, $datelimnat, $concourscia, $concourscn,$nomParrain,$titreParrain];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $ed, $annee, $date, $ville, $lieu, $datelimcia, $datelimnat, $dateouverturesite, $concourscia, $concourscn, $dateclotureinscription,$nomParrain,$titreParrain];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$ed, $annee, $ville, $date, $lieu, $dateouverturesite, $dateclotureinscription, $datelimcia, $datelimnat, $concourscia, $concourscn,$nomParrain,$titreParrain];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$ed, $annee, $ville, $date, $lieu, $dateouverturesite, $dateclotureinscription, $datelimcia, $datelimnat, $concourscia, $concourscn,$nomParrain,$titreParrain];
        }
    }

    public function configureActions(Actions $actions): Actions
    {

        $creerEditionPassee = Action::new('creer_edition_passee', 'Creer une édition passée', 'fa fa-cubes')
            ->linkToCrudAction('creer_edition_passee');//->createAsBatchAction();
        return $actions->add(Crud::PAGE_INDEX, $creerEditionPassee);


    }


    public function creer_edition_passee(AdminContext $context)
    {
        $idEdition = $context->getRequest()->query->get('entityId');
        $edition = $this->getDoctrine()->getRepository('App:Edition')->findOneBy(['id' => $idEdition]);
        $repositoryEquipes = $this->getDoctrine()->getRepository('App:Equipesadmin');
        $repositoryFichiersequipes = $this->getDoctrine()->getRepository('App:Fichiersequipes');
        $repositoryOdpfEditionsPassees = $this->getDoctrine()->getRepository('App:Odpf\OdpfEditionsPassees');
        $repositoryEquipesPassees = $this->getDoctrine()->getRepository('App:Odpf\OdpfEquipesPassees');
        $repositoryEleves = $this->getDoctrine()->getRepository('App:Elevesinter');
        $repositoryOdpfmemoires = $this->getDoctrine()->getRepository('App:Odpf\OdpfMemoires');
        $editionPassee = $repositoryOdpfEditionsPassees->findOneBy(['edition' => $edition->getEd()]);
        if ($editionPassee === null) {

            $editionPassee = new OdpfEditionsPassees();
        }
        $editionPassee->setEdition($edition->getEd());
        $editionPassee->setAnnee($edition->getAnnee());
        $editionPassee->setLieu($edition->getLieu());
        $editionPassee->setVille($edition->getVille());
        $date= 'du '.date_format($edition->getDateouverturesite(),'d F Y ').' au '.date_format($edition->getDateclotureinscription(),' d F Y ');
        $editionPassee->setDateinscription($date);
        $date=date_format($edition->getConcourscia(), 'd F Y');
        $editionPassee->setDateCia($date);
        $date= date_format($edition->getConcourscn(), 'd F Y');
        $editionPassee->setDateCn($date);
        $editionPassee->setNomParrain($edition->getNomParrain());
        $editionPassee->setTitreParrain($edition->getTitreParrain());
        $this->em->persist($editionPassee);
        $this->em->flush();
        $listeEquipes = $repositoryEquipes->findBy(['edition'=>$edition]);

        foreach ($listeEquipes as $equipe) {
            $OdpfEquipepassee = $repositoryEquipesPassees->findOneBy(['numero' => $equipe->getNumero(), 'edition' => $editionPassee]);
            if ($OdpfEquipepassee === null) {
                $OdpfEquipepassee = new OdpfEquipesPassees();
            }
            $OdpfEquipepassee->setEdition($editionPassee);
            $OdpfEquipepassee->setNumero($equipe->getNumero());
            if ($equipe->getRneId() != null) {

                $OdpfEquipepassee->setLettre($equipe->getLettre());
                $OdpfEquipepassee->setLycee($equipe->getRneId()->getNom());
                $OdpfEquipepassee->setVille($equipe->getRneId()->getCommune());
                $OdpfEquipepassee->setAcademie($equipe->getLyceeAcademie());
                $nomsProfs1 = ucfirst($equipe->getPrenomProf1()) . ' ' . strtoupper($equipe->getNomProf1());
                $equipe->getIdProf2() != null ? $nomsProfs = $nomsProfs1 . ', ' . $equipe->getPrenomProf2() . ' ' . $equipe->getNomProf2() : $nomsProfs = $nomsProfs1;
                $OdpfEquipepassee->setProfs($nomsProfs);
                $listeEleves = $repositoryEleves->findBy(['equipe' => $equipe]);
                $nomsEleves = '';
                foreach ($listeEleves as $eleve) {
                    $nomsEleves = $nomsEleves . ucfirst($eleve->getPrenom()) . ' ' . $eleve->getNom() . ', ';
                }
                $OdpfEquipepassee->setEleves($nomsEleves);
            }
            $OdpfEquipepassee->setTitreProjet($equipe->getTitreProjet());
            $OdpfEquipepassee->setSelectionnee($equipe->getSelectionnee());
            $editionPassee->addOdpfEquipesPassee($OdpfEquipepassee);
            $this->em->persist($OdpfEquipepassee);
            $this->em->flush();

            $listeMemoires = $repositoryFichiersequipes->findBy(['equipe' => $equipe, 'typefichier' => [0, 1, 2, 3]]);
            //dd($listeMemoires);
            //dd($this->getParameter('app.path.odpfarchives') . '/' . $OdpfEquipepassee->getEdition()->getEdition() . '/memoires');
            if ($listeMemoires) {

                if (!file_exists($this->getParameter('app.path.odpfarchives') . '/' . $OdpfEquipepassee->getEdition()->getEdition() . '/memoires')) {
                    //mkdir($this->getParameter('app.path.odpfarchives') . '/' . $OdpfEquipepassee->getEdition()->getEdition());
                    mkdir($this->getParameter('app.path.odpfarchives') . '/' . $OdpfEquipepassee->getEdition()->getEdition() . '/memoires');

                }
                foreach ($listeMemoires as $memoire) {

                    $odpfMemoire = $repositoryOdpfmemoires->findOneBy(['equipe' => $OdpfEquipepassee, 'type' => $memoire->getTypefichier()]);

                    if ($odpfMemoire === null) {
                        $odpfMemoire = new OdpfMemoires();
                    }
                    $odpfMemoire->setEquipe($OdpfEquipepassee);
                    $odpfMemoire->setType($memoire->getTypefichier());
                    if (file_exists($this->getParameter('app.path.fichiers') . '/' . $this->getParameter('type_fichier')[$memoire->getTypefichier() == 1 ? 0 : $memoire->getTypefichier()] . '/' . $memoire->getFichier())) {

                       copy($this->getParameter('app.path.fichiers') . '/' . $this->getParameter('type_fichier')[$memoire->getTypefichier() == 1 ? 0 : $memoire->getTypefichier()] . '/' . $memoire->getFichier(),
                            $this->getParameter('app.path.odpfarchives') . '/' . $OdpfEquipepassee->getEdition()->getEdition() . '/memoires/' . $memoire->getFichier());

                        $odpfMemoire->setNomFichier($memoire->getFichier());
                        $odpfMemoire->setUpdatedAt(new DateTime('now'));
                    }
                    elseif (file_exists($this->getParameter('app.path.fichiers') . '/' . $this->getParameter('type_fichier')[$memoire->getTypefichier() == 1 ? 0 : $memoire->getTypefichier()] . '/' . $memoire->getFichier())) {



                    }
                        $this->em->persist($odpfMemoire);
                    $this->em->flush();
                }
            }
        }
        $listePhotos=$this->getDoctrine()->getRepository('App:Photos')->findBy(['edition'=>$edition]);

        foreach ($listePhotos as $photo){
            $equipe=$photo->getEquipe();
            $equipepassee=$repositoryEquipesPassees->findOneBy(['titreProjet'=>$equipe->getTitreProjet()]);
            $photo->setEditionspassees($editionPassee);
            $photo->setEquipepassee($equipepassee);
            $this->em->persist($photo);

            $editionPassee->addPhoto($photo);
            $this->em->persist($photo);
            if (file_exists($this->getParameter('app.path.photos') . '/'. $photo->getPhoto() )) {

                copy($this->getParameter('app.path.photos') . '/' . $photo->getPhoto(),
                    $this->getParameter('app.path.photospassees') . '/' . $editionPassee->getEdition() . '/photoseq/' . $photo->getPhoto());
            }
            if (file_exists($this->getParameter('app.path.photos') . '/thumbs/'. $photo->getPhoto() )) {

                copy($this->getParameter('app.path.photos') . '/thumbs/'. $photo->getPhoto(),
                    $this->getParameter('app.path.photospassees') . '/' . $editionPassee->getEdition() . '/photoseq/thumbs/' . $photo->getPhoto());


            }


        }

        $createArticle = new CreatePageEdPassee($this->em);
        $article=$createArticle->create($editionPassee);
        $this->em->persist($article);
        $this->em->flush();
        return $this->redirectToRoute('odpfadmin');


    }

}

