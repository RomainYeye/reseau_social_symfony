<?php

namespace App\Tests\Repository;

use App\Entity\Publication;
use App\DataFixtures\AppFixtures;
use App\DataFixtures\CompleteFixtures;
use App\DataFixtures\PublicationFixtures;
use App\Repository\PublicationRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Repository\Traits\createUserTrait;
use App\Tests\Repository\Traits\createPublicationTrait;

class PublicationRepositoryTest extends WebTestCase {
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

    public function testFindAllReturnsArePublications() {
        $this->loadFixtures([PublicationFixtures::class]);
        $publications = self::$container->get(PublicationRepository::class)->findAll();
        $this->assertCount(10, $publications);
    }

    public function testFindById() {
        $this->loadFixtures([PublicationFixtures::class]);
        $publication = self::$container->get(PublicationRepository::class)->find(1);
        $this->assertInstanceOf(Publication::class, $publication);
        $this->assertEquals(1, $publication->getId());
    }

    public function testInsertion() {
        $user = $this->createUser("Jacques");

        $manager = self::$container->get("doctrine.orm.entity_manager");
        $manager->persist($user);
        
        $publication = $this->createPublicationOfUser($user);

        $manager->persist($publication);
        $manager->flush();

        $publicationTrouvee = self::$container->get(PublicationRepository::class)->find(1);

        $this->assertNotNull($publicationTrouvee);
        $this->assertEquals(1, $publicationTrouvee->getId());
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