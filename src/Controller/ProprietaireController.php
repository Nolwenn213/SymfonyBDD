<?php

namespace App\Controller;

use App\Entity\Proprietaire;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormTypeInterface;

use App\Entity\Chaton;
use App\Form\ChatonType;
use App\Form\ProprietaireType;
use App\Entity;
use App\Form\ProprietaireSupprimerType;

class ProprietaireController extends AbstractController
{
    /**
     * @Route("/proprietaire/", name="proprietaire_voir")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $proprietaire = $doctrine->getRepository(Proprietaire::class)->findAll();



        return $this->render('proprietaire/index.html.twig', [
            'proprietaire' => $proprietaire
        ]);
    }




    /**
     * @Route("/proprietaire/ajouter", name="proprietaire_ajouter")
     */
    public function ajouterProprietaire(ManagerRegistry $doctrine, Request $request)
    {
        $proprietaire = new Proprietaire();

        $form = $this->createForm(ProprietaireType::class, $proprietaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($proprietaire);
            $em->flush();

            //retour à l'accueil
            return $this->redirectToRoute("proprietaire_voir", ["idProprietaire" => $proprietaire->getId()]);
        }

        return $this->render("proprietaire/ajouter.html.twig", [
            'formulaire' => $form->createView()
        ]);
    }




    /**
     * @Route("/proprietaire/modifier/{id}", name="proprietaire_modifier")
     */
    public function modifierProprietaire($id, ManagerRegistry $doctrine, Request $request)
    {
        //récupérer le chaton dans la BDD
        $proprietaire = $doctrine->getRepository(ProprietaireType::class)->find($id);

        //si on n'a rien trouvé -> 404
        if (!$proprietaire) {
            throw $this->createNotFoundException("Aucun proprietaire avec l'id $id");
        }

        //si on arrive là, c'est qu'on a trouvé un chaton
        //on crée le formulaire avec (il sera rempli avec ses valeurs)
        $form = $this->createForm(ProprietaireType::class, $proprietaire);

        //Gestion du retour du formulaire
        //on ajoute Request dans les paramètres comme dans le projet précédent
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //le handleRequest a rempli notre objet $categorie
            //qui n'est plus vide
            //pour sauvegarder, on va récupérer un entityManager de doctrine
            //qui comme son nom l'indique gère les entités
            $em = $doctrine->getManager();
            //on lui dit de la ranger dans la BDD
            $em->persist($proprietaire);

            //générer l'insert
            $em->flush();

            //retour à l'accueil
            return $this->redirectToRoute("app_home", ["idCategorie" => $proprietaire->getCategorie()->getId()]);
        }

        return $this->render("proprietaire/modifier.html.twig", [
            'proprietaire' => $proprietaire,
            'formulaire' => $form->createView()
        ]);
    }





    /**
     * @Route("/proprietaire/supprimer/{id}", name="proprietaire_supprimer")
     */
    public function supprimerProprietaire($id, ManagerRegistry $doctrine, Request $request)
    {
        //récupérer la catégorie dans la BDD
        $proprietaire = $doctrine->getRepository(Proprietaire::class)->find($id);

        //si on n'a rien trouvé -> 404
        if (!$proprietaire) {
            throw $this->createNotFoundException("Aucun proprio avec l'id $id");
        }

        //si on arrive là, c'est qu'on a trouvé une catégorie
        //on crée le formulaire avec (il sera rempli avec ses valeurs
        $form = $this->createForm(ProprietaireSupprimerType::class, $proprietaire);

        //Gestion du retour du formulaire
        //on ajoute Request dans les paramètres comme dans le projet précédent
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //le handleRequest a rempli notre objet $categorie
            //qui n'est plus vide
            //pour sauvegarder, on va récupérer un entityManager de doctrine
            //qui comme son nom l'indique gère les entités
            $em = $doctrine->getManager();
            //on lui dit de la supprimer de la BDD
            $em->remove($proprietaire);

            //générer l'insert
            $em->flush();

            //retour à l'accueil
            return $this->redirectToRoute("app_home");
        }

        return $this->render("proprietaire/supprimer.html.twig", [
            'proprietaire' => $proprietaire,
            'formulaire' => $form->createView()
        ]);
    }
}
