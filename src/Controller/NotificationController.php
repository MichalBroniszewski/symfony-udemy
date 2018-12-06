<?php
declare(strict_types=1);
/**
 * File: NotificationController.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Controller;

use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package App\Controller
 * @Security("is_granted('ROLE_USER)")
 * @Route("/notification")
 */
class NotificationController extends Controller
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * NotificationController constructor.
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/unread-count", name="notification_unread")
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return JsonResponse
     */
    public function unreadCount()
    {
        return new JsonResponse([
           'count' => $this->notificationRepository->findUnseenByUser($this->getUser())
        ]);
    }
}
