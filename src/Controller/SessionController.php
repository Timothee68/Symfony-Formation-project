<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Session;
use App\Entity\Category;
use App\Entity\Workshop;
use App\Entity\Formation;
use App\Form\ProgramType;
use App\Form\SessionType;
use App\Form\WorkshopType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Intern;
use App\Repository\SessionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class SessionController extends AbstractController
{
    /**
     * affiche la liste des sessions
     * @Route("/session", name="app_session")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $sessions = $doctrine->getRepository(Session::class)->findAll();
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }



    /**
     * affiche la liste des modules
     * @Route("/session/workshop", name="app_workshop")
     */
    public function showWorkshop(ManagerRegistry $doctrine): Response
    {
        $workshops = $doctrine->getRepository(Workshop::class)->findBy([] , ['title' => "ASC"]);
        return $this->render('workshop/index.html.twig', [
            'workshops' => $workshops,
        ]);
    }

    /**
     * fonction d'ajout de session
     * @Route("/session/{id}/add", name="add_session")
     */
    public function addSession(Formation $formation, ManagerRegistry $doctrine, Session $session = null , Request $request): Response
    {
        if(!$session){
            $session = new Session();
        }
        $form = $this->createForm(SessionType::class,$session);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $session = $form->getData();
                $formation->addSession($session);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($session);
                $entityManager->flush();
                $this->addFlash("success" ,
                //  $session->getFormation()->getEntitled().
                " ?? ??t?? ajout??/Modifi?? avec succ??s");
                return $this->redirectToRoute('app_session'); 
            }
        return $this->render('session/add.html.twig', [
            'formAddSession' =>  $form->createView(),
            'edit' => $session->getId(),
        ]);
    }

    /**
     * fonction pour modifier une session d??j?? cr??e
     * @Route("/session/{id}/edit", name="edit_session")
     */
    public function editSession(ManagerRegistry $doctrine, Session $session = null , Request $request): Response
    {
        if(!$session){
            $session = new Session();
        }
        $form = $this->createForm(SessionType::class,$session);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $session = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($session);
                $entityManager->flush();
                $this->addFlash("success" ,
                //  $session->getFormation()->getEntitled().
                " ?? ??t?? ajout??/Modifi?? avec succ??s");
                return $this->redirectToRoute('app_session'); 
            }
        return $this->render('session/add.html.twig', [
            'formAddSession' =>  $form->createView(),
            'edit' => $session->getId(),
        ]);
    }

    /**
     * fonction pour ajouter un module a une session pour cr??e un programme 
     * @Route("/session/program/add/{id}", name="add_program")
     */
    public function addProgram(Session $session ,ManagerRegistry $doctrine, Program $program = null , Request $request): Response
    {
        if(!$program){
            $program = new Program();
        }
        $form = $this->createForm(ProgramType::class,$program);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $program = $form->getData();
                $session->addProgram($program);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($program);
                $entityManager->flush();
                $this->addFlash("success" , " ?? ??t?? ajout??/Modifi?? avec succ??s");
                return $this->redirectToRoute('detail_session', ['id' => $program->getSession()->getId()]); 
            }
        return $this->render('session/addProgram.html.twig', [
            'formAddProgram' =>  $form->createView(),
            'edit' => $program->getId(),
        ]);
    }

     /**
      * fonction pour modifier un module a une session pour cr??e un programme 
     * @Route("/session/program/{id}/edit", name="edit_program")
     */
    public function editProgram(ManagerRegistry $doctrine, Program $program = null , Request $request): Response
    {
        if(!$program){
            $program = new Program();
        }
        $form = $this->createForm(ProgramType::class,$program);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $program = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($program);
                $entityManager->flush();
                $this->addFlash("success" , " ?? ??t?? ajout??/Modifi?? avec succ??s");
                return $this->redirectToRoute('detail_session', ['id' => $program->getSession()->getId()]); 
            }
        return $this->render('session/addProgram.html.twig', [
            'formAddProgram' =>  $form->createView(),
            'edit' => $program->getId(),
        ]);
    }


    /**
     * affiche le detail d'une session par son id
     * @Route("/session/{id}/detail", name="detail_session")
     */
    public function detail(Session $session,SessionRepository $sr)
    {   
        $noInscrits = $sr->findInternNotInSession($session->getId());
        return $this->render('session/detail.html.twig', [
            'session' => $session,
            'noInscrits' => $noInscrits,
        ]);
    }

    /**
     * fonction pour ajouter un stagiaire pour la session en prenant en compte l'id session et l'id du stagiaire
     * @Route("/session/{session_id}/detail/add/{intern_id}", name="addIntern_session")
     * @Entity("session", expr="repository.find(session_id)")
     * @Entity("intern", expr="repository.find(intern_id)")
     */
    //  @Entity pr??cise ici la variable par laquelle il va cherchez les infos dans l'entit?? => ("session") expr va chercher dans le repo de l'entit?? en question l'id
    //  dans la route pour diff??rencier les diff??rents id nomm??e diff??rement expr??s pour facilite cela
    public function addInternInSession(ManagerRegistry $doctrine,Session $session ,Intern $intern)
    {
        // ici on utilise la fonction addIntern de l'entit?? via la relation manyToMany puis on persit et envoie les infos en BDD
        $session->addIntern($intern);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($session);
        $entityManager->flush();
        return $this->redirectToRoute('detail_session' , [ 'id' => $session->getId() ]);
    }

     /**
     * @Route("/session/{session_id}/detail/remove/{intern_id}", name="removeIntern_session")
     * @Entity("session", expr="repository.find(session_id)")
     * @Entity("intern", expr="repository.find(intern_id)")
     */
    public function removeInternInSession(ManagerRegistry $doctrine, Session $session ,Intern $intern)
    {
        dump($session);
        dump($intern);
        $session->removeIntern($intern);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($session);
        $entityManager->flush();
        return $this->redirectToRoute('detail_session' , [ 'id' => $session->getId() ]);
    }

    /**
    * @Route("/session/{id}delete", name="delete_session")
    */
    public function deleteSession(ManagerRegistry $doctrine, Session $session ) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($session);
        $entityManager->flush();
        $this->addFlash("success" ,
        //  $session->getFormation()->getEntitled().
         " ?? ??t?? supprim?? avec succ??s");
        return $this->redirectToRoute("app_session");
    }

    /**
    * @Route("/program/{id}delete", name="delete_program")
    */
    public function deleteProgram(ManagerRegistry $doctrine, Program $program ) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($program);
        $entityManager->flush();
        $this->addFlash("success" , "module ?? ??t?? supprim?? avec succ??s");
        return $this->redirectToRoute('detail_session', ['id' => $program->getSession()->getId()]);
    }

}
