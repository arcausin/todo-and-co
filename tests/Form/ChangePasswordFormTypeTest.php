<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class ChangePasswordFormTypeTest extends TypeTestCase
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
            'plainPassword' => [
                'first' => 'MyPassword',
                'second' => 'MyPassword',
            ],
        ];

        $userToCompare = new User();
        $form = $this->factory->create(ChangePasswordFormType::class, $userToCompare);
        $form->submit($formData);

        $user = new User();
        $user->setPassword('MyPassword');

        $this->assertTrue($form->isSynchronized());
        //$this->assertEquals($user, $userToCompare);
        //$this->assertSame('MyPassword', $userToCompare->getPassword());

        //$this->assertSame('MyPassword', $userToCompare->getPassword());
    }
}
