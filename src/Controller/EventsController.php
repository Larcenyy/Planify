<?php

namespace App\Controller;

use App\Entity\Events;
use App\Form\EventCreateType;
use App\Repository\EventsRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventsController extends AbstractController
{
    #[Route('/my-events/', name: 'app_user_my_events')]
    public function userEvent(EventsRepository $eventsRepository): Response
    {
        $user = $this->getUser();

        $myEvents = $eventsRepository->findBy(['user' => $user]);

        $subscribedEvents = $eventsRepository->createQueryBuilder('e')
            ->where('e.startAt > :now')
            ->andWhere(':user MEMBER OF e.suscribers')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();

        $completedEvents = $eventsRepository->createQueryBuilder('e')
            ->where('e.startAt < :now')
            ->andWhere('e.user = :user OR :user MEMBER OF e.suscribers')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('events/index.html.twig', [
            'myCreatedEvents' => $myEvents,
            'subscribedEvents' => $subscribedEvents,
            'completedEvents' => $completedEvents,

            'currentDateTime' => new \DateTimeImmutable(),
        ]);
    }

    #[Route('/events-unsuscribe/{id}', name: 'app_event_unsuscribe')]
    public function userUnsuscribeEvent($id, EventsRepository $eventsRepository, EntityManagerInterface $entityManager): Response
    {
        $event = $eventsRepository->find($id);
        $user = $this->getUser();

        if (!$event) {
            $this->addFlash('danger', "L'événement choisi n'existe pas");
            return $this->redirectToRoute('app_home');
        }

        if(!$user) {
            $this->addFlash('danger', "Vous devez être connecté pour pouvoir vous retirer d'un événement !");
            return $this->redirectToRoute('app_login');
        }

        if ($event->getUser() === $user) {
            $this->addFlash('danger', "Vous ne pouvez pas vous désinscrire de vos propres événements !");
        } else {
            if ($event->getSuscribers()->contains($user)) {
                $event->removeSuscriber($user);
                $entityManager->persist($event);
                $entityManager->flush();
                $this->addFlash('success', "Vous vous êtes désinscrit à l'événement !");
            } else{
                $this->addFlash('danger', "Vous n'êtes pas inscrit à cet événement !");
            }
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/events-suscribe/{id}', name: 'app_event_suscribe')]
    public function userSuscribeEvent($id, EventsRepository $eventsRepository, EntityManagerInterface $entityManager): Response
    {
        $event = $eventsRepository->find($id);
        $user = $this->getUser();

        if (!$event) {
            $this->addFlash('danger', "L'événement choisi n'existe pas");
            return $this->redirectToRoute('app_home');
        }

        if(!$user) {
            $this->addFlash('danger', "Vous devez être connecté pour pouvoir vous retirer d'un événement !");
            return $this->redirectToRoute('app_login');
        }

        if ($event->getUser() === $user) {
            $this->addFlash('danger', "Vous ne pouvez pas vous inscrire à vos propres événements !");
        }
        else{
            $currentDateTime = new DateTimeImmutable();
            $eventStartAt = $event->getStartAt();

            if ($eventStartAt < $currentDateTime){
                $this->addFlash('danger', "L'événement choisi à déjà démarrer !");
            } else {
                if (!$event->getSuscribers()->contains($user)) {
                    $event->addSuscriber($user);
                    $entityManager->persist($event);
                    $entityManager->flush();
                    $this->addFlash('success', "Vous vous êtes inscrit à l'événement choisi !");
                } else {
                    $this->addFlash('danger', "Vous êtes déjà inscrit à l'événement choisi !");
                }
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_home');
    }

    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/event-create', name: 'app_event_create')]
    public function createEvent(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventCreateType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $event = new Events();
            $event
                ->setTitle($data['title'])
                ->setContent($data['content'])
                ->setLocation($data['location'])
                ->setStartAt(new DateTimeImmutable($data['startAt']->format('Y-m-d H:i:s')))
                ->setEndAt(new DateTimeImmutable($data['endAt']->format('Y-m-d H:i:s')))
                ->setUser($this->getUser());

            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', "L'événement a été bien créer !");
            return $this->redirectToRoute('app_user_my_events');
        }

        return $this->render('events/create.html.twig',[
            "form" => $form->createView()
        ]);
    }
}
