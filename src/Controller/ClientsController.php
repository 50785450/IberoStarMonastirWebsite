<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client;

class ClientsController extends AbstractController
{
    private $titles = ['mr', 'ms', 'mrs', 'dr', 'mx'];


    /**
     * @Route("/guests", name="index_clients")
     */
    public function index()
    {
        $data = [];
        //$data['clients'] = $this->client_data;
        $client = $this->getDoctrine()
                        ->getRepository('App:Client')
                        ->findAll();
        $data['clients']= $client;
        return $this->render('clients/index.html.twig', $data);
    }


    /**
     * @Route("/guests/modify/{id_client}", name="modify_clients")
     */
    public function details(Request $request, $id_client)
    {
        /*return $this->render('clients/index.html.twig', [
            'controller_name' => 'ClientsController',
        ]);*/
        $data = [];
        //$data['clients'] = $this->client_data;
        $client_repo = $this->getDoctrine()
                            ->getRepository('App:Client');
        $data['mode'] = 'modify';
        $data['form'] = [];
        $data['titles'] = $this->titles;

        $form = $this ->createFormBuilder()
                      ->add('name')
                      ->add('last_name')
                      ->add('title')
                      ->add('address')
                      ->add('zip_code')
                      ->add('city')
                      ->add('state')
                      ->add('email')
                      ->getForm();

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            $form_data = $form->getData();
            $data['form'] = [];
            $data['form'] = $form_data;
            $client = $client_repo->find($id_client);

            $client->setTitle($form_data['title']);
            $client->setName($form_data['name']);
            $client->setLastName($form_data['last_name']);
            $client->setAddress($form_data['address']);
            $client->setCity($form_data['city']);
            $client->setZipCode($form_data['zip_code']);
            $client->setState($form_data['state']);
            $client->setEmail($form_data['email']);

            $em = $this->getDoctrine()
                        ->getManager();
            $em->flush();

            return $this->redirectToRoute("index_clients");
            


        }else{
            //$client_data = $this->client_data[$id_client];
            $client = $client_repo->find($id_client);
            $client_data['id'] = $client->getId();
            $client_data['title'] = $client->getTitle();
            $client_data['name'] = $client->getName();
            $client_data['last_name'] = $client->getLastName();
            $client_data['address'] = $client->getAddress();
            $client_data['city'] = $client->getCity();
            $client_data['zip_code'] = $client->getZipCode();
            $client_data['state'] = $client->getState();
            $client_data['email'] = $client->getEmail();

            $client_data['titles'] = $this->titles;
            $data['form'] = $client_data;
        }


        return $this->render('clients/form.html.twig', $data);
    }



    /**
     * @Route("/guests/new", name="new_client")
     */
    public function new(Request $request)
    {
        /*return $this->render('clients/index.html.twig', [
            'controller_name' => 'ClientsController',
        ]);*/
        $data = [];
        $data['mode'] = 'new_client';
        $data['titles'] = $this->titles;
        $data['form'] = [];
        $data['form']['title'] = '';

        $form = $this ->createFormBuilder()
                      ->add('name')
                      ->add('last_name')
                      ->add('title')
                      ->add('address')
                      ->add('zip_code')
                      ->add('city')
                      ->add('state')
                      ->add('email')
                      ->getForm();

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            $form_data = $form->getData();
            $data['form'] = [];
            $data['form'] = $form_data;
            
            $em = $this->getDoctrine()->getManager();
            $client = new Client();
            $client->setTitle($form_data['title']);
            $client->setName($form_data['name']);
            $client->setLastName($form_data['last_name']);
            $client->setAddress($form_data['address']);
            $client->setZipCode($form_data['zip_code']);
            $client->setCity($form_data['city']);
            $client->setState($form_data['state']);
            $client->setEmail($form_data['email']);

            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute("index_clients");

        }

        return $this->render('clients/form.html.twig', $data);

    }


}
