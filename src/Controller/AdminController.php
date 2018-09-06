<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(TicketRepository $ticketRepository, CommentRepository $commentRepository, UserRepository $userRepository)
    {
        return $this->render('admin/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
            'comments' => $commentRepository->findAllFlagedComments(),
            'users' => $userRepository->findAll(),
        ]);
    }
}
