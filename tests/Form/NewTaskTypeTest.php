<?php

namespace App\Tests\Form;

use App\Entity\Task;
use App\Form\NewTaskType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class NewTaskTypeTest extends TypeTestCase
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
            'title' => 'My title',
            'content' => 'My content',
        ];

        $taskToCompare = new Task();
        $form = $this->factory->create(NewTaskType::class, $taskToCompare);
        $form->submit($formData);

        $task = new Task();
        $task->setTitle('My title');
        $task->setContent('My content');

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($task, $taskToCompare);
        $this->assertSame('My title', $taskToCompare->getTitle());
        $this->assertSame('My content', $taskToCompare->getContent());
    }
}
