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
            $entityManager->persist($article);
            $entityManager->flush();
            
            $this->addFlash('success', 'Article créé avec succès');
            return $this->redirectToRoute('article_index');
        }
        
        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
}

#[Route('/article/{id}', name: 'article_show')]
public function show(Article $article): Response
{
    return $this->render('articles/show.html.twig', [
        'article' => $article,
    ]);
}

#[Route('/article/edit/{id}', name: 'article_edit')]
public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        
        $this->addFlash('success', 'Article modifié avec succès');
        return $this->redirectToRoute('article_index');
    }
    
    return $this->render('articles/edit.html.twig', [
        'article' => $article,
        'form' => $form->createView(),
    ]);
}

#[Route('/article/delete/{id}', name: 'article_delete')]
public function delete(Article $article, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($article);
    $entityManager->flush();
    
    $this->addFlash('success', 'Article supprimé avec succès');
    return $this->redirectToRoute('article_index');
}
    #[Route('/', name: 'article_index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('articles/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }
}
?>