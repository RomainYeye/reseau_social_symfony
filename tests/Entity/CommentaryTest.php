<?php

namespace App\tests\Entity;

use App\Entity\Commentary;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentaryTest extends KernelTestCase {

    
    private function expectXErrorsForCommentary(int $nbrErrors, Commentary $commentary) {
        $errors = self::$container->get("validator")->validate($commentary);
        $this->assertCount($nbrErrors, $errors);
    }

    private function createCommentary() {
        return (new Commentary())->setComment("Ce réseau social est génial.");
    }

    public function testIfCommentIsValid() {
        self::bootKernel();
        $commentary = $this->createCommentary();

        $this->expectXErrorsForCommentary(0, $commentary);
    }

    public function testIfBlankCommentIsInvalid() {
        self::bootKernel();
        $commentary = $this->createCommentary()->setComment("");

        $this->expectXErrorsForCommentary(1, $commentary);
    }

    public function testIfCommentWithMoreThan500CharactersIsInvalid() {
        self::bootKernel();
        $comment = str_repeat("a", 501);
        $commentary = $this->createCommentary()->setComment($comment);

        $this->expectXErrorsForCommentary(1, $commentary);
    }

}