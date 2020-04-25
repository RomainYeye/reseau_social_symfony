<?php

namespace App\Tests\Repository\Traits;
use App\Entity\User;
use App\Entity\Publication;
use DateTime;

trait createPublicationTrait {
    public function createPublicationOfUser(User $user) {
        $publication = (new Publication())->setTitre("L'Odysée")
                                          ->setMessage("Le voyage a commencé un matin de printemps.")
                                          ->setUser($user)
                                          ->setDate(new DateTime());
        return $publication;
    }
}

?>