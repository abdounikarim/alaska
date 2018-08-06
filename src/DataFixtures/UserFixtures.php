<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('Jean Forteroche');
        $user->setEmail('jf@alaska.fr');
        $user->setPassword('alaska');
        $roles = ['ROLE_ADMIN'];
        $user->setRoles($roles);

        $manager->persist($user);
        $this->addReference('author', $user);
        $manager->flush();
    }
}
