<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
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
