<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Like;
use App\DataFixtures\AppFixtures;
use App\Repository\LikeRepository;
use App\Tests\Repository\Traits\createUserTrait;
use App\Tests\Repository\Traits\createPublicationTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class LikeRepositoryTest extends WebTestCase {
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

    public function testIfUniqueLikesAreValid() {
        $userJacques = $this->createUser("Jacques");

        $manager = self::$container->get("doctrine.orm.entity_manager");
        $manager->persist($userJacques);

        $userJean = $this->createUser("Jean");
        $manager->persist($userJean);
        
        $publication = $this->createPublicationOfUser($userJacques);
        $manager->persist($publication);
        
        $likeJacques = $this->createLikeOfUserOnPublication($userJean, $publication);
        $manager->persist($likeJacques);
        $likeJean = $this->createLikeOfUserOnPublication($userJacques, $publication);
        $manager->persist($likeJean);

        $manager->flush();

        $likesTrouves = self::$container->get(LikeRepository::class)->findAll();

        $this->assertEquals(2, count($likesTrouves));
    }

    public function testIfNotUniqueLikeIsInvalid() {
        $user = $this->createUser("Jean");

        $manager = self::$container->get("doctrine.orm.entity_manager");
        $manager->persist($user);
        
        $publication = $this->createPublicationOfUser($user);
        $manager->persist($publication);
        
        $like1 = $this->createLikeOfUserOnPublication($user, $publication);
        $like2 = $this->createLikeOfUserOnPublication($user, $publication);
        $manager->persist($like1);
        $manager->persist($like2);

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

    private function createLikeOfUserOnPublication(User $user, Publication $publication) {
        $like = (new Like())->setUser($user)
                            ->setPublication($publication);
        return $like;
    }
}