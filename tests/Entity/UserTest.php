<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetUsername(): void
    {
        $user = new User();
        $user->setUsername('Username');

        $this->assertSame('Username', $user->getUsername());
    }

    public function testGetEmail(): void
    {
        $user = new User();
        $user->setEmail('email@test.test');

        $this->assertSame('email@test.test', $user->getEmail());
    }

    public function testGetPassword(): void
    {
        $user = new User();
        $user->setPassword('password');

        $this->assertSame('password', $user->getPassword());
    }

    public function testGetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
      
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function testGetIsVerified(): void
    {
        $user = new User();
        $user->setIsVerified(true);

        $this->assertTrue($user->isVerified());
    }

    public function testGetCreatedAt(): void
    {
        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable('2021-01-01 00:00:00'));

        $this->assertEquals(new \DateTimeImmutable('2021-01-01 00:00:00'), $user->getCreatedAt());
    }

    public function testGetTasks(): void
    {
        $user = new User();
        $task = new Task();
        $user->addTask($task);

        $this->assertSame($task, $user->getTasks()->first());
    }

    public function testAddTask(): void
    {
        $user = new User();
        $task = new Task();
        $user->addTask($task);

        $this->assertSame($user, $task->getUser());
    }

    public function testRemoveTask(): void
    {
        $user = new User();
        $task = new Task();
        $user->addTask($task);
        $user->removeTask($task);

        $this->assertEmpty($user->getTasks());
    }
}
