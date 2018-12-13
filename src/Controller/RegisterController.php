<?php
declare(strict_types=1);
/**
 * File: RegisterController.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\Form\UserType;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegisterController
 * @package App\Controller
 */
class RegisterController extends Controller
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * RegisterController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        TokenGenerator $tokenGenerator
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/register", name="user_register")
     * @throws \Exception
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($password);
            $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken(30));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $userRegisterEvent = new UserRegisterEvent($user);
            $this->eventDispatcher->dispatch(
                UserRegisterEvent::NAME,
                $userRegisterEvent
            );

            $this->addFlash('notice', 'You\'ve successfully registered to test app!');

            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render(
            'register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
