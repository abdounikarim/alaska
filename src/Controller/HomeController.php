<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\ContactType;
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
        return $this->render('alaska/index.html.twig', ['tickets' => $ticketRepository->findAllPublishedTicketsWithNumberOfComments()]);
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
        return $this->render('alaska/ticket.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/comment/new", name="comment_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
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

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find(1);
        return $this->render('alaska/about.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            $this->addFlash('contact', 'Merci ! Votre message a bien été envoyé');
            return $this->redirectToRoute('home');
        }
        return $this->render('alaska/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
