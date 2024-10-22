<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        // Remove any existing users from the test database
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();

        // Create a User fixture
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = (new User())->setEmail('email@example.com')
            ->setFirstname('John')
            ->setLastname('Doe');


        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $em->persist($user);
        $em->flush();
    }

    public function testLogin(): void
    {
        // Step 1: Access the login page
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        // Step 2: Attempt to login with valid credentials
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com', // Valid email
            '_password' => 'password', // Valid password
        ]);

        // Step 3: Assert that the response redirects to the home page
        self::assertResponseRedirects('/');
        $this->client->followRedirect();

        // Step 4: Assert that there are no error messages
        self::assertSelectorNotExists('.alert-danger');

        // Step 5: Log out to test invalid login scenarios
        // Simulate logging out if your application supports this
        $this->client->request('GET', '/logout'); // Adjust this line according to your logout logic

        // Step 6: Attempt to login with an invalid email
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Se connecter', [
            '_username' => 'bad-email@example.com', // Invalid email
            '_password' => 'password', // Valid password
        ]);

        // Step 7: Assert that the response redirects back to the login page
        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        // Step 8: Ensure an error message is displayed for invalid credentials
        self::assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');

        // Step 9: Attempt to login with an invalid password
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com', // Valid email
            '_password' => 'wrong-password', // Invalid password
        ]);

        // Step 10: Assert that the response redirects back to the login page
        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        // Step 11: Ensure an error message is displayed for invalid credentials
        self::assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');
    }

}
