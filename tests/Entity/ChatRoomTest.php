<?php

namespace App\Tests\Entity;

use App\Entity\ChatRoom;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChatRoomTest extends KernelTestCase
{
    protected static $faker;
    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        self::$faker = Factory::create('fr_FR');
    }

    public function getEntity(string $name): ChatRoom
    {
        return (new ChatRoom())
            ->setName($name);
    }

    public function getErrors(ChatRoom $chatRoom, int $error, string $needleMessage = null): void
    {
        $errors = static::getContainer()->get('validator')->validate($chatRoom);
        $this->assertCount($error, $errors);
        if (isset($needleMessage)) {
            $message = [];
            foreach ($errors as $violation) {
                $message[] = $violation->getMessage();
            }
            $this->assertContains($needleMessage, $message);
        }
    }

    public function testValidName(): void
    {
        $chatRoom = $this->getEntity(self::$faker->name());
        $this->getErrors($chatRoom, 0);
    }

    public function testAssertNameBlank()
    {
        $chatRoom = $this->getEntity("");
        $this->getErrors($chatRoom, 2,'Name cannot be blank');
    }

    public function testInvalidMaxName()
    {
        $chatRoom = $this->getEntity(self::$faker->realTextBetween(50, 100));
        $this->getErrors($chatRoom, 1, 'Name must be at most 20 characters');
    }

    public function testInvalidMinName()
    {
        $chatRoom = $this->getEntity("ab");
        $this->getErrors($chatRoom, 1, 'Name must be at least 3 characters');
    }
}
