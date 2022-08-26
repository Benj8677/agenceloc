<?php

namespace App\Controller;

use App\Repository\VehiculeRepository;
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

}

