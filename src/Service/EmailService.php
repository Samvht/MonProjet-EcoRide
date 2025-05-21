<?php

namespace App\Service;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendCancellationEmail(array $participants, $covoiturage)
    {
        foreach ($participants as $participant) {
            $email = (new Email())
                ->from('EcoRide2025@gmail.com')
                ->to($participant->getEmail())
                ->subject('Annulation de Covoiturage')
                ->html('<p>Bonjour ' . $participant->getPseudo() . ',</p><p>Le covoiturage prévu le ' . $covoiturage->getDateDepart() . ' a été annulé.</p>');

            $this->mailer->send($email);
        }
    }

    public function sendAvisInvitationEmail(Utilisateur $participant, Covoiturage $covoiturage): void
{
    $email = (new TemplatedEmail())
        ->from('no-reply@ecoride.com')
        ->to($participant->getEmail())
        ->subject('Partage ton avis sur ton covoiturage')
        ->htmlTemplate('email/avis.html.twig')
        ->context([
            'participant' => $participant,
            'covoiturage' => $covoiturage,
        ]);

    $this->mailer->send($email);
}
}
