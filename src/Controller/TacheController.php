<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Tache;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TacheRepository;


final class TacheController extends AbstractController
{
   

    #[Route('/taches/nouveau', name: 'app_tache_nouveau')]
    public function nouveau(EntityManagerInterface $em): Response
    {
    $tache = new Tache();
    $tache->setTitre('ma première tache');
    $tache->setDescription('Ceci est le contenu de ma première tache créé avec Doctrine.');
    $tache->setDateCreation(new \DateTime());
    $tache-> setTerminee(true);

    $em->persist($tache);
    $em->flush();

    return new Response("tache créé avec l'id : " . $tache->getId());
    }

    
    #[Route('/taches', name: 'app_taches')]
    public function index(TacheRepository $tacheRepository): Response
    {
        $taches = $tacheRepository->findAll();

        return $this->render('tache/index.html.twig', [
            'taches' => $taches,
        ]);
    }

    
    
     #[Route('/taches/{id}', name: 'app_tache_detail', requirements: ['id' => '\d+'])]
    public function detail(Tache $tache): Response
    {
        return $this->render('tache/detail.html.twig', [
            'tache' => $tache,
        ]);
    }








    
}
