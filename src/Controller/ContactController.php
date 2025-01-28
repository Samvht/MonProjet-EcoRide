<?php

namespace App\Controller;

use App\Form\Contact;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods:['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $contactForm= $this->createForm(Contact::class);
        $contactForm->handleRequest($request);

        if($contactForm->isSubmitted() && $contactForm->isValid()){
            $data = $contactForm->getData();

            #création email
            $email = (new Email())
                ->from($data['email'])
                ->to('EcoRide2025@gmail.com')
                ->subject('Nouveau message de contact')
                ->text('Expéditeur : '.$data['email'].'\n\n'.$data['titre'].'\n\n'.$data['message']
            );

            #envoyer l'email
            $mailer->send($email);
            #afficher message confirmation
            $this->addFlash('success', 'Votre message a bien été envoyé!');

            #renvoi vers la même page 
            return $this->redirectToRoute('app_contact');
        }

    #retourne la vue
    return $this->render('contact/contact.html.twig', [
        'contactForm' => $contactForm->createView(),
    ]);

    }
}