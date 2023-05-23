<?php

namespace App\Controller\Backend;

use App\Entity\Categorie;

use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categorie', name: 'admin.categorie')]
class CategorieController extends AbstractController
{
    
    public function __construct(
        private readonly CategorieRepository $repo  /* on instacie la class CategorieRepository pour envoyer des requêtes das la table catégorie */ 
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {  
        return  $this->render(
            'Backend/Categorie/index.html.twig',
            [
                'categories' => $this->repo->findAll(), 
            ]
        );                  /* categories est le terme qu'on emploie dans le TWIG ici index.html.twig*/
    }                       /* le findAll() va chercher les propriétés dans l'instanciation de CategorieRepository */

    #[Route('/create', name: ".create", methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $this->repo->save($categorie, true);
            $this->addFlash('success', 'Category created successfully');

            return $this->redirectToRoute('admin.categorie.index');
        }

        return $this->render('Backend/Categorie/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/update/{id}', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Categorie $categorie, Request $request): Response|RedirectResponse
    {
        if (!$categorie instanceof Categorie) {
            $this->addFlash('error', 'Article not found');

            return $this->redirectToRoute('admin.categorie.index');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($categorie, true);

            $this->addFlash('success', 'Category modified  successfully');

            return $this->redirectToRoute('admin.categorie.index');
        }

        return $this->render('Backend/Categorie/update.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name:'.delete', methods: ['POST', 'DELETE'])]
    public function delete(?Categorie $categorie, Request $request): RedirectResponse
    {
        if(!$categorie instanceof Categorie){
            $this->addFlash('error', 'Category not found');
            return $this->redirectToRoute('admin.categorie.index');
        }
        if($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('token'))){
            $this->repo->remove($categorie, true);
            $this->addFlash('success', 'Category deleted successfully');
            return $this->redirectToRoute('admin.categorie.index');
        }
        $this->addFlash('error', 'Token invalide');
        return $this->redirectToRoute('admin.categorie.index');
    }

    
}