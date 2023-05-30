<?php

namespace App\Controller\Backend;

use App\Entity\User;

use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/users', name: 'admin.user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $repo
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return  $this->render(
            'Backend/User/index.html.twig',
            [
                'users' => $this->repo->findAll(),
            ]);
    }

 /*   #[Route('/create', name: ".create", methods: ['GET', 'POST'])]
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
    }*/

    #[Route('/{id}/edit', name: '.update', methods: ['GET', 'POST'])]
    public function update(?User $user, Request $request): Response|RedirectResponse
    {
        if (!$user instanceof User) {
            $this->addFlash('error', 'User not found');

            return $this->redirectToRoute('admin.user.index');
        }

        //TODO: create form user with condition and return view
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($user, true);

            $this->addFlash('success', 'User profile modified  successfully');

            return $this->redirectToRoute('admin.user.index');
        }

        return $this->render('Backend/User/edit.html.twig', [
            'form' => $form,
        ]);
    }
/*
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
    public function switchVisibilityArticle (?Article $article): JsonResponse
    {
        if(!$article instanceof Article) {
            return new JsonResponse('Article not found', 404);
        }

        $article->setActif(!$article->isActif());
        $this->repo->save($article, true); // trus flush $article vers la BDD

        return new JsonResponse('Visibility changed', 200);
    }*/
}