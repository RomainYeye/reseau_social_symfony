<?php

namespace App\tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase {
    
    private function createUser() {
        return (new User())->setUsername("jnz")
                           ->setPlainPassword("Mjf454151@")
                           ->setEmail("jean@charles.fr");
    }

    private function expectXErrorsForUser(int $nbrErrors, User $user) {
        $errors = self::$container->get("validator")->validate($user);
        $this->assertCount($nbrErrors, $errors);
    }

        //$username
        public function testIfUsernameOnlyIsValid(){
            self::bootKernel();
            $user = $this->createUser()->setUsername("Jean");
            $this->expectXErrorsForUser(0, $user);
        }
        public function testIfUsernameOnlyIsUnderThanThreeCharacters(){
            self::bootKernel();
            $user = $this->createUser()->setUsername("Da");
            $this->expectXErrorsForUser(1, $user);
        }
        public function testIfUsernameOnlyNumbers(){
            self::bootKernel();
            $user = $this->createUser()->setUsername("4545789");
            $this->expectXErrorsForUser(0, $user);
        }
        public function testIfUsernameTooLong(){
            self::bootKernel();
            $user = $this->createUser()->setUsername("too long 20 characters");
            $this->expectXErrorsForUser(1, $user);
        }
        public function testIfUsernameBlank(){
            self::bootKernel();
            $user = $this->createUser()->setUsername("");
            $this->expectXErrorsForUser(1, $user);
        }
    
        //$plainPassword
        public function testIfPlainPasswordOnlyIsValid(){
            self::bootKernel();
            $user = $this->createUser()->setPlainPassword("tT8gggggggg!");
            $this->expectXErrorsForUser(0, $user);
        }
        public function testIfPlainPasswordOnlyIsUnderThanEightCharacters(){
            self::bootKernel();
            $user = $this->createUser()->setPlainPassword("Da787ju");
            $this->expectXErrorsForUser(1, $user);
        }
        public function testIfPlainPasswordOnlyNumbers(){
            self::bootKernel();
            $user = $this->createUser()->setPlainPassword("4545789758");
            $this->expectXErrorsForUser(1, $user);
        }
        public function testIfPlainPasswordBlank(){
            self::bootKernel();
            $user = $this->createUser()->setPlainPassword("");
            $this->expectXErrorsForUser(1, $user);
        }

        //$email
        public function testIfEmailOnlyIsValid(){
            self::bootKernel();
            $user = $this->createUser()->setEmail("Jean-59@gmail.com");
            $this->expectXErrorsForUser(0, $user);
        }
        public function testIfEmailOnlyUnderThansixCharacters(){
            self::bootKernel();
            $user = $this->createUser()->setEmail("d@d.f");
            $this->expectXErrorsForUser(1, $user);
        }
        public function testIfEmailOnlyNumbers(){
            self::bootKernel();
            $user = $this->createUser()->setEmail("4545789@777.7777");
            $this->expectXErrorsForUser(1, $user);
        }
        public function testIfEmailBlank(){
            self::bootKernel();
            $user = $this->createUser()->setEmail("");
            $this->expectXErrorsForUser(1, $user);
        }

}