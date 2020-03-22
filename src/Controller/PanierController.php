<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Form\ProduitModifType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}")
 */

class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier")
     */
    public function index(Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();
        $panier=new Panier();
        $form= $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $pdo->persist($panier);
            $pdo->flush();
            $this->addFlash(
                "success",'Ajouter au panier'
            );
        }

        $paniers = $pdo->getRepository(Panier::class)->findAll();
        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
        ]);
    }




    /**
     *@Route("/delete/{id}", name="supprpanier")
     */

    public function delete(Panier $panier=null){
        if ($panier!=null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($panier);
            $pdo->flush();
            $this->addFlash("success", "Article supprimée du panier");
        }

        else{
            $this->addFlash("danger", "Article intouvable");
        }

        return $this->redirectToRoute('panier');

    }
}
