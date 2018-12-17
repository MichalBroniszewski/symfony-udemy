<?php
declare(strict_types=1);
/**
 * File: MailerTest.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Tests\Mailer;

use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class MailerTest
 * @package App\Tests\Mailer
 */
class MailerTest extends TestCase
{
    /**
     * @var User|MockObject
     */
    private $user;
    /**
     * @var Mailer|MockObject
     */
    private $mailer;
    /**
     * @var \Swift_Mailer|MockObject
     */
    private $swiftMailer;
    /**
     * @var \Twig_Environment|MockObject
     */
    private $twig;
    /**
     * @var string
     */
    private $from;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->user = $user = new User();

        $this->swiftMailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->twig = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->from = 'test@aaa.com';

        $this->mailer = new Mailer(
            $this->swiftMailer,
            $this->twig,
            $this->from
        );
    }

    /**
     * @return void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testSendConfirmationEmail()
    {
        $this->user->setEmail('test@aaa.com');

        $this->twig->expects($this->once())
            ->method('render')
            ->with('email/registration.html.twig', [
                'user' => $this->user
            ]);

        $this->swiftMailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($subject) {
                $messageStr = (string)$subject;

                return strpos($messageStr, sprintf('From: %s', $this->from));
            }));

        $this->mailer->sendConfirmationEmail($this->user);
    }
}
