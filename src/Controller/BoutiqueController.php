<?php

namespace App\Controller;

use App\Entity\Commande;
use PHPUnit\TextUI\Command;
use App\Form\ReservationType;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BoutiqueController extends AbstractController
{

    #[Route('/', name: 'app_boutique')]
    public function index(VehiculeRepository $repo): Response
    {
        $vehicules = $repo->findBy([], ['date_enregistrement' => 'DESC'], 4);
        return $this->render('boutique/index.html.twig', [
            'tabVehicules' => $vehicules,
        ]);
    }

    #[Route('/compte', name: 'app_compte')]
    public function compte(): Response
    {
        $user = $this->getUser();
        return $this->render('boutique/compte.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/vehicules', name: 'app_liste')]
    public function liste(Request $request, VehiculeRepository $repo,): Response
    {
        $tri = ($request->query->get('tri'));
        if ($tri==null)
        {
            $vehicules = $repo->findBy([], ['date_enregistrement' => 'DESC']);
        }
        else
        {
            $tri = explode('-', $tri);
            $vehicules = $repo->findBy([], [$tri[0] => $tri[1]]);
        }

        return $this->render('boutique/liste.html.twig', [
            'tabVehicules' => $vehicules,
        ]);
    }

    #[Route('/vehicule/{id}', name: 'app_show')]
    public function show($id, VehiculeRepository $repo): Response
    {
        $vehicule =  $repo->find($id);

        
        return $this->renderForm('boutique/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/reservation/{id}', name: 'app_resa')]
    public function resa(Request $superglobals, EntityManagerInterface $manager, $id, VehiculeRepository $repo, Command $commande=null): Response
    {
        $vehicule =  $repo->find($id);

        if (!$commande)
        {
            $commande = new Commande;
            $commande->setDateEnregistrement(new \DateTime());
            $commande->setUser($this->getUser());
            $commande->setVehicule($vehicule);
        }
        
        $form = $this->createForm(ReservationType::class, $commande);

        $form->handleRequest($superglobals);

        if ($form->isSubmitted() && $form->isValid())
        {
            $debut = $commande->getDateDepart()->getTimestamp();
            $fin = $commande->getDateFin()->getTimestamp();
            if ($debut<$fin)
            {
                $interval = $fin-$debut;

                $interval = ceil($interval/60/60/24);

                $prixTotal = $interval * $commande->getVehicule()->getPrixJour();
                $commande->setPrixTotal(ceil($prixTotal));
                
                $manager->persist($commande);
                $manager->flush();
                $this->addFlash('success', "Votre réservation a été enregistré !");
                return $this->redirectToRoute("app_compte");
            }
            else
            {
                $this->addFlash('error', "Vos dates de réservation sont invalide !");
                return $this->redirectToRoute("app_compte");
            }
        }
        
        return $this->renderForm('boutique/reservation.html.twig', [
            'formResa' => $form,
            'vehicule' => $vehicule,
        ]);
    }

}

