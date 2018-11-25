<?php
declare(strict_types=1);
/**
 * File: BlogController.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * BlogController constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return Response
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        return $this->render(
            'blog/index.html.twig',
            [
                'posts' => $this->session->get('posts')
            ]
        );
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[uniqid()] = [
            'title' => 'A random title' . rand(1, 30),
            'text' => 'Random text' . rand(1, 300),
            'date' => new \DateTime()
        ];
        $this->session->set('posts', $posts);

        return $this->redirect($this->generateUrl('blog_index'));
    }

    /**
     * @param $id
     * @return Response
     * @Route("/show/{id}", name="blog_show")
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');

        if (!$posts || !isset($posts[$id])) {
            throw new NotFoundHttpException('Post not found');
        }

        return $this->render(
            'blog/post.html.twig',
            [
                'id' => $id,
                'post' => $posts[$id]
            ]
        );
    }
}
