<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('Jean Forteroche');
        $user->setEmail('jf@alaska.fr');
        $password = $this->encoder->encodePassword($user, 'alaska');
        $user->setPassword($password);
        $roles = ['ROLE_ADMIN'];
        $user->setRoles($roles);

        $manager->persist($user);
        $this->addReference('author', $user);
        $manager->flush();
    }
}
