<?php

namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Form\ContactType;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\SpreadsheetService;


use Acme\DemoBundle\Entity\Product;


class DemoController extends Controller
{
    /**
     * @Route("/", name="_demo")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/hello/{name}", name="_demo_hello")
     * @Template()
     */
    public function helloAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/google/spreadsheet", name="json_response")
     * @Template
     */
    public function googleSpreadSheetAction()
    {

        //$accessToken = "1TzbsF_GCpzLycDqD-RC2xaKKJOjOP1evNNzwc0ci7FI";
        //$serviceRequest = new DefaultServiceRequest($accessToken);
        //ServiceRequestFactory::setInstance($serviceRequest);

        //$instance = ServiceRequestFactory::getInstance();

        //$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
        //var_dump($instance);
        //die('stop');



        return array('name'=>'piotr');
    }



    /**
     * @Route("/category/add", name="add_category")
     * @Template()
     */
    public function categoryAddAction()
    {
        $em = $this->getDoctrine()->getManager();


        for($i = 0 ; $i < 10; $i++) {
            $category = new Category();
            $category->setName('Main Products');

            $category->setName( 'Category_' . $i );
            $em->persist($category);

            echo ' cat : ' . $i . '-added' . '<br/>';
        }

        $em->flush();

        die('category added');

        return array();
    }


    /**
     * @Route("/product/add", name="add_product")
     * @Template()
     */
    public function productAddAction()
    {
        $em = $this->getDoctrine()->getManager();

        $category = new Category();
        $category->setName('Main Products');
        $em->persist($category);

        for($i = 0 ; $i < 1000; $i++) {
            $product = new Product();
            $product->setName( 'A Foo Bar_' . $i );
            $product->setPrice( '19.99' . $i );
            $product->setDescription( 'Lorem ipsum dolor:' . $i );
            $product->setCategory($category);
            $em->persist($product);

            echo ' p : ' . $i . '-added' . '<br/>';
        }

        $em->flush();

        die('product added');

        return array();
    }



    /**
     * @Route("/product/showAll", name="show_all_product")
     * @Template()
     */
    public function prodcutShowAllAction()
    {
        $products = $this->getDoctrine()
            ->getRepository('AcmeDemoBundle:Product')
            ->findAll();

        foreach($products as $product) {
            echo 'name: ' . $product->getName() . '<br/>';
            echo 'price: ' . $product->getPrice() . '<br/>';
            echo 'description: ' . $product->getDescription() . '<br/>';
            echo 'category: ' . $product->getCategory()->getName() . '<br/>';
            echo '-----------------------------------------------------<br/>';
        }
//        foreach($products in $product) {
//            echo ( 'name: ' . $product->getName() . '<br/>');
//        }

        die;

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        return array();
    }

    /**
     * @Route("/product/showByCat/{id}", name="product_show_by_category")
     * @Template()
     */
    public function productShowByCatAction($id)
    {
        $products = $this->getDoctrine()->getRepository('AcmeDemoBundle:Product')->findOneByIdJoinedToCategory($id);
        var_dump($products); die;

    }

    /**
     * @Route("/product/update/{id}", name="update_product")
     * @Template()
     */
    public function productUpdateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AcmeDemoBundle:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $product->setName('New product name!');
        $em->flush();

        die('product updated id: ' . $id);
        return array();
    }

    /**
     * @Route("/product/delete/{id}", name="delete_product")
     * @Template()
     */
    public function productDelete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AcmeDemoBundle:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $em->remove($product);
        $em->flush();
    }

    /**
     * @Route("/product/category/{id}", name="category_product")
     * @Template()
     */
    public function productCategoryAction($id)
    {
        $category = $this->getDoctrine()->getManager()->getRepository('AcmeDemoBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found for id '.$id
            );
        }

        $products = $category->getProducts();


        foreach($products as $product) {
            echo 'name: ' . $product->getName() . '<br/>';
            echo 'price: ' . $product->getPrice() . '<br/>';
            echo 'description: ' . $product->getDescription() . '<br/>';
            echo 'category: ' . $product->getCategory()->getName() . '<br/>';
            echo '-----------------------------------------------------<br/>';
        }
        die('products in category');
    }




    /**
     * @Route("/contact", name="_demo_contact")
     * @Template()
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(new ContactType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $mailer = $this->get('mailer');

            // .. setup a message and send it
            // http://symfony.com/doc/current/cookbook/email.html

            $request->getSession()->getFlashBag()->set('notice', 'Message sent!');

            return new RedirectResponse($this->generateUrl('_demo'));
        }

        return array('form' => $form->createView());
    }
}
