<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * @Route("/product", name="Product_")
 */
class ProductController extends Controller
{

	/**
	 * @Route ("/", name="index")
	 */
	public function index()
	{
		$products = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('Product/index.html.twig', [
            'products' => $products
        ]);
	}

	/**
	 * @Route ("/create", name="create")
	 */
	public function create(Request $request)
	{
		$product = new Product();

        $form = $this->createProductForm($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été sauvegardé.');

            return $this->redirectToRoute('Product_detail', [
                'id' => $product->getId(),
            ]);
           }
        return $this->render('Product/create.html.twig', [
            'form' => $form->createView(),
        ]);
	}

	/**
	 * @Route ("/{id}", name="detail")
	 */
	public function detail(Request $request)
	{
		$product = $this->findProduct($request);

        return $this->render('Product/detail.html.twig', [
            'product' => $product,
        ]);
	}

	/**
	 * @Route ("/{id}/update", name="update")
	 */
	public function update(Request $request)
	{
		$product = $this->findProduct($request);

        $form = $this->createProductForm($product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été modifié.');

            return $this->redirectToRoute('Product_detail', [
                'id' => $product->getId()
            ]);
            }

        return $this->render('Product/update.html.twig', [
            'form' => $form->createView(),
        ]);
	}

	/**
	 * @Route ("/{id}/delete", name="delete")
	 */
	public function delete()
	{
		return new Response("supression d'un formulaire 'delete.html.twig'");
	}

	 private function createProductForm(Product $product)
    {
        return $this
            ->createFormBuilder($product)

            ->add('designation')
            ->add('reference')
            ->add('brand')
            ->add('price')
            ->add('stock')
            ->add('active', Type\CheckboxType::class, [
                'required' => false
            ])
            ->add('submit', Type\SubmitType::class)
            ->getForm();
    }

    private function findProduct(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);

        $product = $repository->find(
            $request->attributes->get('id')
        );

        if (null === $product) {
            throw $this->createNotFoundException(
                "Product not found"
            );
        }
        return $product;
    }
}