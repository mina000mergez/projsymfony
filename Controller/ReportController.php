<?php

namespace App\Controller;

use App\Entity\MedecinSearch;
use App\Form\MedecinSearchType;
use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class ReportController extends AbstractController
{
    #[Route('/most-consulted-medecins', name: 'most_consulted_medecins')]
    public function index(RendezVousRepository $repository): Response
    {
        $medecins = $repository->findMostConsultedMedecins();
        return $this->render('report/index.html.twig', [
            'medecins' => $medecins,
        ]);
    }

    #[Route('/rendezVousMedecin', name: 'rendez_vous_medecin')]
    public function RendezVousMedecin(Request $request, RendezVousRepository $repository): Response
    {
        $medecinSearch = new MedecinSearch();
        $form = $this->createForm(MedecinSearchType::class, $medecinSearch);
        $form->handleRequest($request);
        $rendezVous = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $medecin = $medecinSearch->getMedecin();
            if ($medecin != "") {
                $rendezVous = $repository->findBy(['medecin' => $medecin]);
            } else {
                $rendezVous = $repository->findAll();
            }
        }

        return $this->render('report/RendezVousMedecin.html.twig', [
            'form' => $form->createView(),
            'rendezVous' => $rendezVous,
        ]);
    }
}