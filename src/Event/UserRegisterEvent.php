<?php
declare(strict_types=1);
/**
 * File: UserRegisterEvent.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Event;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserRegisterEvent
 * @package App\Event
 */
class UserRegisterEvent extends Event
{
    /** @var string  */
    const NAME = 'user.register';

    /**
     * @var User
     */
    private $registeredUser;

    /**
     * UserRegisterEvent constructor.
     * @param User $registeredUser
     */
    public function __construct(User $registeredUser)
    {
        $this->registeredUser = $registeredUser;
    }

    /**
     * @return User
     */
    public function getRegisteredUser(): User
    {
        return $this->registeredUser;
    }
}
