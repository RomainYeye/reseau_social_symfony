<?php

namespace App\Tests\Repository\Traits;
use App\Entity\User;

trait createUserTrait {
    public function createUser($username) {
        $user = (new User())->setUsername($username)
                            ->setPassword("test")
                            ->setEmail("Jean@test.fr");
        return $user;
    }
}

?>