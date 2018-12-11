<?php
declare(strict_types=1);
/**
 * File: UserSubscriber.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\EventListener;

use App\Event\UserRegisterEvent;
use App\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UserSubscriber
 * @package App\EventListener
 */
class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    /**
     * @param UserRegisterEvent $event
     * @return void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function onUserRegister(UserRegisterEvent $event)
    {
        $this->mailer->sendConfirmationEmail($event->getRegisteredUser());
    }
}
