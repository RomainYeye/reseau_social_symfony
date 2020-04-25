<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Commentary;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CommentaryFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user= $this->createUser(150);
        $manager->persist($user);
        $publication = $this->createPublication($user);
        $manager->persist($publication);
        // create 10 commentaries! Bam!
        for ($i = 0; $i < 10; $i++) {
            $userCommentator = $this->createUser($i);
            $manager->persist($userCommentator);
            $commentary = new Commentary();
            $commentary->setComment("comment-" . $i)
                       ->setUser($userCommentator)
                       ->setPublication($publication);
            $manager->persist($commentary);
        }

        $manager->flush();
    }

    private function createUser(int $i){
        $user= new User();
        $user->setUsername("Pascal-$i")
             ->setPassword($this->encoder->encodePassword($user, '1234'))
             ->setEmail("Pascal-$i@test.fr");
        return $user; 
    }

    private function createPublication(User $user) {
        $publication = new Publication();
        $date = DateTime::createFromFormat("Y-m-d H:i:s", "2020-02-18 19:47:05");
        $publication->setTitre("Publication Test")
                    ->setMessage("Message test qui a suffisamment de caractères.")
                    ->setDate($date);
        $user->addPublication($publication);
        $publication->setUser($user);
        return $publication;
    }

}

