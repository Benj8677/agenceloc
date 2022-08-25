<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use App\Form\VehiculeType;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    // Gestion Véhicule
    
    #[Route('/admin/vehicules', name: "admin_vehicules")]
    public function adminArticles(VehiculeRepository $repo, EntityManagerInterface $manager)
    {
        $champs = $manager->getClassMetadata(Vehicule::class)->getFieldNames();

        //dd($champs); // dump & die

        $vehicules = $repo->findAll();

        return $this->render("admin/admin_vehicules.html.twig", [
            "vehicules" => $vehicules,
            "champs" => $champs
        ]);
    }
    
    #[Route('/admin/vehicule/new', name: "admin_new_vehicule")]
    #[Route('/admin/vehicule/edit/{id}', name: "admin_edit_vehicule")]
    public function vehicule_form(Request $superglobals, EntityManagerInterface $manager, Vehicule $vehicule=null)
    {
        if (!$vehicule)
        {
            $vehicule = new Vehicule;
            $vehicule->setDateEnregistrement(new \DateTime());
        }

        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->handleRequest($superglobals);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($vehicule);
            $manager->flush();
            $this->addFlash('success', "Le vehicule a bien été modifié/ajouté !");
            return $this->redirectToRoute("admin_vehicules");
        }

        return $this->renderForm("admin/vehicule_form.html.twig", [
            'formVehicule' => $form,
            'editMode' => $vehicule->getId() !== null
        ]);

    }

    #[Route("/admin/vehicule/delete/{id}", name:"admin_del_vehicule")]
    public function delete(EntityManagerInterface $manager, $id, VehiculeRepository $repo)
    {
        $vehicule = $repo->find($id);

        $manager->remove($vehicule);

        $manager->flush();

        $this->addFlash('success', "Le vehicule a bien été supprimé !");

        return $this->redirectToRoute('admin_vehicules');
    }

    // Gestion User

    #[Route('/admin/users', name: "admin_users")]
    public function adminUser(UserRepository $repo, EntityManagerInterface $manager)
    {
        $champs = $manager->getClassMetadata(User::class)->getFieldNames();

        //dd($champs); // dump & die

        $users = $repo->findAll();

        return $this->render("admin/admin_users.html.twig", [
            "users" => $users,
            "champs" => $champs
        ]);
    }
    
    #[Route('/admin/user/new', name: "admin_new_user")]
    #[Route('/admin/user/edit/{id}', name: "admin_edit_user")]
    public function user_form(Request $superglobals, EntityManagerInterface $manager, User $user=null)
    {
        if (!$user)
        {
            $user = new user;
            $user->setDateEnregistrement(new \DateTime());
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($superglobals);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié/ajouté !");
            return $this->redirectToRoute("admin_users");
        }

        return $this->renderForm("admin/user_form.html.twig", [
            'formUser' => $form,
            'editMode' => $user->getId() !== null
        ]);

    }

    #[Route("/admin/user/delete/{id}", name:"admin_del_user")]
    public function deleteUser(EntityManagerInterface $manager, $id, UserRepository $repo)
    {
        $user = $repo->find($id);

        $manager->remove($user);

        $manager->flush();

        $this->addFlash('success', "L'utilisateur a bien été supprimé !");

        return $this->redirectToRoute('admin_users');
    }
    

    // Gestion Commande

    #[Route('/admin/commandes', name: "admin_commandes")]
    public function adminCommande(CommandeRepository $repo, EntityManagerInterface $manager)
    {
        $champs = $manager->getClassMetadata(Commande::class)->getFieldNames();

        //dd($champs); // dump & die

        $commandes = $repo->findAll();

        return $this->render("admin/admin_commandes.html.twig", [
            "commandes" => $commandes,
            "champs" => $champs
        ]);
    }
    
    #[Route('/admin/commande/new', name: "admin_new_commande")]
    #[Route('/admin/commande/edit/{id}', name: "admin_edit_commande")]
    public function commande_form(Request $superglobals, EntityManagerInterface $manager, Commande $commande=null)
    {
        if (!$commande)
        {
            $commande = new commande;
            $commande->setDateEnregistrement(new \DateTime());
        }

        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($superglobals);

        if ($form->isSubmitted() && $form->isValid())
        {
            $debut = $commande->getDateDepart();
            $fin = $commande->getDateFin();
            $interval = $debut->diff($fin);
            if (intval($interval->h)>0)
            {
                $interval = intval($interval->d)+1;
            }
            else
            {
                $interval = intval($interval->d);
            }
            $prixTotal = $interval * $commande->getVehicule()->getPrixJour();
            $commande->setPrixTotal(ceil($prixTotal));
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', "La commande a bien été modifié/ajouté !");
            return $this->redirectToRoute("admin_commandes");
        }

        return $this->renderForm("admin/commande_form.html.twig", [
            'formCommande' => $form,
            'editMode' => $commande->getId() !== null
        ]);

    }

    #[Route("/admin/commande/delete/{id}", name:"admin_del_commande")]
    public function deleteCommande(EntityManagerInterface $manager, $id, CommandeRepository $repo)
    {
        $commande = $repo->find($id);

        $manager->remove($commande);

        $manager->flush();

        $this->addFlash('success', "La commande a bien été supprimé !");

        return $this->redirectToRoute('admin_commandes');
    }
}
