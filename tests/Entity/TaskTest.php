<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetTitle(): void
    {
        $task = new Task();
        $task->setTitle('My title');

        $this->assertSame('My title', $task->getTitle());
    }

    public function testGetContent(): void
    {
        $task = new Task();
        $task->setContent('My content');

        $this->assertSame('My content', $task->getContent());
    }

    public function testGetIsDone(): void
    {
        $task = new Task();
        $task->setIsDone(true);

        $this->assertTrue($task->isIsDone());
    }

    public function testGetCreatedAt(): void
    {
        $task = new Task();
        $task->setCreatedAt(new \DateTimeImmutable('2021-01-01 00:00:00'));

        $this->assertEquals(new \DateTimeImmutable('2021-01-01 00:00:00'), $task->getCreatedAt());
    }

    public function testGetUser(): void
    {
        $task = new Task();
        $user = new User();
        $task->setUser($user);

        $this->assertSame($user, $task->getUser());
    }
}
