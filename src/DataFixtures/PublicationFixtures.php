<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Publication;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PublicationFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        
        $user= $this->createUser();
        $manager->persist($user);
        // create 9 publications! Bam!
        for ($i = 0; $i < 10; $i++) {
            $publication = new Publication();
            $date = DateTime::createFromFormat("Y-m-d H:i:s", "2020-04-0$i 0$i:0$i:0$i");
            $publication->setTitre("titre-" . $i)
                        ->setMessage("message-" . $i)
                        ->setDate($date)
                        ->setUser($user);
            $manager->persist($publication);
        }

        $manager->flush();
    }

    private function createUser(){
        $user= new User();
        $user->setUsername("leLoulou")
             ->setPassword($this->encoder->encodePassword($user, '1234'))
             ->setEmail("leLoulou@test.fr");
        return $user; 
    }

}