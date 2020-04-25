<?php

namespace App\Tests\Repository;

use App\DataFixtures\AppFixtures;
use App\Tests\Repository\Traits\createUserTrait;
use App\Tests\Repository\Traits\createPublicationTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\CompleteFixtures;
use App\Repository\NotificationRepository;
use App\Entity\Notification;
use App\Controller\Traits\createNewNotificationTrait;

class NotificationRepositoryTest extends WebTestCase {
    use FixturesTrait;
    use createUserTrait;
    use createPublicationTrait;
    use createNewNotificationTrait;

    /**
     * Prepares the tests
     * @before
     * @return void
     */
    public function setUp() {
        self::bootKernel();
    }

    public function testFindById() {
        $this->loadFixtures([CompleteFixtures::class]);
        $publication = self::$container->get(NotificationRepository::class)->find(1);
        $this->assertInstanceOf(Notification::class, $publication);
        $this->assertEquals(1, $publication->getId());
    }

    public function testInsertion() {

        $manager = self::$container->get("doctrine.orm.entity_manager");

        $user = $this->createUser("Jacques");
        $manager->persist($user);
        
        $publication = $this->createPublicationOfUser($user);
        $manager->persist($publication);

        $notification = $this->createNewNotification($user, $publication, "publication");
        $manager->persist($notification);

        $manager->flush();

        $notificationTrouvee = self::$container->get(NotificationRepository::class)->find(1);

        $this->assertNotNull($notificationTrouvee);
        $this->assertEquals(1, $notificationTrouvee->getId());
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