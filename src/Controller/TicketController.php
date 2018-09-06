<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ticket")
 */
class TicketController extends Controller
{

    /**
     * @Route("/{id}/edit", name="ticket_edit", methods="GET|POST")
     */
    public function edit(Request $request, Ticket $ticket, FileUploader $fileUploader): Response
    {
        $imageOld = $ticket->getImage();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $ticket->getImage();
            if($image != null) {
                $imageName = $fileUploader->upload($image);
                $ticket->setImage($imageName);
            } else {
                $ticket->setImage($imageOld);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_edit', ['id' => $ticket->getId()]);
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_delete", methods="DELETE")
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ticket);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}
