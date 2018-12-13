<?php
declare(strict_types=1);
/**
 * File: SecurityController.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $authenticationUtils->getLastUsername(),
                'error' => $authenticationUtils->getLastAuthenticationError()
            ]
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     * @param string $token
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirm(string $token, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy([
            'confirmationToken' => $token
        ]);

        if (null !== $user) {
            $user->setEnabled(true);
            $user->setConfirmationToken('');

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('security/confirmation.html.twig', [
            'user' => $user
        ]);
    }
}
