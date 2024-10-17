<?php

namespace App\Controller;

use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(['/', '/home'], name: 'app_home')]
    public function index(EventsRepository $eventsRepository): Response
    {
        $nowDate = new \DateTime();

        // get alls events and order by date
        $upcomingEvents = $eventsRepository->createQueryBuilder('e')
            ->where('e.startAt > :now')
            ->setParameter('now', $nowDate)
            ->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'events' => $upcomingEvents,
        ]);
    }
}
