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
        $user->setImage('jeanforteroche.png');
        $user->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus et enim risus. Etiam euismod, justo ac blandit dapibus, odio risus pharetra odio, eu gravida turpis nulla ut leo. Nullam dignissim, nibh at fringilla auctor, nibh turpis tempus neque, a egestas tellus augue eget urna. Nullam a dui velit. Nulla eget ultricies urna. Integer sagittis imperdiet tincidunt. Suspendisse tristique mollis enim sodales blandit. Mauris non turpis a nulla venenatis faucibus sed quis est. Proin lacinia finibus arcu at varius. Pellentesque blandit ullamcorper odio, vel tempus tellus imperdiet interdum. Sed dignissim sem a lectus facilisis, ac porttitor ipsum tempor. Integer vel justo eget tellus placerat viverra. Mauris vel orci in odio congue vestibulum. Sed mattis, felis feugiat pulvinar commodo, nibh urna elementum augue, nec feugiat quam massa ac elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;

Mauris aliquam massa non tortor lobortis iaculis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis porttitor risus ligula, id semper ex tincidunt sed. Nam vitae quam vel mauris sodales interdum at ut lectus. Sed vehicula nisl eget turpis interdum, et laoreet purus vehicula. Aliquam ullamcorper suscipit nulla in venenatis. Duis vitae pharetra nisi. Ut pharetra velit vitae dui auctor, et ultricies diam hendrerit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur malesuada, quam sit amet scelerisque vehicula, erat metus finibus urna, eu tempor augue odio eu neque. Pellentesque posuere, felis consectetur euismod facilisis, lacus risus congue ligula, eu gravida quam ligula in velit. Etiam sit amet sem laoreet, luctus velit nec, lobortis lectus. Aliquam feugiat tortor non ultricies semper. Suspendisse potenti.

Phasellus non lacus arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce vitae aliquam ligula. Mauris convallis venenatis arcu, sed iaculis enim blandit non. Maecenas ac lectus urna. Cras et posuere tellus, a molestie lorem. Etiam et ultricies odio.

Duis eget felis nibh. Etiam pulvinar, turpis in gravida ullamcorper, nisl nulla tempus odio, sit amet mattis metus lorem sed lectus. Nullam quam lectus, lobortis in finibus vitae, commodo a purus. Sed vel augue tellus. Sed laoreet ac enim nec pharetra. Donec viverra auctor libero, scelerisque rutrum mi vehicula nec. Fusce mattis quam tortor, eu tincidunt purus efficitur a.

Suspendisse consequat erat risus, ac fermentum enim volutpat id. Aliquam in erat urna. Phasellus pharetra velit ut est placerat, vitae rutrum neque tempor. Duis justo turpis, auctor sit amet leo vitae, ultrices scelerisque eros. Sed pellentesque neque sit amet finibus dapibus. Ut finibus mauris blandit velit placerat luctus. Curabitur nec felis aliquam, dictum nulla id, malesuada sapien. Mauris consequat nisl a arcu pharetra, vel ultricies velit tincidunt. Vestibulum sed ante erat.

');
        $manager->persist($user);
        $this->addReference('author', $user);
        $manager->flush();
    }
}
