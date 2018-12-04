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
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
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
     * @return Response
     * @Route("/", name="micro_post_index")
     */
    public function index()
    {
        return $this->render(
            'micro-post/index.html.twig', [
                'posts' => $this->microPostRepository->findBy(
                    [],
                    ['time' => 'DESC']
                )
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
        $microPost->setTime(new \DateTime('now'));
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
