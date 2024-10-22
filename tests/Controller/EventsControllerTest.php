<?php

namespace App\Tests\Controller;

use App\Entity\Events;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class EventsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/events/';
    private ?User $user = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Events::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
        $this->manager->flush();

        $email = 'user' . uniqid() . '@example.com';

        $this->user = new User();
        $this->user->setFirstname('John');
        $this->user->setLastname('Doe');
        $this->user->setEmail($email);
        $this->user->setPassword('password');
        $this->user->setRoles(['ROLE_USER']);

        $this->manager->persist($this->user);
        $this->manager->flush();

        $this->client->loginUser($this->user);
    }


    public function testNew(): void
    {
        $client = $this->client;

        $crawler = $client->request('GET', '/events/create');

        $form = $crawler->filter('form')->form([
            'events[title]' => 'Titre de l\'événement',
            'events[content]' => 'Contenu de l\'événement',
            'events[startAt]' => (new \DateTime())->format('Y-m-d H:i:s'),
            'events[endAt]' => (new \DateTime('+1 hour'))->format('Y-m-d H:i:s'),
            'events[location]' => 'Emplacement de test',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/events/my-events/', Response::HTTP_SEE_OTHER);
        $client->followRedirect();

        $this->assertSelectorTextContains('.alert.alert-success', "Félicitation! L'événement a été bien créer !");
    }

    public function testEdit(): void
    {
        // Step 1: Create an event to edit
        $event = new Events();
        $event->setTitle('Original title');
        $event->setContent('Original content');
        $event->setStartAt(new \DateTimeImmutable());
        $event->setEndAt(new \DateTimeImmutable('+1 hour'));
        $event->setLocation('Original location');
        $event->setUser($this->user); // Associate the user

        $this->manager->persist($event);
        $this->manager->flush();

        // Step 2: Access the edit page
        $crawler = $this->client->request('GET', sprintf('%s%d/edit', $this->path, $event->getId()));

        // Step 3: Submit the edit form
        $form = $crawler->filter('form')->form([
            'events[title]' => 'New title',
            'events[content]' => 'New content',
            'events[startAt]' => (new \DateTime())->format('Y-m-d H:i:s'),
            'events[endAt]' => (new \DateTime('+2 hours'))->format('Y-m-d H:i:s'),
            'events[location]' => 'New location',
        ]);

        $this->client->submit($form);

        // Step 4: Verify the redirection
        $this->assertResponseRedirects('/events/my-events/', Response::HTTP_SEE_OTHER);
        $this->client->followRedirect();

        // Step 5: Check that the changes were applied
        $updatedEvent = $this->repository->find($event->getId());
        self::assertSame('New title', $updatedEvent->getTitle());
        self::assertSame('New content', $updatedEvent->getContent());
        self::assertSame('New location', $updatedEvent->getLocation());
    }
}
