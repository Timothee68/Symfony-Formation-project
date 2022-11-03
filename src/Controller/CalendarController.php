<?php

namespace App\Controller;

use DateTime;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use App\Repository\SessionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/calendar")
 */
class CalendarController extends AbstractController
{
    /**
     * @Route("/", name="app_calendar_index", methods={"GET"})
     */
    public function index(CalendarRepository $calendar, SessionRepository $session): Response
    {
        $events = $calendar->findAll();
        $sessions = $session->findAll();
        //   dd($events); =>  debugeur un peut comme var_dump() ou dump()
            foreach($events as $event){
                $rdv[]= [
                    'id' => $event->getId(),
                    'start' => $event->getStart()->format('Y-m-d H:i:s'),
                    'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                    'title' => $event->getTitle(),
                    'description' => $event->getDescription(),
                    'backgroundColor' => $event->getBackgroundColor(),
                    'borderColor' => $event->getBorderColor(),
                    'textColor' => $event->getTextColor(),
                    'allDay' => $event->isAllDay(),

                ];
            foreach($sessions as $session){
                $dataSession[] = [
                    'id' =>  $session->getId(),
                    'title' =>  $session->getTitle(),
                    'nbPlace' => $session->getNbPlace(),
                    'dateStart' => $session->getDateStart()->format('Y-m-d H:i:s'),
                    'dateEnd' => $session->getDateEnd()->format('Y-m-d H:i:s'),

                ];
            }
            }
            $data=json_encode($rdv);
            $dataOfSession=json_encode($dataSession);

        return $this->render('calendar/index.html.twig' ,[
            'calendars' => $calendar->findAll(),
            'data' => $data,
            'dataOfSession' => $dataOfSession,
        ]);
    }


    /**
     * @Route("/new", name="app_calendar_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CalendarRepository $calendarRepository): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendarRepository->add($calendar, true);

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_calendar_show", methods={"GET"})
     */
    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_calendar_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Calendar $calendar, CalendarRepository $calendarRepository): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendarRepository->add($calendar, true);

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }



}
