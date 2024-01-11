<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class UserTypeTest extends TypeTestCase
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
            'roles' => ['ROLE_USER'],
        ];

        $userToCompare = new User();
        $form = $this->factory->create(UserType::class, $userToCompare);
        $form->submit($formData);

        $user = new User();
        $user->setUsername('My username');
        $user->setRoles(['ROLE_USER']);

        $this->assertTrue($form->isSynchronized());
        //$this->assertEquals($user, $userToCompare);
        $this->assertSame('My username', $userToCompare->getUsername());
        $this->assertSame(['ROLE_USER'], $userToCompare->getRoles());
    }
}
