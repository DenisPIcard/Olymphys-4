<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;


class valid_fichiers
{
    public function validation_fichiers(UploadedFile $file, $num_type_fichier)
    {




        if (($num_type_fichier == 0) or ($num_type_fichier == 1)) {
            $violations = $validator->validate(
                $file,
                [new NotBlank(),
                    new File(['maxSize' => '2600k',
                        'mimeTypes' => ['application/pdf',]])]
            );
            if ($violations->count() > 0) {

                /** @var ConstraintViolation $violation */
                $violation = $violations[0];
                $this->addFlash('alert', $violation->getMessage());
                return $this->redirectToRoute('fichiers_charge_fichiers', ['infos' => $infos,]);
            }

            $sourcefile = $file;
            $stringedPDF = file_get_contents($sourcefile, true);
            $regex = "/\/Page |\/Page\/|\/Page\n|\/Page\r\n|\/Page>>\r/";//selon l'outil de codage en pdf utilisé, les pages ne sont pas repérées de la m^me façon
            $pages = preg_match_all($regex, $stringedPDF, $title);

            if ($pages == 0) {
                $regex = "/\/Pages /";
                $pages = preg_match_all($regex, $stringedPDF, $title);

            }
            if ($pages > 20) { //S'il y a plus de 20 pages la procédure est interrompue et on return à la page d'accueil avec un message d'avertissement
                $request->getSession()
                    ->getFlashBag()
                    ->add('alert', 'Votre mémoire contient  ' . $pages . ' pages. Il n\'a pas pu être accepté, il ne doit pas dépasser 20 page !');
                return $this->redirectToRoute('fichiers_charge_fichiers', array('infos' => $infos));
            }
        }
        if ($num_type_fichier == 2) {
            $violations = $validator->validate(
                $file,
                [
                    new NotBlank(),
                    new File([
                        'maxSize' => '1000k',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier du bon format',
                    ])
                ]
            );
            if ($violations->count() > 0) {

                /** @var ConstraintViolation $violation */
                $violation = $violations[0];
                $this->addFlash('alert', $violation->getMessage());
                return $this->redirectToRoute('fichiers_charge_fichiers', [
                    'infos' => $infos,
                ]);
            }
            $sourcefile = $file; //$this->getParameter('app.path.tempdirectory').'/temp.pdf';
            $stringedPDF = file_get_contents($sourcefile, true);
            $regex = "/\/Page |\/Page\//";
            $pages = preg_match_all($regex, $stringedPDF, $title);
            if ($pages == 0) {
                $regex = "/\/Pages /";
                $pages = preg_match_all($regex, $stringedPDF, $title);
            }
            if ($pages > 1) { //S'il y a plus de 1 page la procédure est interrompue et on return à la page d'accueil avec un message d'avertissement
                $request->getSession()
                    ->getFlashBag()
                    ->add('alert', 'Votre résumé contient  ' . $pages . ' pages. Il n\'a pas pu être accepté, il ne doit pas dépasser 1 page !');
                return $this->redirectToRoute('fichiers_charge_fichiers', array('infos' => $infos));
            }
        }
        if ($num_type_fichier == 3) {
            if ($dateconnect > $this->session->get('datelimdiaporama')) {
                $violations = $validator->validate(
                    $file,
                    [
                        new NotBlank(),
                        new File([
                            'maxSize' => '10000k',
                            'mimeTypes' => [
                                'application/pdf',
                            ],
                            'mimeTypesMessage' => 'Veuillez télécharger un fichier du bon format'
                        ])
                    ]
                );
                if ($violations->count() > 0) {
                    /** @var ConstraintViolation $violation */
                    $violation = $violations[0];
                    $this->addFlash('alert', $violation->getMessage());
                    return $this->redirectToRoute('fichiers_charge_fichiers', [
                        'infos' => $infos,
                    ]);
                }
            } else {
                $message = 'Le dépôt des diaporamas n\'est possible qu\'après le concours national';
                $request->getSession()
                    ->getFlashBag()
                    ->add('alert', $message);
                return $this->redirectToRoute('fichiers_charge_fichiers', array('infos' => $infos));
            }

        }
        if (($num_type_fichier == 4) or ($num_type_fichier == 7)) {
            $violations = $validator->validate($file, [new NotBlank(),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['application/pdf', 'application/x-pdf', "application/msword",
                            'application/octet-stream',
                            'application/vnd.oasis.opendocument.text',
                            'image/jpeg'],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier du bon format'
                    ])
                ]
            );
            if ($violations->count() > 0) {

                /** @var ConstraintViolation $violation */
                $violation = $violations[0];
                $this->addFlash('alert', $violation->getMessage());
                return $this->redirectToRoute('fichiers_charge_fichiers', [
                    'infos' => $infos,
                ]);
            }

        }
        if ($num_type_fichier == 5) {

            $violations = $validator->validate($file, [new NotBlank(),
                    new File([
                        'maxSize' => '10000k',
                        'mimeTypes' => ['application/pdf', 'application/x-pdf'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier du bon format'
                    ])
                ]
            );
            if ($violations->count() > 0) {

                /** @var ConstraintViolation $violation */
                $violation = $violations[0];
                $this->addFlash('alert', $violation->getMessage());
                return $this->redirectToRoute('fichiers_charge_fichiers', [
                    'infos' => $infos,
                ]);
            }


        }
        if ($num_type_fichier == 6) {

            $violations = $validator->validate($file, [new NotBlank(),
                    new File([
                        'maxSize' => '1000k',
                        'mimeTypes' => ['application/pdf', 'application/x-pdf'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier du bon format'
                    ])
                ]
            );
            if ($violations->count() > 0) {

                /** @var ConstraintViolation $violation */
                $violation = $violations[0];
                $this->addFlash('alert', $violation->getMessage());
                return $this->redirectToRoute('fichiers_charge_fichiers', [
                    'infos' => $infos,
                ]);
            }


        }
    }
}