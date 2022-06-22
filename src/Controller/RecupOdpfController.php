<?php

namespace App\Controller;

use App\Entity\Odpf\OdpfArticle;
use App\Entity\Odpf\OdpfCategorie;
use App\Entity\Odpf\OdpfEditionsPassees;
use App\Entity\Odpf\OdpfEquipesPassees;
use App\Entity\Odpf\OdpfFichierspasses;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\u;

class RecupOdpfController extends AbstractController
{
    private EntityManagerInterface $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;


    }


    /**
     * @Route("/recup/odpf", name="app_recup_odpf")
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class)
            ->add('numero', ChoiceType::class, [
                'choices' => range(1, 30),
                'label' => 'Choisir le numéro de l\'édition'


            ])
            ->add('save', SubmitType::class, ['label' => 'Valider'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $numero = $form->get('numero')->getData() - 1;
            if ($file) {

                $originalFilename = $file->getClientOriginalName();
                try {

                    $file->move(
                        $this->getParameter('app.path.recupOdpf'),
                        $originalFilename
                    );


                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $texte = file_get_contents('recupOdpf/' . $originalFilename);
                $editionName = explode('.', explode('_', $originalFilename)[1])[0];
                $editionPasseeRepository = $this->doctrine->getRepository(OdpfEditionsPassees::class);
                $equipeRepository = $this->doctrine->getRepository(OdpfEquipesPassees::class);
                $editionPassee = $editionPasseeRepository->findOneBy(['pseudo' => $editionName]);
                if ($editionPassee === null) {
                    $editionPassee = new OdpfEditionsPassees();

                }
                $editionPassee->setPseudo($editionName);
                $editionPassee->setEdition($numero);
                $this->doctrine->persist(($editionPassee));
                $this->doctrine->flush();
                $this->createDir(($editionPassee));
                $listeLiens = explode('<li>', $texte);

                if ($listeLiens > 0) {
                    foreach (range(0, count($listeLiens) - 1) as $i) {
                        if (count($listeLiens) > 1) {
                            if ($i > 0) {

                                if (count(explode('href="', $listeLiens[$i])) > 2) {

                                    $liens = explode('href="', $listeLiens[$i]);
                                    $equipe = $this->extractDonneesEdition($liens[0]);
                                } else {
                                    $equipe = $this->extractDonneesEdition($listeLiens[$i]);
                                }

                                $numeroeq = $equipe['numero'];
                                $lettre = $equipe['lettre'];
                                $lycee = $equipe['lycee'];
                                $nomLycee = explode('-', $lycee)[0];
                                $villeLycee = null;
                                if (isset(explode('-', $lycee)[1])) {
                                    $villeLycee = explode('-', $lycee)[1];
                                }
                                $projet = $equipe['nom'];

                                $equipePassee = $equipeRepository->createQueryBuilder('e')
                                    ->where('e.numero =:numero')
                                    ->orWhere('e.lettre =:lettre')
                                    ->andWhere('e.editionspassees =:edition')
                                    ->setParameters(['numero' => $numeroeq, 'lettre' => $lettre, 'edition' => $editionPassee])
                                    ->getQuery()
                                    ->getOneOrNullResult();
                                if ($equipePassee === null) {
                                    $equipePassee = new OdpfEquipesPassees();
                                }
                                $equipePassee->setNumero($numeroeq);
                                $equipePassee->setLettre($lettre);
                                $equipePassee->setEditionspassees($editionPassee);
                                $equipePassee->setTitreProjet($projet);
                                $equipePassee->setSelectionnee(true);
                                $equipePassee->setLycee($nomLycee);
                                $equipePassee->setVille($villeLycee);
                                $this->doctrine->persist($equipePassee);
                                $this->doctrine->flush();


                                if (isset($liens)) {

                                    $pathFichier = $equipe['fichier'];

                                    $this->deposeFichier($pathFichier, $equipePassee, $editionPassee);

                                    foreach (range(1, count($liens) - 1) as $j) {
                                        $pathFichier = u($liens[$j])->before('"')->toString();

                                        $this->deposeFichier($pathFichier, $equipePassee, $editionPassee);
                                    }
                                } else {

                                    $pathFichier = u($equipe['fichier'])->after('"')->toString();

                                    $this->deposeFichier($pathFichier, $equipePassee, $editionPassee);

                                }
                            }

                        }


                    }


                    $this->createArticleEdition($listeLiens[0], $editionPassee);

                }
            }

        }

        return $this->renderForm('recup_odpf/index.html.twig', [
            'controller_name' => 'RecupOdpfController', 'form' => $form
        ]);
    }

    public function createDir($editionPassee)
    {
        $filesystem = new Filesystem();
        if (!file_exists($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/fichiers')) {

            $dir = $this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/fichiers';
            $filesystem->mkdir($dir);
        }
        if (!file_exists($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/parrain')) {
            //mkdir($this->getParameter('app.path.odpf_archives') . '/' . $OdpfEquipepassee->getEdition()->getEdition());
            $filesystem->mkdir($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/parrain');
        }
        if (!file_exists($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/affiche')) {
            //mkdir($this->getParameter('app.path.odpf_archives') . '/' . $OdpfEquipepassee->getEdition()->getEdition());
            //$dir=
            $filesystem->mkdir($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/affiche');
        }
        if (!file_exists($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/photoseq')) {
            //mkdir($this->getParameter('app.path.odpf_archives') . '/' . $OdpfEquipepassee->getEditionspassees()->getEdition());
            $filesystem->mkdir($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/photoseq');
        }
        if (!file_exists($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/photoseq/thumbs')) {
            //mkdir($this->getParameter('app.path.odpf_archives') . '/' . $OdpfEquipepassee->getEditionspassees()->getEdition());
            $filesystem->mkdir($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/photoseq/thumbs');
        }
        if (!file_exists($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/documents')) {
            //mkdir($this->getParameter('app.path.odpf_archives') . '/' . $OdpfEquipepassee->getEditionspassees()->getEdition());
            $filesystem->mkdir($this->getParameter('app.path.odpf_archives') . '/' . $editionPassee->getEdition() . '/documents');
        }


    }

    public function extractDonneesEdition($lien): array
    {

        $pathFichier = u($lien)->before('.pdf', true);
        $pathFichier = $pathFichier->after('href="')->toString();
        $equipe['fichier'] = $pathFichier;
        $nom = u($lien)->before('</a>')->afterLast('>')->toString();
        $equipe['nom'] = $nom;
        $equipe['numero'] = null;
        if (u($lien)->containsAny('gr-')) {
            $equipe['numero'] = u(u($lien)->after('gr-')->toString())->before('/')->toString();
        }
        $equipe['lycee'] = substr($lien, strpos($lien, '<em>') + 4, strpos($lien, '</em>') - strpos($lien, '<em>') - 4);
        $equipe['lettre'] = null;
        if (u($lien)->containsAny('Equipe')) {
            $equipe['lettre'] = substr(u($lien)->after('Equipe')->toString(), 0, 1);
        }
        if (u($lien)->containsAny('groupe')) {
            $equipe['lettre'] = substr(u($lien)->after('groupe')->toString(), 0, 1);
        }

        return $equipe;


    }

    public function deposeFichier($pathfichier, $equipePassee, $editionPassee)
    {
        $fichiersRepository = $this->doctrine->getRepository(OdpfFichierspasses::class);
        $fichier = 'https://odpf.org/' . $pathfichier;
        $nomFichier = explode('/', $fichier)[count(explode('/', $fichier)) - 1];

        switch ($nomFichier) {
            case 'memoire.pdf':
                $typefichier = 0;

                break;
            case 'annexe.pdf' :
                $typefichier = 1;
                break;
            case 'annexes.pdf' :
                $typefichier = 1;
                break;
            case 'resume.pdf' :
                $typefichier = 2;
                break;

        }
        if (isset($typefichier)) {
            $fichierPasse = $fichiersRepository->findOneBy(['equipepassee' => $equipePassee, 'typefichier' => $typefichier]);


            if ($fichierPasse == null) {
                $fichierPasse = new OdpfFichierspasses();

            }
            $fichierPasse->setTypefichier($typefichier);
            try {
                $fileSystem = new Filesystem();
                $fileSystem->copy($fichier, $this->getParameter('app.path.recupOdpf') . '/' . $nomFichier);
                $file = new UploadedFile($this->getParameter('app.path.recupOdpf') . '/' . $nomFichier, $nomFichier, null, null, true);
                $fichierPasse->setEditionspassees($editionPassee);
                $fichierPasse->setEquipepassee($equipePassee);
                $fichierPasse->setNational(true);
                $fichierPasse->setFichierFile($file);
                $this->doctrine->persist($fichierPasse);
                $this->doctrine->flush();
            } catch (Exception $e) {

            }
        }
    }

    public function createArticleEdition($lien, $editionPassee)
    {
        $numero = $editionPassee->getEdition();
        $articleRepository = $this->doctrine->getRepository(OdpfArticle::class);
        $categoriesRepository = $this->doctrine->getRepository(OdpfCategorie::class);

        $article = $articleRepository->findOneBy(['choix' => 'edition' . $numero]);
        $categorie = $categoriesRepository->findOneBy(['categorie' => 'editions']);

        $textes = explode('<ul class="thesis-list">', $lien);

        $hrefs = explode('href="', $textes[0]);

        $texte = $hrefs[0];
        foreach (range(1, count($hrefs) - 1) as $i) {
            $u = new UnicodeString($hrefs[$i]);
            if (u($hrefs[$i])->containsAny('pdf')) {
                $pathFichier = $u->before('.pdf', true);


                $pathFichierorigine = 'https://odpf.org/' . substr($pathFichier->toString(), 0);
                $nomFichierOdpf = $pathFichier->afterLast('/');

                $pathFichiercible = $this->getParameter('app.path.odpf_archives') . '/' . $numero . '/documents/' . $nomFichierOdpf->replace('.', $numero . '.')->toString();
                $fileSystem = new Filesystem();
                $fileSystem->copy($pathFichierorigine, $pathFichiercible);


                $pathOlymphys = '/odpf/odpf-archives/' . $numero . '/documents/' . $nomFichierOdpf->replace('.', $numero . '.')->toString();

                $hrefs[$i] = $u->replace($pathFichier, $pathOlymphys)->toString();
            }


            $texte = $texte . 'href=' . $hrefs[$i];

        }
        $equipeRepository = $this->doctrine->getRepository(OdpfEquipesPassees::class);
        $listeEquipes = $equipeRepository->createQueryBuilder('e')
            ->andWhere('e.editionspassees =:editionPassee')
            ->setParameter('editionPassee', $editionPassee)
            ->addOrderBy('e.lettre', 'ASC')
            ->addOrderBy('e.numero', 'ASC')
            ->getQuery()->getResult();


        if ($listeEquipes !== null) {
            $texte = $texte . '<ul class="thesis-list">';
            foreach ($listeEquipes as $equipe) {
                $equipe->getSelectionnee() != true ? $marque = $equipe->getNumero() : $marque = $equipe->getLettre();
                if ($equipe->getLettre()===null){
                    $marque=$equipe->getNumero();
                }
                $texte = $texte . '<li><a href="/odpf/editionspassees/equipe,' . $equipe->getId() . '" >' . $marque . ' - ' . $equipe->getTitreProjet() . '</a>, lycée ' . $equipe->getLycee() . ', ' . $equipe->getVille() . '</li>';

            }
            $texte = $texte . '</ul>';

        }

        if ($article === null) {
            $article = new OdpfArticle();

        }

        $article->setChoix('edition' . $numero);
        $article->setCategorie($categorie);
        $article->setTexte($texte);
        $this->doctrine->persist($article);
        $this->doctrine->flush();

    }


}
