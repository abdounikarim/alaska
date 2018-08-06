<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 3; $i++) {
            $ticket = new Ticket();
            $ticket->setTitle('Billet');
            $ticket->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ac lacus sit amet massa consectetur gravida. Aliquam nulla risus, eleifend a turpis a, suscipit feugiat elit. Maecenas nulla ligula, hendrerit et consequat ut, sodales eget quam. Sed cursus mauris sit amet ipsum semper venenatis. Vivamus sagittis risus efficitur fringilla lacinia. Ut lacinia lorem at sodales mollis. Vestibulum fringilla magna diam, sed euismod nunc semper sit amet. Sed quis velit semper, tincidunt dolor id, porta odio. In eu odio convallis, dignissim erat vitae, volutpat urna. Proin felis magna, tincidunt eget ornare sit amet, feugiat eu orci.');
            $ticket->setAuthor($this->getReference('author'));
            $ticket->setImage('e8a95f8ba98ba5ad53b5c14468583dd5.jpeg');
            $ticket->setAlt('Bienvenue en Alaska');
            $ticket->setPublished(true);
            $ticket->setCreatedAt(new \DateTime());
            $ticket->setUpdatedAt(new \DateTime());

            $manager->persist($ticket);
            $this->addReference('ticket-'.$i, $ticket);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
