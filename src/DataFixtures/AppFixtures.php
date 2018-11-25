<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $microPost = new MicroPost();
            $microPost->setText('Some random txt '. rand(0, 300));
            $microPost->setTime(new \DateTime('now'));
            $manager->persist($microPost);
        }

        $manager->flush();
    }
}
