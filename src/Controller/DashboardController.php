<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Commande;
use App\Entity\Vehicules;
use App\Form\VehiculesType;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\VehiculesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(VehiculesRepository $repo): Response
    {
        $vehicules = $repo->findAll();
        return $this->render('dashboard/home.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }
    

        #[Route('/dashboard/show/{id}', name:"dashboard_show")]
        public function show($id, VehiculesRepository $repo)
        {
            $vehicules = $repo->find($id) ;
            return $this->render('dashboard/show.html.twig', [
                'vehicules' => $vehicules,
            ]);
        }

        // Gestion membre 

#[Route('/dashboard/user/modifier/{id}', name:"dashboard_user_modifier")]
#[Route('/dashboard/user/ajout', name:'dashboard_user_ajout')]
public function form(Request $globals, EntityManagerInterface $manager,  UserRepository $repo, User $user) : Response
    {
    if($user == null)
        {
            $user =  new User ;
        } 
        $editMode = ($user->getId() !== null);
        $user = $repo->findAll();
        $user =  new User ;
    
    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($globals);
    if($form->isSubmitted() && $form->isValid())
    {
        $user->setDateEnregistrement(new \Datetime);
        $manager->persist($user);
        $manager->flush();
    }

        return $this->render('dashboard/formUser.html.twig', [
            'formUser' => $form,
            'editMode' => $user->getId() !== null,
            'user' => $user

        ]);
    }

#[Route('/dashboard/user/supprimer/{id}', name:'dashboard_user_supprimer')]
public function supprimer(User $user, EntityManagerInterface $manager)
{
    $manager->remove($user);
    $manager-> flush();    
    return $this->redirectToRoute('form_user');
}


// ---------------------------------------------- Gestion Commande ------------------------

#[Route('/commande', name:"commande")]
public function commande(CommandeRepository $repo) : Response
{
    $commande = $repo->findAll() ;
    return $this->render('admin/index.html.twig', [
        'commandes' => $commande,
    ]);
}


#[Route('/commande/modifier/{id}', name:"commande_modifier")]
#[Route('//commande/modifier/ajout', name:'commande_ajout')]
public function table(Request $globals, EntityManagerInterface $manager, Commande $commande, CommandeRepository $repo, $se) : Response
    {

        if($commande == null)
        {
            $commande =  new User ;
        } 
        
        $editMode = ($commande->getId() !== null);
        $commande = $repo->findAll();
        $commande =  new Commande ;
        $se->handleRequest($globals);

    

    $se->handleRequest($globals);
    if($se->isSubmitted() && $se->isValid())
    {
        $commande->setDateHeureDepart(new \Datetime);
        $commande->setDateHeureFin(new \Datetime);
        $manager->persist($commande);
        $manager->flush();

        if($editMode)
            {
                $this->addFlash('success', "La commande a bien été modifié");
            }else{

                $this->addFlash('success', "La commande a bien été ajouté");
            }

            //* redirectToRoute() permet de rediriger vers une autre page de notre site a l'aide du nom de la route (name)
            return $this->redirectToRoute('commande');
    }

        return $this->render('admin/index.html.twig
        ', [
            'editMode' => $commande->getId() !== null,
            'commande' => $commande

        ]);
    }
#[Route('/commande/supprimer/{id}', name:'comande_supprimer')]
    public function supprime(Commande $commande, EntityManagerInterface $manager) : Response
    {
        $manager->remove($commande);
        $manager-> flush();    

        return $this->redirectToRoute('admin/index.html.twig');
    }

}


