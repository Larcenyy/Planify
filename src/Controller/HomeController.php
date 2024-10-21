<?php

namespace App\Controller;

use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    /**
     * @param EventsRepository $eventsRepository
     * @return Response
     */
    #[Route(['/', '/home'], name: 'app_home')]
    public function index(EventsRepository $eventsRepository): Response
    {
        $nowDate = new \DateTime();

        // get alls events and order by date
        $upcomingEvents = $eventsRepository->findEventByDate($nowDate);

        return $this->render('home/index.html.twig', [
            'events' => $upcomingEvents,
        ]);
    }
}
