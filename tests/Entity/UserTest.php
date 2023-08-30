<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    protected static $faker;
    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        self::$faker = Factory::create('fr_FR');
    }

    public function getEntity(string $email, string $firstName, string $lastName, string $pseudo, mixed $password): User
    {
        return (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setPseudo($pseudo)
            ->setPassword($password);
    }

    public function getErrors(User $user, int $errorsNumber, string $needleMessage = ''): void
    {
        $errors = self::getContainer()->get('validator')->validate($user);
        $messages = [];
        $this->assertGreaterThanOrEqual($errorsNumber, count($errors));
        if (is_null($needleMessage)) {
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            $this->assertContains($needleMessage, $messages);
        }
    }

    public function testValidEntity()
    {
        $user = $this->getEntity(self::$faker->email(), self::$faker->firstName(), self::$faker->lastName(), self::$faker->userName(), self::$faker->password(8));
        $this->getErrors($user, 0);
    }

    public function testBlankEmail()
    {
        $user = $this->getEntity('', self::$faker->firstName(), self::$faker->lastName(), self::$faker->userName(), self::$faker->password(8));
        $this->getErrors($user, 1, 'The email cannot be blank');
    }

    public function testInvalidEmail(): void
    {
        $email = self::$faker->text(10);
        $user = $this->getEntity($email, self::$faker->firstName(), self::$faker->lastName(), self::$faker->userName(), self::$faker->password(8));
        $this->getErrors($user, 1, "The email address \"$email\" is invalid");
    }

    public function testBlankFirstName()
    {
        $user = $this->getEntity(self::$faker->email(), '', self::$faker->lastName(), self::$faker->userName(), self::$faker->password(8));
        $this->getErrors($user, 1, 'The firstName cannot be blank');
    }

    public function testInvalidFirstName()
    {
        $firstName = '123456';
        $user = $this->getEntity(self::$faker->email(), $firstName, self::$faker->lastName(), self::$faker->userName(), self::$faker->password(8));
        $this->getErrors($user, 1, "The firstName must be letters only");
    }

    public function testBlankLastName()
    {
        $user = $this->getEntity(self::$faker->email(), self::$faker->firstName(), '', self::$faker->userName(), self::$faker->password(8));
        $this->getErrors($user, 1, 'The lastName cannot be blank');
    }

    public function testInvalidLastName()
    {
        $lastName = '123456';
        $user = $this->getEntity(self::$faker->email(),  self::$faker->firstName(), $lastName, self::$faker->userName(), self::$faker->password(8));
        $this->getErrors($user, 1, "The lastName must be letters only");
    }

    public function testBlankPassword()
    {
        $user = $this->getEntity(self::$faker->email(), self::$faker->firstName(), self::$faker->lastName(), self::$faker->userName(),'');
        $this->getErrors($user, 1, 'The password cannot be blank');
    }

    public function testLessPasswordScore()
    {
        $lessScorePasswords = [123456, 'sssssss', 'abc'];
        $password = self::$faker->randomElement($lessScorePasswords);
        $user = $this->getEntity(self::$faker->email(), self::$faker->firstName(), self::$faker->lastName(), self::$faker->userName(),$password);
        $this->getErrors($user, 1, 'The password is too weak');
    }

    public function testBlankPseudo()
    {
        $user = $this->getEntity(self::$faker->email(), self::$faker->firstName(), self::$faker->lastName(), '', self::$faker->password(8));
        $this->getErrors($user, 1, 'The pseudo cannot be blank');
    }

    public function testMinPseudo()
    {
        $pseudo = 'a';
        $user = $this->getEntity(self::$faker->email(), self::$faker->firstName(), self::$faker->lastName(), $pseudo, self::$faker->password(10));
        $this->getErrors($user, 1, 'The pseudo must be at least 2 characters');
    }

    public function testInvalidPseudo()
    {
        $invalidPseudos = [123456, '@àé^!'];
        $pseudo = self::$faker->randomElement($invalidPseudos);
        $user = $this->getEntity(self::$faker->email(), self::$faker->firstName(), self::$faker->lastName(), $pseudo, self::$faker->password(10));
        $this->getErrors($user, 1, 'The pseudo is invalid');
    }
}
