<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category_list')]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/details/{id}", name="category_details")
     */
    public function detailsAction(ManagerRegistry $doctrine, $id)
    {
        $category = $doctrine->getRepository(Category::class)->find($id);

        return $this->render('category/details.html.twig', [ 'category' => $category
        ]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function deleteCategory(ManagerRegistry $doctrine,$id)
    {
        $em = $doctrine->getManager();
        $categories = $em->getRepository(Category::class)->find($id);
        $em->remove($categories);
        $em->flush();

        $this->addFlash(
            'error',
            'Category deleted'
        );
        return $this->redirectToRoute('product_list');
    }


    /**
     * @Route("/category/create", name="category_create", methods={"GET","POST"})
     */
    public function createAction(ManagerRegistry$doctrine,Request $request, SluggerInterface $slugger)
    {
        $categories = new Category();
        $form = $this->createForm(CategoryType::class, $categories);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($categories);
            $em->flush();

            $this->addFlash(
                'notice',
                'Category Added Success'
            );
            return $this->redirectToRoute(Category::class);
        }
        return $this->renderForm('category/create.html.twig', ['form' => $form, 'categories'=>$categories]);
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function EditCategory(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager  = $doctrine->getManager();
        $categories = $entityManager->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, @$categories);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $em->persist($categories);
            $em->flush();
            return $this->redirectToRoute(Category::class, [
                'id' => $categories->getId()
            ]);
        }
        return $this->renderForm('category/edit.html.twig', ['form' => $form, 'categories' => $categories]);
    }

    #[Route('/category/categoryByCat', name: 'categoryByCat')]
    public function categoryByCatAction(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAll();
        $categories = $doctrine->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'products' => $products, 'categories'=>$categories
        ]);
    }
    #[Route('/category/details/{id}', name: 'category_details')]
    public function categorydetails( $id, ManagerRegistry $doctrine): Response
    {
        $category_id = $doctrine->getManager();
        $categories = $category_id->getRepository(Category::class)->find($id);
        $products = $categories;
        return $this->render('category/details.html.twig',['products' => $products,'categories'=>$categories]);
    }
}


