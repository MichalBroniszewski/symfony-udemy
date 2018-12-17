<?php
declare(strict_types=1);
/**
 * File: TokenGeneratorTest.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Tests\Security;

use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Class TokenGeneratorTest
 * @package App\Tests\Security
 */
class TokenGeneratorTest extends TestCase
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testTokenGeneration()
    {
        $expectedLength = 30;
        $tokenGenerator = new TokenGenerator();
        $token = $tokenGenerator->getRandomSecureToken(30);

        $this->assertEquals($expectedLength, strlen($token));
        $this->assertTrue(ctype_alnum($token), 'Token contains incorrect characters');
    }
}
