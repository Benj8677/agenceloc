<?php

namespace App\Controller;

use App\Entity\Commande;
use PHPUnit\TextUI\Command;
use App\Form\ReservationType;
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

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('boutique/about.html.twig');
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
            $dateD = $form->get("dateDebut")->getData()->getTimestamp();
            $dateR = $form->get("dateRetour")->getData()->getTimestamp();
            $hD = $form->get("heureDebut")->getData()*60*60;
            $hR = $form->get("heureRetour")->getData();
            

            $debut = $dateD + $hD;
            $fin = $dateR + $hR;


            //$debut = $commande->getDateDepart()->getTimestamp();
            //$fin = $commande->getDateFin()->getTimestamp();
            if ($debut<$fin)
            {
                $interval = $fin-$debut;

                $interval = ceil($interval/60/60/24);

                $prixTotal = $interval * $commande->getVehicule()->getPrixJour();
                $commande->setPrixTotal(ceil($prixTotal));

                $debut = (new \DateTime())->setTimestamp($debut);
                $fin = (new \DateTime())->setTimestamp($fin);
    
                $commande->setDateDepart($debut);
                $commande->setDateFin($fin);
                    
                $manager->persist($commande);
                $manager->flush();
                $this->addFlash('success', "Votre r??servation a ??t?? enregistr?? !");
                return $this->redirectToRoute("app_compte");
            }
            else
            {
                $this->addFlash('error', "Vos dates de r??servation sont invalide !");
                return $this->redirectToRoute("app_resa",[
                    "id" => $vehicule->getId()
                ]);
            }
        }
        
        return $this->renderForm('boutique/reservation.html.twig', [
            'formResa' => $form,
            'vehicule' => $vehicule,
        ]);
    }

}

