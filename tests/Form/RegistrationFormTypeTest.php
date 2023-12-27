<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationFormTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'username' => 'My username',
            'email' => 'email@test.test',
            'plainPassword' => [
                'first' => 'MyPassword',
                'second' => 'MyPassword',
            ],
        ];

        $userToCompare = new User();
        $form = $this->factory->create(RegistrationFormType::class, $userToCompare);
        $form->submit($formData);

        $user = new User();
        $user->setUsername('My username');
        $user->setEmail('email@test.test');
        $user->setPassword('MyPassword');

        $this->assertTrue($form->isSynchronized());
        //$this->assertEquals($user, $userToCompare);
        $this->assertSame('My username', $userToCompare->getUsername());
        $this->assertSame('email@test.test', $userToCompare->getEmail());
        //$this->assertSame('MyPassword', $userToCompare->getPassword());

        $this->assertTrue($userToCompare instanceof UserInterface);
        $this->assertTrue($userToCompare instanceof PasswordAuthenticatedUserInterface);

        $this->assertSame('email@test.test', $userToCompare->getUserIdentifier());
        //$this->assertSame('MyPassword', $userToCompare->getPassword());
        $this->assertNull($userToCompare->eraseCredentials());
    }
}
