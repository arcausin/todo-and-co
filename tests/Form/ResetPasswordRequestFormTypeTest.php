<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class ResetPasswordRequestFormTypeTest extends TypeTestCase
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
            'email' => 'email@test.test',
        ];

        $userToCompare = new User();
        $form = $this->factory->create(ResetPasswordRequestFormType::class, $userToCompare);
        $form->submit($formData);

        $user = new User();
        $user->setEmail('email@test.test');

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $userToCompare);
        $this->assertSame('email@test.test', $userToCompare->getEmail());
    }
}
