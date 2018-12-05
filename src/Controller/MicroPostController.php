<?php
declare(strict_types=1);
/**
 * File: MicroPostController.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MicroPostController
 * @package App\Controller
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * MicroPostController constructor.
     * @param MicroPostRepository $microPostRepository
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserRepository $userRepository
     * @return Response
     * @Route("/", name="micro_post_index")
     */
    public function index(UserRepository $userRepository)
    {
        $currentUser = $this->getUser();
        $usersToFollow = [];

        if ($currentUser instanceof User) {
            $followingUsers = $currentUser->getFollowing();
            $posts = $this->microPostRepository->findAllByUsers($followingUsers);

            $usersToFollow = count($posts) === 0 ?
                $userRepository->findAllWithMoreThanFivePostsExceptUser($currentUser)
                : [];
        } else {
            $posts = $this->microPostRepository->findBy(
                [],
                ['time' => 'DESC']
            );
        }

        return $this->render(
            'micro-post/index.html.twig', [
                'posts' => $posts,
                'usersToFollow' => $usersToFollow
            ]
        );
    }

    /**
     * @param MicroPost $microPost
     * @param Request $request
     * @return Response
     * @Route("/edit/{id}", name="micro_post_edit")
     */
    public function edit(MicroPost $microPost, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $microPost);
        $form = $this->formFactory->create(
            MicroPostType::class,
            $microPost
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('micro_post_index'));
        }

        $this->addFlash(
            'notice',
            sprintf('Micro post nr.%s was successfully edited!', $microPost->getId())
        );

        return $this->render(
            'micro-post/add.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @param MicroPost $microPost
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/delete/{id}", name="micro_post_delete")
     */
    public function delete(MicroPost $microPost)
    {
        $this->denyAccessUnlessGranted('delete', $microPost);
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->addFlash('notice', 'Micro post was deleted.');

        return $this->redirect($this->generateUrl('micro_post_index'));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request)
    {
        $user = $this->getUser();
        $microPost = new MicroPost();
        $microPost->setUser($user);

        $form = $this->formFactory->create(
            MicroPostType::class,
            $microPost
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return $this->redirect($this->generateUrl('micro_post_index'));
        }

        return $this->render(
            'micro-post/add.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     * @param User $userWithPosts
     * @return Response
     */
    public function userPosts(User $userWithPosts)
    {
        return $this->render(
            'micro-post/user-posts.html.twig', [
                'posts' => $this->microPostRepository->findBy(
                    ['user' => $userWithPosts],
                    ['time' => 'DESC']
                ),
                'user' => $userWithPosts
            ]
        );
    }

    /**
     * @param MicroPost $post
     * @return Response
     * @Route("/{id}", name="micro_post_post")
     */
    public function post(MicroPost $post)
    {
        return $this->render(
            'micro-post/post.html.twig', [
                'post' => $post
            ]
        );
    }
}
