<?php
declare(strict_types=1);
/**
 * File: Greeting.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Service;

use Psr\Log\LoggerInterface;

/**
 * Class Greeting
 * @package App\Service
 */
class Greeting
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Greeting constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $name
     * @return string
     */
    public function greet(string $name): string
    {
        $this->logger->info("Greeted $name");
        return "Hello $name";
    }
}
