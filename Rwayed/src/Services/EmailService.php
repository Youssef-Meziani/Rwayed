<?php

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    private MailerInterface $mailer;
    private string $adminEmail;

    public function __construct(MailerInterface $mailer, string $adminEmail)
    {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function sendEmail(array $emailDetails): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->adminEmail, $emailDetails['from_name']))
            ->to($emailDetails['to'])
            ->subject($emailDetails['subject'])
            ->htmlTemplate($emailDetails['template'])
            ->context($emailDetails['context']);

        $this->mailer->send($email);
    }
}
