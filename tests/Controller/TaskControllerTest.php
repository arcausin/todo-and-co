<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Task;

class TaskControllerTest extends WebTestCase
{
    private int $random_number_new;
    private int $random_number_edit;

    protected function setUp(): void
    {
        $this->random_number_new = random_int(0, 1000);
        $this->random_number_edit = random_int(0, 1000);

        while ($this->random_number_new === $this->random_number_edit) {
            $this->random_number_edit = random_int(0, 1000);
        }
    }

    // app_task_index
    public function testTaskIndexRedirectToHomepage(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_index'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_home'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testTaskIndexAdminIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_index'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testTaskIndexUserIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findSecondUser();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_index'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testTaskIndexNewTaskLink(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_index'));

        $link = $crawler->selectLink('Créer une nouvelle tâche')->link();
        $client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals($urlGenerator->generate('app_task_new'), parse_url($link->getUri(), PHP_URL_PATH));

        $this->assertSelectorTextContains('h1', 'Ajouter une nouvelle tâche');
    }

    // app_task_new
    public function testTaskNewRedirectToHomepage(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_new'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_home'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testTaskNewIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_new'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Ajouter une nouvelle tâche');
    }

    public function testTaskNewFormIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_new'));

        $form = $crawler->selectButton('Ajouter')->form([
            'new_task[title]' => 'Titre'.$this->random_number_new,
            'new_task[content]' => 'Contenu'.$this->random_number_new
        ]);

        $client->submit($form);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_task_index'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Liste des tâches');

        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche Titre'.$this->random_number_new.' a été ajoutée avec succès.');
    }

    // app_task_edit
    public function testTaskEditRedirectToHomepage(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();

        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        if ($taskRepository->findOneByTitle('Titre'.$this->random_number_new)) {
            $task = $taskRepository->findOneByTitle('Titre'.$this->random_number_new);
        } else {
            $task = $user->getTasks()->first();
        }

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_edit', ['id' => $task->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_home'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testTaskEditIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        if ($taskRepository->findOneByTitle('Titre'.$this->random_number_new)) {
            $task = $taskRepository->findOneByTitle('Titre'.$this->random_number_new);
        } else {
            $task = $user->getTasks()->first();
        }

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_edit', ['id' => $task->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Modifier une tâche');
    }

    public function testTaskEditFormIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        if ($taskRepository->findOneByTitle('Titre'.$this->random_number_new)) {
            $task = $taskRepository->findOneByTitle('Titre'.$this->random_number_new);
        } else {
            $task = $user->getTasks()->first();
        }

        if ($task->isIsDone()) {
            $isDone = false;
        } else {
            $isDone = true;
        }

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_task_edit', ['id' => $task->getId()]));

        $form = $crawler->selectButton('Modifier')->form([
            'edit_task[title]' => 'Titre'.$this->random_number_edit,
            'edit_task[content]' => 'Contenu'.$this->random_number_edit,
            'edit_task[is_done]' => $isDone
        ]);

        $client->submit($form);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_task_index'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Liste des tâches');

        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche Titre'.$this->random_number_edit.' a été modifiée avec succès.');
    }

    // app_task_delete
    public function testTaskDeleteRedirectToHomepage(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();

        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        if ($taskRepository->findOneByTitle('Titre'.$this->random_number_new)) {
            $task = $taskRepository->findOneByTitle('Titre'.$this->random_number_new);
        } else {
            $task = $user->getTasks()->first();
        }

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_POST, $urlGenerator->generate('app_task_delete', ['id' => $task->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_home'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testTaskDeleteIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $taskRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        if ($taskRepository->findOneByTitle('Titre'.$this->random_number_edit)) {
            $task = $taskRepository->findOneByTitle('Titre'.$this->random_number_edit);
        }
        elseif ($taskRepository->findOneByTitle('Titre'.$this->random_number_new)) {
            $task = $taskRepository->findOneByTitle('Titre'.$this->random_number_new);
        } else {
            $task = $user->getTasks()->first();
        }

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_POST, $urlGenerator->generate('app_task_delete', ['id' => $task->getId()]));

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_task_index'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Liste des tâches');

        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche '.$task->getTitle().' a été supprimée avec succès.');
    }
}
