<?php

namespace App\Tests\Repository;

use App\DataFixtures\AppFixtures;
use App\Entity\Friendship;
use App\Repository\NotifyToRepository;
use App\Tests\Repository\Traits\createUserTrait;
use App\Tests\Repository\Traits\createPublicationTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Controller\Traits\createNewNotificationTrait;
use App\Controller\Traits\createNewNotifyToTrait;

class NotifyToRepositoryTest extends WebTestCase {
    use FixturesTrait;
    use createUserTrait;
    use createPublicationTrait;
    use createNewNotificationTrait;
    use createNewNotifyToTrait;

    /**
     * Prepares the tests
     * @before
     * @return void
     */
    public function setUp() {
        self::bootKernel();
    }

    public function testIfUniqueNotifyToIsValid() {

        $manager = self::$container->get("doctrine.orm.entity_manager");

        $user1 = $this->createUser("Jean");
        $user2 = $this->createUser("Albert");
        $user3 = $this->createUser("Philippe");
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user3);

        $publication = $this->createPublicationOfUser($user1);
        $manager->persist($publication);

        $friendship1 = (new Friendship())->setUser($user1)
                                         ->setFriend($user2);
        $manager->persist($friendship1);
        $friendship2 = (new Friendship())->setUser($user1)
                                         ->setFriend($user3);
        $manager->persist($friendship2);

        $notification = $this->createNewNotification($user1, $publication, "publication");
        $manager->persist($notification);

        $notifyTo1 = $this->sendNewNotifyToCaseNewPublication($friendship1->getFriend(), $notification);
        $manager->persist($notifyTo1);

        $notifyTo2 = $this->sendNewNotifyToCaseNewPublication($friendship2->getFriend(), $notification);
        $manager->persist($notifyTo2);

        $manager->flush();

        $notifyToFound = self::$container->get(NotifyToRepository::class)->findAll();

        $this->assertEquals(2, count($notifyToFound));
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