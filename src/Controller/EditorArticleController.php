<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/editor/article")
 */
class EditorArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserRepository $repo): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setEditDateTime(new \DateTime());
            $image = $form->get('image')->getData();



            $users = $repo->findAll()[0];
            $article->setArticleEditor($users);


            
            if ($image) {

                $newFileName = uniqid() . '.' . $image->guessExtension();

                try {

                    $image->move(
                        './image/article/',
                        $newFileName
                    );

                } catch (FileException $e) {
                    //error
                }

                $article->setImage('./image/article/' . $newFileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $oldImage = $article->getImage();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setEditDateTime(new \DateTime());

            $image = $form->get('image')->getData();
            if ($image) {
                $newFileName = uniqid() . '.' . $image->guessExtension();

                try {

                    $file = glob($oldImage);

                    if (@is_file($file[0])) {
                        @unlink($file[0]);
                    }

                } catch
                (FileException $e) {
                    $this->addFlash('danger', 'Something wrong... Please try again later');
                }

                try {

                    $image->move(
                        'image/article/',
                        $newFileName
                    );

                } catch (FileException $e) {
                    $this->addFlash('danger', 'Something wrong... Please try later');
                }

                $article->setImage('image/article/' . $newFileName);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }
}
