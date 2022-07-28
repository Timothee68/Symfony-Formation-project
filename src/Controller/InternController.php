<?php

namespace App\Controller;

use App\Entity\Intern;
use App\Entity\Session;
use App\Form\InternType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
