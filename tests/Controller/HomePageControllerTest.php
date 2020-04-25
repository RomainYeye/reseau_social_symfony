<?php

namespace App\Tests\Controller;

use App\DataFixtures\CompleteFixtures;
use App\Repository\UserRepository;
use App\Tests\Controller\Traits\AuthenticateSimulatorTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class HomePageControllerTest extends WebTestCase {

    use FixturesTrait;
    use AuthenticateSimulatorTrait;

    private $client;

    /**
     * Prepares the tests
     * @before
     * @return void
     */
    protected function setUp(){
        $this->ensureKernelShutdown();
        $this->client = static::createClient();;
    }

    public function testRedirecttoLoginWhenNotAuthenticated(){
        $this->client->request('GET', '/homepage');

        $this->assertResponseRedirects('/login');
    }

    public function testRedirectToLoginWhenAccessHomePageWithNoAuthentication(){
        $this->client->request('GET', '/');

        $this->assertResponseRedirects('/login');
    }

    public function testAccessToHomePageRouteWithAuth(){
        $this->loadFixtures([CompleteFixtures::class]);
        $user = self::$container->get(UserRepository::class)->find(1);
        //Appel au Trait
        $cookie = $this->createCookieForUser($user);
        $this->client->getCookieJar()->set($cookie);
        $this->client->request('GET', '/homepage');
        $this->assertResponseIsSuccessful();
    }
    
}