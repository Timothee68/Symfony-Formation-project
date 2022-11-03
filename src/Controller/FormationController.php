<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Category;
use App\Entity\Workshop;
use App\Entity\Formation;
use App\Form\CategoryType;
use App\Form\WorkshopType;
use App\Form\FormationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Button;

class FormationController extends AbstractController
{
    /**
     * @Route("/secretaire/category", name="app_category")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $categorys = $doctrine->getRepository(Category::class)->findBy([], ['categoryName' => 'ASC']);
        return $this->render('category/index.html.twig', [
            'categorys' => $categorys ,
        ]);
    }

    /**
     * @Route("/secretaire/formation/formations", name="app_formation")
     */
    public function formation(ManagerRegistry $doctrine): Response
    {
        $formations = $doctrine->getRepository(Formation::class)->findBy([] , ['entitled' => 'ASC']);
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

     /**
     * @Route("/secretaire/formation/add", name="add_formation" )
     * @Route("/secretaire/formation/edit/{formation_id}", name="edit_formation")
     * @ParamConverter("formation", options={"id" = "formation_id"})
     */
    public function addFormation(ManagerRegistry $doctrine, Formation $formation = null ,Request $request): Response
    {
        if(!$formation){
            $formation = new Formation();
        }
        $form = $this->createForm(FormationType::class,$formation);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $formation = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($formation);
                $entityManager->flush();
                $this->addFlash("success" , $formation->getEntitled()." à été ajouté/Modifié avec succès");
                return $this->redirectToRoute('app_formation'); 
            }
            return $this->render('formation/add.html.twig', [
                'formAddFormation' =>  $form->createView(),
                'edit' => $formation->getId(),
        ]);
    }


    /**
     * @Route("/secretaire/formation/add", name="add_category")
     * @Route("/secretaire/formation/{id}/edit", name="edit_category")
     */
    public function addCategory(ManagerRegistry $doctrine, Category $category = null , Request $request): Response
    {
            $new=false;
        if(!$category){
            $new= true;
            $category = new Category();
        }
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $category = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($category);
                $entityManager->flush();
                if($new){
                    $message = " à été ajouté avec succès";
                }else{
                    $message = " à été mis a jour avec succès";
                }
               $this->addFlash("success" , $category.' '.$message); 
                return $this->redirectToRoute('app_category'); 
            }
        return $this->render('category/add.html.twig', [
            'formAddCategory' =>  $form->createView(),
            'edit' => $category->getId(),
            'option' => $new
        ]);
    }


    
    /**
     * @Route("/secretaire/category/detail/add/{id}", name="add_workshop")
     */
    public function addWorkshop(Category $category ,ManagerRegistry $doctrine, Workshop $workshop = null , Request $request): Response
    {
        $workshop = new Workshop();      
        $form = $this->createForm(WorkshopType::class,$workshop);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $workshop = $form->getData();
                $category->addWorkshop($workshop);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($workshop);
                $entityManager->flush();
                $this->addFlash("success" , $workshop->getTitle()." à été Ajouté/Modifié avec succès");
                return $this->redirectToRoute('detail_category', ['id' =>  $workshop->getCategory()->getId()]);
            }
        return $this->render('workshop/add.html.twig', [
            'formAddWorkshop' =>  $form->createView(),
            'edit' => $workshop->getId(),
        ]);
    }

    /**
     * @Route("/secretaire/category/detail/{id}/edit", name="edit_workshop")
     */
    public function editWorkshop(ManagerRegistry $doctrine, Workshop $workshop = null , Request $request): Response
    {
        $form = $this->createForm(WorkshopType::class,$workshop);
        $form->handleRequest($request);
        // si envoye et sanitise avec les filter etc protection faille xss puis on execute le tout 
            if($form->isSubmitted() && $form->isValid())
            {
                $workshop = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($workshop);
                $entityManager->flush();
                $this->addFlash("success" , $workshop->getTitle()." à été Ajouté/Modifié avec succès");
                return $this->redirectToRoute('detail_category', ['id' => $workshop->getCategory()->getId()]);
            }
        return $this->render('workshop/add.html.twig', [
            'formAddWorkshop' =>  $form->createView(),
            'edit' => $workshop->getId(),
        ]);
    }


    /**
    * @Route("/secretaire/formation/formations/{id}/delete", name="delete_formation")
    */
    public function deleteFormation(ManagerRegistry $doctrine, Formation $formation ) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($formation);
        $entityManager->flush();
        $this->addFlash("success" , $formation->getEntitled()." à été supprimé avec succès");
        return $this->redirectToRoute("app_formation");
    }

    /**
    * @Route("/secretaire/category/detail/{id}delete", name="delete_workshop")
    */
    public function deleteWorkshop(ManagerRegistry $doctrine, Workshop $workshop ) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($workshop);
        $entityManager->flush();
        $this->addFlash("success" , $workshop->getTitle()." à été supprimé avec succès");
        return $this->redirectToRoute('detail_category', ['id' => $workshop->getCategory()->getId()]);
    }

    /**
    * @Route("/secretaire/formation/{id}delete",name="delete_category")
    */
    public function deleteCategory(ManagerRegistry $doctrine, Category $category ) :Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash("success" , $category->getCategoryName()." à été supprimé avec succès");
        return $this->redirectToRoute("app_category");
    }

    /**
    * @Route("/secretaire/category/{id}", name="detail_category")
    */
    public function detailCategory(Category $category) : Response
    {
        return $this->render('category/detail.html.twig', [
            'category' => $category,
        ]);
    }


    /**
    * @Route("/formation/{id}", name="detail_formation")
    */
    public function detail(Formation $formation): Response
    {
        return $this->render('formation/detail.html.twig', [
            'formation' => $formation,
        ]);
    }
}
