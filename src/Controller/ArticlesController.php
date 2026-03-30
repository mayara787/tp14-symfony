<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
final class ArticlesController extends AbstractController
{
    //    #[Route('/articles', name: 'app_articles')]
    //     public function index(): Response
    //     {
    //     $articles = [
    //         ['titre' => 'Introduction à Symfony',    'auteur' => 'Alice',  'publie' => true],
    //         ['titre' => 'Les bases de Twig',          'auteur' => 'Bob',    'publie' => true],
    //         ['titre' => 'Doctrine ORM en pratique',   'auteur' => 'Claire', 'publie' => false],
    //         ['titre' => 'Sécurité avec Symfony',      'auteur' => 'David',  'publie' => true],
    //         ['titre' => 'API Platform (brouillon)',   'auteur' => 'Eve',    'publie' => false],
    //     ];

    //     return $this->render('articles/index.html.twig', [
    //         'articles' => $articles,
    //     ]);
    // }

    // #[Route('/articles/nouveau', name: 'app_article_nouveau')]
    // public function nouveau(EntityManagerInterface $em): Response
    // {
    // $article = new Article();
    // $article->setTitre('Mon premier article');
    // $article->setContenu('Ceci est le contenu de mon premier article créé avec Doctrine.');
    // $article->setAuteur('Étudiant');
    // $article->setDateCreation(new \DateTime());
    // $article->setPublie(true);

    // $em->persist($article);
    // $em->flush();

    // return new Response("Article créé avec l'id : " . $article->getId());
    // }


    #[Route('/articles/nouveau', name: 'app_article_nouveau')]
    public function nouveau(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        
        // Création du formulaire
        $form = $this->createForm(ArticleType::class, $article);
        
        // Traitement de la requête
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();
            
            // Message flash de confirmation
            $this->addFlash('success', 'Article créé avec succès !');
            
            return $this->redirectToRoute('app_articles');
        }
        
        return $this->render('articles/nouveau.html.twig', [
            'formulaire' => $form,
        ]);
    }

    #[Route('/articles', name: 'app_articles')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

#[Route('/articles/{id}/modifier', name: 'app_article_modifier', requirements: ['id' => '\d+'])]
public function modifier(Article $article, Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush(); // Pas besoin de persist() car l'entité est déjà gérée par Doctrine

        $this->addFlash('success', 'Article modifié avec succès !');
        return $this->redirectToRoute('app_article_detail', ['id' => $article->getId()]);
    }

    return $this->render('articles/modifier.html.twig', [
        'formulaire' => $form,
        'article' => $article,
    ]);
}

#[Route('/articles/{id}/supprimer', name: 'app_article_supprimer', requirements: ['id' => '\d+'], methods: ['POST'])]
public function supprimer(Article $article, Request $request, EntityManagerInterface $em): Response
{
    // Vérification du token CSRF pour la sécurité
    if ($this->isCsrfTokenValid('supprimer_' . $article->getId(), $request->request->get('_token'))) {
        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'Article supprimé avec succès.');
    } else {
        $this->addFlash('danger', 'Token CSRF invalide. Suppression annulée.');
    }

    return $this->redirectToRoute('app_articles');
}







    #[Route('/articles/{id}', name: 'app_article_detail', requirements: ['id' => '\d+'])]
    public function detail(Article $article): Response
    {
        return $this->render('articles/detail.html.twig', [
            'article' => $article,
        ]);
    }




}
