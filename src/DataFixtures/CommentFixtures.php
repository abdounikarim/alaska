<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++) {
            $comment = new Comment();
            $comment->setPseudo('Toto');
            $comment->setMessage('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ac lacus sit amet massa consectetur gravida. Aliquam nulla risus, eleifend a turpis a, suscipit feugiat elit. Maecenas nulla ligula, hendrerit et consequat ut, sodales eget quam. Sed cursus mauris sit amet ipsum semper venenatis. Vivamus sagittis risus efficitur fringilla lacinia. Ut lacinia lorem at sodales mollis. Vestibulum fringilla magna diam, sed euismod nunc semper sit amet. Sed quis velit semper, tincidunt dolor id, porta odio. In eu odio convallis, dignissim erat vitae, volutpat urna. Proin felis magna, tincidunt eget ornare sit amet, feugiat eu orci.');
            $comment->setCreatedAt(new \DateTime());
            $flag = rand(0, 1);
            if($flag == 0) {
                $flag = false;
            } else {
                $flag = true;
            }
            $comment->setFlag($flag);
            $ticket = rand(1, 3);
            if($ticket == 1){
                $ticket_reference = 'ticket-1';
            } else if($ticket == 2) {
                $ticket_reference = 'ticket-2';
            } else {
                $ticket_reference = 'ticket-3';
            }
            $comment->setTicket($this->getReference($ticket_reference));
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TicketFixtures::class,
        ];
    }
}
