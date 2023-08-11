<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Vehicules;
use App\Form\VehiculesType;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use App\Repository\VehiculesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
class AdminController extends AbstractController
{
    // Gestion voitures 
    #[Route('/dashboard/modifier/{id}', name:"dashboard_modifier")]
    #[Route('/dashboard/ajout', name:'dashboard_ajout')]
    public function form(Request $globals, EntityManagerInterface $manager, Vehicules $vehicule = null,  SluggerInterface $slugger, VehiculesRepository $repo) : Response
        {
            $vehicules = $repo->findAll();
            if($vehicule == null)
        {
            
            $vehicule =  new Vehicules ;
        }
        $editMode = ($vehicule->getId() !== null);
        $form = $this->createForm(VehiculesType::class, $vehicule);

        $form->handleRequest($globals);
        if($form->isSubmitted() && $form->isValid())
        {
            //! Début traitement de l'image 
            $imageFile = $form->get('photo')->getData();
 
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('img_upload'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                               
                $vehicule->setPhoto($newFilename);
            }   
            //!fin du traitement de l'image
            $vehicule->setDateEnregistrment(new \Datetime);
            $manager->persist($vehicule);
            $manager->flush();
            if($editMode)
            {
                $this->addFlash('success', "La voiture a bien été modifié");
            }else{

                $this->addFlash('success', "La voiture a bien été ajouté");
            }
            return $this->redirectToRoute('dashboard_gestion');
        }

        
        return $this->render('admin/form.html.twig', [
            'formVehicules' => $form,
            'editMode' => $vehicule->getId() !== null,
            'vehicules' => $vehicules
            
        ]);
    }

        #[Route('/dashboard/supprimer/{id}', name:'dashboard_supprimer')]
    public function supprimer(vehicules $vehicules, EntityManagerInterface $manager) : Response
    {
        $manager->remove($vehicules);
        $manager-> flush();    

        return $this->redirectToRoute('dashboard_gestion');
    }

    #[Route('/dashboard/gestion', name:'dashboard_gestion')]
        public function gestion(VehiculesRepository $repo) : Response
        {
            $vehicules = $repo->findAll();
            return $this->render('admin/gestion.html.twig', [
                'vehicules' => $vehicules,
            ]);
        }


       

        
}
