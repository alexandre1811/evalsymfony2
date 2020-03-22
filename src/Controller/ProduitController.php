<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Panier;
use App\Form\ProduitModifType;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}")
 */

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(Request $request)
    {
        $pdo =$this->getDoctrine()->getManager();
        $produit=new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $fichier = $form->get('photoupload')->getData();
            if ($fichier){
                $nomFichier= uniqid().'.'.$fichier->guessExtension();
                try {
                    $fichier->move(
                        $this->getParameter('upload_dir'),
                        $nomFichier
                    );
                }
                catch (FileException $e){
                    $this->addFlash('danger', "Impossiple d'uploader le fichier");
                    return $this->redirectToRoute('produit');
                }
                $produit->setPhoto($nomFichier);
            }
            $pdo->persist($produit);
            $pdo->flush();
        }
        $produits = $pdo->getRepository(Produit::class)->findAll();
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'form_ajout' => $form->createView(),

        ]);
    }

    /**
     * @Route("/produit/{id}", name="modifproduit")
     */

    public function modifproduit(Produit $produit=null, Request $request){
        if ($produit !=null){
            $form= $this->createForm(ProduitType::class, $produit);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($produit);
                $pdo->flush();
                $this->addFlash("success", "Produit Modifié");

            }

            return $this->render('produit/produit.html.twig', [
                'produit' => $produit,
                'form_edit'=>$form -> createView(),
            ]);
        }

        else{
            return $this->redirectToRoute('produit');
        }
    }

    /**
     *@Route("/produit/delete/{id}", name="supprproduit")
     */

    public function delete(Produit $produit=null){
        if ($produit !=null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($produit);
            $pdo->flush();
            $this->addFlash("success", "Produit supprimée");
        }
        else{
            $this->addFlash("danger", "Produit intouvable");
        }

        return $this->redirectToRoute('produit');

    }

    /**
     * @Route("/ajout/{id}", name="paniermodif")
     */
    public function ajoutpanier(Panier $panier=null, Request $request){
        if ($panier !=null){
            $form= $this->createForm(ProduitModifType::class, $panier);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($panier);
                $pdo->flush();
                $this->addFlash("success", "Produit Modifié");

            }

            return $this->render('panier/produitpanier.html.twig', [
                'form_ajoutpanier'=>$form -> createView(),
            ]);
        }

        return $this->redirectToRoute('panier');
    }

}
