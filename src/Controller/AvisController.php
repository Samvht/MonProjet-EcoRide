<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\DeposerAvis;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CovoiturageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AvisController extends AbstractController
{

    #[Route('/avis/{covoiturage_id}', name: 'laisser_avis')]
    public function laisserAvis(int $covoiturage_id, Request $request, EntityManagerInterface $em, CovoiturageRepository $covoiturageRepo): Response {
        $covoiturage = $covoiturageRepo->find($covoiturage_id);

    if (!$covoiturage || $covoiturage->getStatut() !== 'termine') {
        throw $this->createNotFoundException('Covoiturage introuvable ou non terminé.');
    }

    $avis = new Avis();
    $DeposerAvisForm = $this->createForm(DeposerAvis::class, $avis);
    $DeposerAvisForm->handleRequest($request);

    if ($DeposerAvisForm->isSubmitted() && $DeposerAvisForm->isValid()) {
        $avis->setCovoiturage($covoiturage);
        $avis->setUtilisateur($covoiturage->getCreateur());
        $avis->setStatut(false); // à valider par un employé

        $em->persist($avis);
        $em->flush();

        $this->addFlash('success', 'Merci pour ton avis !');
        return $this->redirectToRoute('app_home'); 
    }

    return $this->render('avis/avis.html.twig', [
        'DeposerAvisForm' => $DeposerAvisForm->createView(),
        'covoiturage' => $covoiturage
    ]);
}
}