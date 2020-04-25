<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\CompleteFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase {

    use FixturesTrait;

    private $client;

    /**
     * Prepares thes tests
     * 
     * @return void
     */
    protected function setUp(){
        $this->ensureKernelShutdown();
        $this->client = static::createClient();
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->loadFixtures([AppFixtures::class]);
    }

    public function testLoginFormIsDisplayed(){
        $this->client->request("GET", "/login");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("h1", "Please sign in");
    }

    public function testLoginSuccessRedirectToHomePageRoute(){
        $this->loadFixtures([CompleteFixtures::class]);
        $form = $this->createLoginForm("name-1", "1234");
        $this->client->submit($form);
        $this->assertResponseRedirects('/homepage');

    }
     
    public function testLoginWithBadUsername(){
        $this->loadFixtures([CompleteFixtures::class]);
        $form = $this->createLoginForm("myemailsdsfs", "password1");
        $this->client->submit($form);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists(".alert.alert-danger");
        $this->assertSelectorTextContains("div", "Username could not be found.");

    }

    private function createLoginForm($username, $password){
        $crawler = $this->client->request("GET", "/login");
        $button = $crawler->selectButton('Sign in');
        $form = $button->form([
            "username" => $username,
            "password" => $password
        ]);
        return $form;
    }
}