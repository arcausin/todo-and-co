<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class UserControllerTest extends WebTestCase
{
    private int $random_number;

    protected function setUp(): void
    {
        $this->random_number = random_int(0, 1000);
    }

    // app_user_index
    public function testUserIndexRedirectToHomepage(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_user_index'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_home'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testUserIndexAdminIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_user_index'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    // app_user_edit
    public function testUserEditRedirectToHomepage(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_user_edit', ['id' => 1]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_home'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testUserEditIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $user = $userRepository->findSecondUser();

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_GET, $urlGenerator->generate('app_user_edit', ['id' => $user->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Modifier un utilisateur');
    }

    public function testUserEditFormIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $user = $userRepository->findSecondUser();

        if ($user->getRoles() === ['ROLE_USER']) {
            $roles = 'ROLE_ADMIN';
        } else {
            $roles = 'ROLE_USER';
        }

        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_user_edit', ['id' => $user->getId()]));

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'Nom d\'utilisateur '.$this->random_number,
            'user[roles]' => $roles
        ]);

        $client->submit($form);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_user_index'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');

        $this->assertSelectorTextContains('div.alert.alert-success', 'L\'utilisateur Nom d\'utilisateur '.$this->random_number.' a été modifié avec succès.');
    }

    // app_user_delete
    public function testUserDeleteRedirectToHomepage(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findSecondUser();

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_POST, $urlGenerator->generate('app_user_delete', ['id' => $user->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_home'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $this->assertSelectorTextContains('h1', 'Accueil');
    }

    public function testUserDeleteIsUp(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findFirstAdmin();
        $client->loginUser($user);

        $user = $userRepository->findSecondUser();

        $urlGenerator = $client->getContainer()->get('router.default');
        $client->request(Request::METHOD_POST, $urlGenerator->generate('app_user_delete', ['id' => $user->getId()]));

        $this->assertResponseRedirects(parse_url($urlGenerator->generate('app_user_index'), PHP_URL_HOST));

        $client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');

        $this->assertSelectorTextContains('div.alert.alert-success', 'L\'utilisateur '.$user->getUsername().' a été supprimé avec succès.');
    }
}
