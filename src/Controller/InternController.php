<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Intern;
use App\Entity\Message;
use App\Form\InternType;
use App\Form\MessageType;
use App\Entity\MessageMail;
use App\Form\MessageMailType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class InternController extends AbstractController
{
    /**
     * @Route("/intern", name="app_intern")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $interns = $doctrine->getRepository(Intern::class)->findBy([],['firstName' => 'ASC']);
        return $this->render('intern/index.html.twig', [
            'interns' => $interns,
        ]);
    }


    
     /**
     * @Route("/intern/add", name="add_intern")
     * @Route("/intern/{id}edit", name="edit_intern")
     */
    public function add(ManagerRegistry $doctrine, Intern $intern = null , Request $request): Response
    {
        if(!$intern){
            $intern = new Intern();
        }
        $form = $this->createForm(InternType::class,$intern);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $intern = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($intern);
                $entityManager->flush();
                $this->addFlash("success" , $intern->getFirstName()." ".$intern->getName()." à été ajouté/Modifié avec succès");
                return $this->redirectToRoute('app_intern'); 
            }
        return $this->render('intern/add.html.twig', [
            'formAddIntern' =>  $form->createView(),
            'edit' => $intern->getId(),
        ]);
    }
    // * 
    // * @Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_USER')")
    /**
     * @Route("/message", name="app_message")
     * @IsGranted("ROLE_SECRETAIRE") or is_granted('ROLE_FORMATEUR)
     */
    public function message(ManagerRegistry $doctrine , Message $message= null, Request $request) : Response
    {
        if(!$message){
            $message = new Message();
        }
        $form = $this->createForm(MessageType::class,$message);
        $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $message = $form->getData();
                // ici ou modifie le user de message avec l'utilisateur en cours 
                $message->setUser($this->getUser());
                $entityManager = $doctrine->getManager();
                $entityManager->persist($message);
                $entityManager->flush();
                $this->addFlash("success" , "Message envoyé avec succès !");
                return $this->redirectToRoute('app_message'); 
            }
            $messages= $doctrine->getRepository(Message::class)->findBy([] , ["dateCreation" => "ASC"] );
            return $this->render('message/index.html.twig', [
                'messages' => $messages,
                'formAddMessage' =>  $form->createView(),
                'edit' => $message->getId(),
        ]);
    }

    /**
     * @Route("/formateur/messageMail", name="app_messageMail")
     */
    public function messagerie(ManagerRegistry $doctrine , MessageMail $message= null, UserRepository $user, Request $request) : Response
    {
        if(!$message){
            $message = new MessageMail();
        }
        $form = $this->createForm(MessageMailType::class,$message);
        $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $message = $form->getData();
                // ici ou modifie le user de message avec l'utilisateur en cours 
                $message->setSender($this->getUser());
                $entityManager = $doctrine->getManager();
                $entityManager->persist($message);
                $entityManager->flush();
                $this->addFlash("success" , "Message envoyé avec succès !");
                return $this->redirectToRoute('app_messageMail'); 
            }
            $user = $doctrine->getRepository(User::class)->findOneBy(['id' => $this->getUser() ]);
            $messages= $doctrine->getRepository(MessageMail::class)->findBy([] , ["createdAt" => "ASC"] );
            return $this->render('message/message.html.twig', [
                'user' => $user,
                'messages' => $messages,
                'formAddMessageMail' =>  $form->createView(),
                'edit' => $message->getId(),
        ]);
    }


    /**
     * fonction qui permet de renvoyer les emails recus par les autres utilisateurs
     * @Route("/received/{id}", name="app_received")
     */
    public function received(ManagerRegistry $doctrine , MessageMail $message= null, Request $request) : Response
    {
        // dans les parametre de la fonction je récupere le message recus et je crée un nouveaux messages $response 
        $response = new MessageMail();
        $response->setRecipient($message->getSender());
        $form = $this->createForm(MessageMailType::class,$response);
        $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $response = $form->getData();
                $response->setSender($this->getUser());
                $entityManager = $doctrine->getManager();
                $entityManager->persist($response);
                $entityManager->flush();
                $this->addFlash("success" , "Message envoyé avec succès !");
                return $this->redirectToRoute('app_messageMail'); 
            }
        return $this->render('message/received.html.twig', [
            'formAddMessageMail' =>  $form->createView(),
            'message' => $message,
        ]);
    }

    /**
     * fonction qui permet de renvoyer les emails recus par l'utilisateur en cour
     * @Route("/sent/{id}", name="app_sent")
     */
    public function sent(MessageMail $message): Response
    {
        return $this->render('message/send.html.twig', [
            'message' => $message,
        ]);
    }

     /**
     * fonction qui permet en cliquant sur le mail de le passer a lu  
     * @Route("/read/{id}", name="app_read")
     */
    public function read(ManagerRegistry $doctrine ,MessageMail $message): Response
    {
        $message->setIsRead(true);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($message);
        $entityManager->flush();
        $this->addFlash("success" , "Message lu !");
        return $this->render('message/read.html.twig', compact("message", [
            'message' => $message,
        ]));
    }
    

    /**
    * @Route("/intern/{id}delete", name="delete_intern")
    */
    public function delete(ManagerRegistry $doctrine, Intern $intern ) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($intern);
        $entityManager->flush();
        $this->addFlash("success" , $intern->getFirstName()." ".$intern->getName()." à été supprimé avec succès");
        return $this->redirectToRoute("app_intern");
    }
    
    /**
    * @Route("/intern/{id}", name="detail_intern")
    */
    public function detail(Intern $intern): Response
    {
        return $this->render('intern/detail.html.twig', [
            'intern' => $intern,
        ]);
    }

}
