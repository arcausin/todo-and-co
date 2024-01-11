<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class HomeControllerTest extends WebTestCase
{
    public function testHomepageIsUp(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_home'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testHomepageRegisterLink(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_home'));

        $link = $crawler->selectLink('S\'inscrire')->link();
        $client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertEquals($urlGenerator->generate('app_register'), parse_url($link->getUri(), PHP_URL_PATH));

        $this->assertSelectorTextContains('h1', 'Inscription');
    }

    public function testHomepageLoginLink(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_home'));

        $link = $crawler->selectLink('Se connecter')->link();
        $client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertEquals($urlGenerator->generate('app_login'), parse_url($link->getUri(), PHP_URL_PATH));

        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testHomepageLogoutLink(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_home'));

        $link = $crawler->selectLink('Se déconnecter')->link();
        $client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertEquals($urlGenerator->generate('app_logout'), parse_url($link->getUri(), PHP_URL_PATH));
        
        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_home'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testHomepageTasksLink(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_home'));

        $link = $crawler->selectLink('Tâches')->link();
        $client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals($urlGenerator->generate('app_task_index'), parse_url($link->getUri(), PHP_URL_PATH));

        $this->assertSelectorTextContains('h1', 'Liste des tâches');
    }

    public function testHomepageUsersLink(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_home'));

        $link = $crawler->selectLink('Utilisateurs')->link();
        $client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals($urlGenerator->generate('app_user_index'), parse_url($link->getUri(), PHP_URL_PATH));

        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }
}
