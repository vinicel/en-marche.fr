<?php

namespace AppBundle\Controller\EnMarche\Nomenclature;

use AppBundle\Entity\Nomenclature\Senator;
use AppBundle\Entity\Referent;
use AppBundle\Entity\ReferentArea;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/le-mouvement/senateurs", name="app_nomenclature_senator_")
 */
class SenatorController extends Controller
{
    /**
     * @Route(name="list", methods={"GET"})
     */
    public function list(): Response
    {
        $doctrine = $this->getDoctrine();
        $referentsRepository = $doctrine->getRepository(Referent::class);
        $referentAreasRepository = $doctrine->getRepository(ReferentArea::class);

        return $this->render('nomenclature/senator/list.html.twig', [
            'referents' => $referentsRepository->findByStatusOrderedByAreaLabel(),
            'groupedZones' => $referentAreasRepository->findAllGrouped(),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Senator $senator): Response
    {
        return $this->render('nomenclature/senator/show.html.twig', [
            'senator' => $senator,
        ]);
    }
}
