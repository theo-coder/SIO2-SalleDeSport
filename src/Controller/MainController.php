<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ContactType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function main(ArticleRepository $articleRepository)
    {
        return $this->render('main/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articles(CategoryRepository $categoryRepository)
    {
        return $this->render('article/articles.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function article(Article $article)
    {
        return $this->render('article/article.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
