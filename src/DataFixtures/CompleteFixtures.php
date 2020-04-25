<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Friendship;
use App\Entity\Publication;
use App\Entity\Like;
use App\Entity\Commentary;
use DateTime;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Controller\Traits\createNewNotificationTrait;
use App\Controller\Traits\createNewNotifyToTrait;
use App\Entity\NotifyTo;

class CompleteFixtures extends Fixture
{
    use createNewNotificationTrait;
    use createNewNotifyToTrait;

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // create 10 users! Bam!
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername("name-" . $i);
            $user->setPassword($this->encoder->encodePassword($user, '1234'));
            $user->setEmail("email-" . $i . "@test.fr");
            $manager->persist($user);
            array_push($users, $user);
        }
        
        //create friendships! Bam!
        $numberOfUsers = count($users);
        for($i=0; $i<$numberOfUsers-1; $i++ ) {
            $counterOfFriendships = rand(0, $numberOfUsers-($i+1));

            for($j=$i; $j<$i + ($counterOfFriendships-1); $j++) {
                $friend = $users[$j+1];

                $firstFriendship = new Friendship();
                $firstFriendship->setUser($users[$i])
                                ->setFriend($friend);
                $manager->persist($firstFriendship);
                $users[$i]->addFriend($firstFriendship);

                $reverseFriendship = new Friendship();
                $reverseFriendship->setUser($friend)
                                ->setFriend($users[$i]);
                $manager->persist($reverseFriendship);
                $friend->addFriend($reverseFriendship);

            }
        }

        //create publications! bam!
        $publications = [];
        foreach($users as $user) {
            for($i=0; $i<rand(0,3); $i++) {
                $publication = new Publication();
                $date = DateTime::createFromFormat("Y-m-d H:i:s", "2020-04-0$i 0$i:0$i:0$i");
                $publication->setUser($user)
                            ->setTitre("Mon titre $i")
                            ->setMessage(str_repeat("Je dirai " . ($i+1) . " fois ça dans ma publication. ", $i+1))
                            ->setDate($date);
                $manager->persist($publication);
                array_push($publications, $publication);

                $notification = $this->createNewNotification($user, $publication, "publication");
                $manager->persist($notification);

                $userFriendships = $user->getFriends();
                for($i=0; $i<count($userFriendships); $i++) {
                    $notifyTo = $this->sendNewNotifyToCaseNewPublication($userFriendships[$i]->getFriend(), $notification);
                    $manager->persist($notifyTo);
                }
            }
        }
        
        //create likes! bam!
        foreach($publications as $publication) {
            for($i=0; $i<rand(0, 10); $i++) {
                $like = new Like();
                $like->setUser($users[$i])
                     ->setPublication($publication);
                $manager->persist($like);

                $notification = $this->createNewNotification($users[$i], $publication, "like");
                $manager->persist($notification);

                $notifyTo = $this->sendNewNotifyToCaseLikeOrCommentary($publication->getUser(), $notification);
                $manager->persist($notifyTo);
            }
        }

        //create commentaries! bam!
        foreach($publications as $publication) {
            for($i=0; $i<rand(0, 3); $i++) {
                $chiffre = rand(0, 9);
                $commentary = new Commentary();
                $commentary->setUser($users[$i])
                           ->setPublication($publication)
                           ->setComment(str_repeat("Cool ta publication ! Je la note $chiffre/$chiffre ! ", $i+1));
                $manager->persist($commentary);

                $notification = $this->createNewNotification($users[$i], $publication, "commentaire");
                $manager->persist($notification);

                $notifyTo = $this->sendNewNotifyToCaseLikeOrCommentary($publication->getUser(), $notification);
                $manager->persist($notifyTo);
            }
        }

        $manager->flush();
    }
}
