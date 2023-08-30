<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageTest extends KernelTestCase
{
    protected static $faker;

    public function setUp(): void
    {
        self::bootKernel();
        self::$faker = Factory::create(('fr_FR'));
    }

    public function testIsPropertyContentExist(): void
    {
        $message = (new Message());
        $this->assertTrue(property_exists($message, 'content'));
    }

    public function testBlankContent()
    {
        $message = (new Message())
        ->setContent('');
        $errors = self::getContainer()->get('validator')->validate($message);
        $errorsMessage = [];
        foreach ($errors as $error) {
            $errorsMessage[] = $error->getMessage();
        }
        $this->assertNotEmpty($errors);
        $this->assertContains('Content cannot be blank', $errorsMessage);
    }

    public function testValidContent()
    {
        $message = (new Message())
            ->setContent(self::$faker->realText());
        $errors = self::getContainer()->get('validator')->validate($message);
        $this->assertEmpty($errors);
    }
}
