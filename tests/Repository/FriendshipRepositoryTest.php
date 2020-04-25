<?php

namespace App\Tests\Repository;

use App\DataFixtures\AppFixtures;
use App\Entity\Friendship;
use App\Repository\FriendshipRepository;
use App\Tests\Repository\Traits\createUserTrait;
use App\Tests\Repository\Traits\createPublicationTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class FriendshipRepositoryTest extends WebTestCase {
    use FixturesTrait;
    use createUserTrait;
    use createPublicationTrait;

    /**
     * Prepares the tests
     * @before
     * @return void
     */
    public function setUp() {
        self::bootKernel();
    }

    public function testIfUniqueFriendshipIsValid() {

        $userJacques = $this->createUser("Jacques");
        $userJean = $this->createUser("Jean");
        $userPierre = $this->createUser("Pierre");
        
        $manager = self::$container->get("doctrine.orm.entity_manager");

        $manager->persist($userJacques);
        $manager->persist($userJean);
        $manager->persist($userPierre);

        $friendship = new Friendship();
        $friendship->setUser($userJacques)
                   ->setfriend($userJean);
        $manager->persist($friendship);
        
        $friendship2 = new Friendship();
        $friendship2->setUser($userJacques)
                    ->setfriend($userPierre);
        $manager->persist($friendship2);

        $manager->flush();

        $friendsshipsFound = self::$container->get(FriendshipRepository::class)->findAll();

        $this->assertEquals(2, count($friendsshipsFound));
    }

    public function testIfNotUniqueFriendshipIsInvalid() {
        
        $userJacques = $this->createUser("Jacques");
        $userJean = $this->createUser("Jean");

        $manager = self::$container->get("doctrine.orm.entity_manager");

        $manager->persist($userJacques);
        $manager->persist($userJean);

        $friendship1 = (new Friendship())->setUser($userJacques)
                                         ->setFriend($userJean);

        $friendship2 = (new Friendship())->setUser($userJacques)
                                         ->setFriend($userJean);

        $manager->persist($friendship1);
        $manager->persist($friendship2);

        $this->expectException(UniqueConstraintViolationException::class);
        $manager->flush();
    }

    /**
     * Stops the Kernel
     * @after
     * @return void
     */
    public function closeTests() {
        self::ensureKernelShutdown();
        $this->loadFixtures([AppFixtures::class]);
    }

}