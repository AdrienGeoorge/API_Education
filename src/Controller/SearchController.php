<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Service\EstablishmentLocation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SearchController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/', name: 'app_search')]
    public function index(Request $request, EstablishmentLocation $locationService): Response
    {
        // TODO : ne veut pas fonctionner avec le formType
        // $form = $this->createForm(SearchType::class);
        $form = $this->createFormBuilder()
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank(),
                    new Length(5)
                ]
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $establishments = $locationService->getList(
                "numero_uai,appellation_officielle,secteur_public_prive_libe",
                sprintf("code_postal_uai='%s'", $data['zipCode'])
            );
        }

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'establishments' => $establishments ?? null
        ]);
    }
}
