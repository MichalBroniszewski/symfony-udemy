<?php
declare(strict_types=1);
/**
 * File: Mailer.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Mailer;

use App\Entity\User;

/**
 * Class Mailer
 * @package App\Mailer
 */
class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $mailFrom;

    /**
     * UserSubscriber constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     * @param string $mailFrom
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig,
        string $mailFrom
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailFrom = $mailFrom;
    }

    /**
     * @param User $user
     * @return void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render('email/registration.html.twig', [
            'user' => $user
        ]);

        $message = new \Swift_Message();
        $message
            ->setSubject('Test email symfony')
            ->setFrom($this->mailFrom)
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
