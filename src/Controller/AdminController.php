<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\TicketType;
use App\Form\UserType;
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
            $ticket->setCreatedAt(new \DateTime());
            $image = $ticket->getImage();
            $imageName = $fileUploader->upload($image);
            $ticket->setImage($imageName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();
            $this->addFlash('add_ticket', 'Le nouveau billet a bien été ajouté');

            return $this->redirectToRoute('home');
        }

        return $this->render('admin/ticket_add.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ticket/{id}", name="ticket_show", methods="GET")
     */
    public function show(Ticket $ticket): Response
    {
        return $this->render('admin/ticket.html.twig', ['ticket' => $ticket]);
    }

    /**
     * @Route("/ticket/{id}/edit", name="ticket_edit", methods="GET|POST")
     */
    public function editTicket(Request $request, Ticket $ticket, FileUploader $fileUploader): Response
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
            $this->addFlash('edit_ticket', 'Le billet a bien été modifié');

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/ticket_edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ticket/{id}", name="ticket_delete", methods="DELETE")
     */
    public function deleteTicket(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ticket->getComments() as $comment)
            {
                $em->remove($comment);
            }
            $em->remove($ticket);
            $em->flush();
        }
        $this->addFlash('delete_ticket', 'Le billet a bien été supprimé');

        return $this->redirectToRoute('admin');
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
    public function deleteComment(Comment $comment): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        $this->addFlash('comment_delete', 'Le commentaire a été supprimé');

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function editUser(Request $request, User $user, FileUploader $fileUploader)
    {
        $imageOld = $user->getImage();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $image = $user->getImage();
            if($image != null) {
                $imageName = $fileUploader->upload($image);
                $user->setImage($imageName);
            } else {
                $user->setImage($imageOld);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('edit_user', 'L\'utilisateur a bien été modifié');

            return $this->redirectToRoute('admin');
        }
        return $this->render('admin/user_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
