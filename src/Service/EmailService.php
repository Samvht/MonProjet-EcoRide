<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
}
