<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/article', name: 'admin.article')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $repo
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return  $this->render(
            'Backend/Article/index.html.twig',
            [
                'articles' => $this->repo->findAllWithTags(),
            ]
        );
    }

    #[Route('/create', name: ".create", methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        // Création d'un nouvel objet Article
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUser($this->getUser());

            $this->repo->save($article, true);

            $this->addFlash('success', 'Article created successfully');

            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('Backend/Article/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/update/{id}', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Article $article, Request $request): Response|RedirectResponse
    {
        if (!$article instanceof Article) {
            $this->addFlash('error', 'Article not found');

            return $this->redirectToRoute('admin.article.index');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($article, true);

            $this->addFlash('success', 'Article modified  successfully');

            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('Backend/Article/update.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: '.delete', methods: ['POST', 'DELETE'])]
    public function delete(?Article $article, Request $request): RedirectResponse
    {
        if (!$article instanceof Article) {
            $this->addFlash('error',  'Article not found');

            return $this->redirectToRoute('admin.article.index');
        }

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('token'))) {
            $this->repo->remove($article, true);

            $this->addFlash('success', 'Article deleted successfully');

            return $this->redirectToRoute('admin.article.index');
        }

        $this->addFlash('error', 'Token invalide');

        return $this->redirectToRoute('admin.article.index');
    }

    #[Route('/switch/{id}', name: '.switch', methods: ['GET'])]
    public function switchVisibiltyArticle(?Article $article): JsonResponse
    {
        if (!$article instanceof Article) {
            return new JsonResponse('Article not found', 404);
        }

        $article->setActif(!$article->isActif());
        $this->repo->save($article, true);

        return new JsonResponse('Visibility changed', 200);
    }
}
