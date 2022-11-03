<?php

namespace App\Controller;

use DateTime;
use App\Entity\Session;
use App\Entity\Calendar;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="app_api")
     */
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }


     /**
     * @Route("/api/{id}/edit", name="api_event_edit",  methods={"PUT"})  // on le block sur une méthode PUT on ne peut donc pas l'ouvrir il y auras un 405 erreur 
     */
    // on doit pouvoir mettre a jour ou crée un évenement si il n'existe pas 
    public function majEvent(?Calendar $calendar ,ManagerRegistry $doctrine, Request $request): Response   // le point d'interogation signifie que qu'on peut potentiellement passez un Id qui n'existe pas l'inconveniant est qu'il faut récuperer l'intégralité des données
    {
        // on récupère  les données envoyer par fullCalendar
        $donnees = json_decode($request->getContent());
        if( 
            isset($donnees->title) && !empty($donnees->title) &&
            // isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->end) && !empty($donnees->end) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
            ){
                // soit les données sont complètes
                // on initialise un code qui va etre le 200 = mise a jour
                $code=200;
                //on vérifie si l'id existe
                if(!$calendar){
                    // on instancie un rdv 
                    $calendar = new Calendar();
                    // on change le code 201 qui = a created
                    $code=201;
                }
                // on hydrate l'objet avec les données
                $calendar->setTitle($donnees->title);
                $calendar->setStart(new DateTime($donnees->start));
                if($donnees->allDay){
                    $calendar->setEnd(new DateTime($donnees->start));
                }else{
                    $calendar->setEnd(new DateTime($donnees->end));
                }
                $calendar->setAllDay($donnees->allDay);
                $calendar->setDescription($donnees->description);
                $calendar->setBackgroundColor($donnees->backgroundColor);
                $calendar->setBorderColor($donnees->borderColor);
                $calendar->setTextColor($donnees->textColor);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($calendar);
                $entityManager->flush();

                // return new Response('ok', $code);
                return $this->render('api/index.html.twig', [
                    'controller_name' => 'ApiController',
                ]);

            }else{
                    //soit les données sont incomplètes
                    return new Response('données incomplètes', 404);  // on crée une reponse et l'envoie sur une page 404
            }
    }



 /**
     * @Route("/apiSession/{id}/edit", name="api_event_edit_session",  methods={"PUT"})  // on le block sur une méthode PUT on ne peut donc pas l'ouvrir il y auras un 405 erreur 
     */
    // on doit pouvoir mettre a jour ou crée un évenement si il n'existe pas 
    public function majEventSession(?Session $session ,ManagerRegistry $doctrine, Request $request): Response   // le point d'interogation signifie que qu'on peut potentiellement passez un Id qui n'existe pas l'inconveniant est qu'il faut récuperer l'intégralité des données
    {
        // on récupère  les données envoyer par fullCalendar
        $donnees = json_decode($request->getContent());
        if( 
            isset($donnees->title) && !empty($donnees->title) &&
            // isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->dateEnd) && !empty($donnees->end) &&
            isset($donnees->idFormation) && !empty($donnees->idFormation) &&
            isset($donnees->nbPlace) && !empty($donnees->nbPlace)         
            ){
                // soit les données sont complètes
                // on initialise un code qui va etre le 200 = mise a jour
                $code=200;
                //on vérifie si l'id existe
                if(!$session){
                    // on instancie un rdv 
                    $session = new Session();
                    // on change le code 201 qui = a created
                    $code=201;
                }
                // on hydrate l'objet avec les données
                $session->setTitle($donnees->title);
                $session->setNbPlace($donnees->nbPlace);
                $session->setDateStart(new DateTime($donnees->start));
                if($donnees->allDay){
                    $session->setDateEnd(new DateTime($donnees->start));
                }else{
                    $session->setDateEnd(new DateTime($donnees->end));
                }

                $entityManager = $doctrine->getManager();
                $entityManager->persist($session);
                $entityManager->flush();

                // return new Response('ok', $code);
                return $this->render('api/index.html.twig', [
                    'controller_name' => 'ApiController',
                ]);

            }else{
                    //soit les données sont incomplètes
                    return new Response('données incomplètes', 404);  // on crée une reponse et l'envoie sur une page 404
            }
    }
}