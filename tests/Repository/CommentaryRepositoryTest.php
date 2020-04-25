<?php
namespace App\Tests\Repository;
ini_set('memory_limit', '800M');

use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Commentary;
use App\DataFixtures\AppFixtures;
use App\DataFixtures\CommentaryFixtures;
use App\DataFixtures\CompleteFixtures;
use App\Repository\CommentaryRepository;
use App\Tests\Repository\Traits\createPublicationTrait;
use App\Tests\Repository\Traits\createUserTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentaryRepositoryTest extends WebTestCase {
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

    public function testFindAllReturnsAreCommentaries() {
        $this->loadFixtures([CommentaryFixtures::class]);
        $commentaries = self::$container->get(CommentaryRepository::class)->findAll();
        $this->assertCount(10, $commentaries);
    }

    public function testFindById() {
        $this->loadFixtures([CommentaryFixtures::class]);
        $commentary = self::$container->get(CommentaryRepository::class)->find(1);
        $this->assertInstanceOf(Commentary::class, $commentary);
    }

    public function testInsertion() {
        $user = $this->createUser("Jean");

        $manager = self::$container->get("doctrine.orm.entity_manager");
        $manager->persist($user);
        
        $publication = $this->createPublicationOfUser($user);
        $manager->persist($publication);
        
        $commentary = $this->createCommentaryOfUserOnPublication($user, $publication);
        $manager->persist($commentary);

        $manager->flush();

        $commentaryTrouve = self::$container->get(CommentaryRepository::class)->find(1);

        $this->assertNotNull($commentaryTrouve);
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

    private function createCommentaryOfUserOnPublication(User $user, Publication $publication) {
        $commentary = (new Commentary())->setComment("J'ai adoré, surtout quand c'était fini.")
                                        ->setPublication($publication)
                                        ->setUser($user);
        return $commentary;
    }
}