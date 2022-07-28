<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(SessionRepository $sr): Response
    {

        $pastSessions = $sr->findPastSession();
        $progressSessions = $sr->findProgressSession();
        $futurSessions = $sr->findFuturSession();
        return $this->render('home/index.html.twig', [
            'pastSessions' => $pastSessions,
            'progressSessions' => $progressSessions,
            'futurSessions' => $futurSessions,
        ]);
    }
    
}
