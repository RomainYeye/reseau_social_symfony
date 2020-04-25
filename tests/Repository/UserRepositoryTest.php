<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\DataFixtures\AppFixtures;
use App\DataFixtures\CompleteFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase {
    use FixturesTrait;

    /**
     * Prepares the tests
     * @before
     * @return void
     */
    public function setUp() {
        self::bootKernel();
    }

    public function testFindAllReturnsAreUsers() {
        $this->loadFixtures([CompleteFixtures::class]);
        $users = self::$container->get(UserRepository::class)->findAll();
        $this->assertCount(10, $users);
    }

    public function testFindById() {
        $this->loadFixtures([CompleteFixtures::class]);
        $user = self::$container->get(UserRepository::class)->find(1);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->getId());
    }

    public function testInsertion() {
        $user = (new User())->setUsername("Jean")
                            ->setPassword("test")
                            ->setEmail("Jean@test.fr");

        $manager = self::$container->get("doctrine.orm.entity_manager");
        
        $manager->persist($user);
        $manager->flush();

        $userTrouve = self::$container->get(UserRepository::class)->find(1);

        $this->assertNotNull($userTrouve);
        $this->assertEquals(1, $userTrouve->getId());
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