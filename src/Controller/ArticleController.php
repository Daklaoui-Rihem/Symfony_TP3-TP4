<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'article_list')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    #[Route('/article/save')]
    public function save(EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setNom('Article 1');
        $article->setPrix('1000');
        
        $entityManager->persist($article);
        $entityManager->flush();
        
        return new Response('Article enregistré avec id ' . $article->getId());
    }

    #[Route('/article/new', name: 'new_article')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $article = new Article();
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        // Form is valid, proceed with saving
        $entityManager->persist($article);
        $entityManager->flush();
        
        $this->addFlash('success', 'Article created successfully');
        return $this->redirectToRoute('article_list');
    }
    
    // Either the form was not submitted or it contains validation errors
    return $this->render('articles/new.html.twig', [
        'form' => $form->createView()
    ]);
}

    #[Route('/article/{id}', name: 'article_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        
        return $this->render('articles/show.html.twig', ['article' => $article]);
    }

    #[Route('/article/edit/{id}', name: 'edit_article')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/delete/{id}', name: 'delete_article')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        
        $entityManager->remove($article);
        $entityManager->flush();
        
        return $this->redirectToRoute('article_list');
    }
}
?>