<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reservation;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="reservation")
     */
    public function index()
    {

        $data[] = [];
        $reservation_repo = $this     
                                ->getDoctrine()
                                ->getRepository('App:Reservation');
        
        $reservations = $reservation_repo->getCurrentReservations();
        $data['reservations'] = $reservations;

        return $this->render(   'reservation/index.html.twig', 
                                $data );
    }

    /**
     * @Route("/reservation/{id_client}", name="booking")
     */
    public function book(Request $request, $id_client)
    {
        $data = [];
        $data['rooms'] = null;
        $data['dates']['from'] = '';
        $data['dates']['to'] = '';
        $form = $this   ->createFormBuilder()
                        ->add('dateFrom')
                        ->add('dateTo')
                        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form_data = $form->getData();

            $data['dates']['from'] = $form_data['dateFrom'];
            $data['dates']['to'] = $form_data['dateTo'];

            $em = $this->getDoctrine()->getManager();
            $rooms = $em->getRepository('App:Room')
                ->getAvailableRooms($form_data['dateFrom'], $form_data['dateTo']);   

            $data['rooms'] = $rooms;

        } 
        
        $client = $this
                        ->getDoctrine()
                        ->getRepository('App:Client')
                        ->find($id_client);

        

        
        $data['client'] = $client;
        return $this->render(   'reservation/book.html.twig',
                                $data );

    }

    /**
     * @Route("/book_Room/{id_client}/{id_room}/{date_in}/{date_out}", name="book_Room")
     */
    public function bookRoom($id_client, $id_room, $date_in, $date_out)
    {
        $reservation = new Reservation();
        $date_start = new \DateTime($date_in);
        $date_end = new \DateTime($date_out);
        $reservation->setDateIn($date_start);
        $reservation->setDateOut($date_end);

        $client = $this
                    ->getDoctrine()
                    ->getRepository('App:Client')
                    ->find($id_client);

        $room = $this
                    ->getDoctrine()
                    ->getRepository('App:Room')
                    ->find($id_room);

        $em = $this
                ->getDoctrine()
                ->getManager();

        $room_availability = $em
                                ->getRepository('App:Room')
                                ->checkRoomAvailability($id_room,$date_in, $date_out);

        //Check if there are booked rooms with those dates
        if(!$room_availability)
        {
            //Room is available
            $reservation->setClient($client);
            $reservation->setRoom($room);

            $em->persist($reservation);
            $em->flush();
            
            return $this->redirectToRoute('reservation');

        }else
        {

            throw new \Exception('Room is already booked!');

        }  

    }

     /**
     * @Route("/reservation/cancel/{reservation_id}", name="cancel_booking")
     */
    public function cancel($reservation_id)
    {
        $em = $this
                ->getDoctrine()
                ->getManager();

        $reservation = $em->getReference('App:Reservation', $reservation_id);
        $em->remove($reservation);
        $em->flush();

        return $this->redirectToRoute('reservation');


    }

}
