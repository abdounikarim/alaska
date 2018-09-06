<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin")
     */
    public function index(TicketRepository $ticketRepository, CommentRepository $commentRepository, UserRepository $userRepository)
    {
        return $this->render('admin/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
            'comments' => $commentRepository->findAllFlagedComments(),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ticket/new", name="ticket_new", methods="GET|POST")
     */
    public function newTicket(Request $request, FileUploader $fileUploader): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $ticket->getImage();
            $imageName = $fileUploader->upload($image);
            $ticket->setImage($imageName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/unflag/{id}", name="unflag")
     */
    public function unflag(Comment $comment)
    {
        $comment->setFlag(0);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('unflag', 'Le commentaire a été désignalé');
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/comment/{id}", name="comment_delete")
     */
    public function delete(Comment $comment): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        $this->addFlash('comment_delete', 'Le commentaire a été supprimé');

        return $this->redirectToRoute('admin');
    }
}
