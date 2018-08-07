<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentType;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home", methods="GET")
     */
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/index.html.twig', ['tickets' => $ticketRepository->findAllPublishedTicketsWithNumberOfComments()]);
    }

    /**
     * @Route("/ticket/{id}", name="ticket", methods="GET|POST")
     */
    public function show(Request $request, Ticket $ticket): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setFlag(false);
            $comment->setTicket($ticket);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('add_comment', 'Le commentaire a été ajouté');
            return $this->redirectToRoute('ticket', [
                'id' => $ticket->getId()
            ]);
        }
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/comment/{id}/flag", name="flag", methods="GET")
     */
    public function flag(Comment $comment)
    {
        $comment->setFlag(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        $this->addFlash('flag', 'Le commentaire a été signalé');
        return $this->redirectToRoute('ticket', [
            'id' => $comment->getTicket()->getId()
        ]);
    }
}
