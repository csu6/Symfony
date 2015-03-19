<?php
// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class AdvertController extends Controller
{

  public function menuAction()
  {
	$listAdverts = array(
		array('id' => 2, 'title' => 'Recherche développeur Symfony 2'),
		array('id' => 5, 'title' => 'Mission de webmaster'),
		array('id' => 9, 'title' => 'Offre de stage webdesigner')
	);
	
	return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
		'listAdverts' => $listAdverts
	));
  }
  
  
  public function indexAction($page)
  {

    // Notre liste d'annonce en dur
    $listAdverts = array(
      array(
        'title'   => 'Recherche développpeur Symfony2',
        'id'      => 1,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Mission de webmaster',
        'id'      => 2,
        'author'  => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Offre de stage webdesigner',
        'id'      => 3,
        'author'  => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date'    => new \Datetime())
    );

    // Et modifiez le 2nd argument pour injecter notre liste
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts
    ));

  }

  public function viewAction($id)
  {

	// On récupère le repository
	$em = $this->getDoctrine()->getManager();
	
	// On recupere l'annonce $id
	$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
	
	if(null === $advert) {
	  throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
	}

    // Onr ecupere la liste des candidatures de cette annonce
	$listApplications = $em
	  ->getRepository('OCPlatformBundle:Application')
	  ->findBy( array('advert' => $advert) )
	  ;
	  
	 // On recupere maintenant la liste des AvertSkill
	$listAdvertSkills = $em
	  ->getRepository('OCPlatformBundle:AdvertSkill')
	  ->findBy( array('advert' => $advert) )
	  ;
	  
	  // On récupère l'entité correspondante à l'id $id
	  //$advert = $repository->find($id);
	  
	  // ou
	  // $advert = $this->getDoctrine()->getManager()->find('OCPlatformBundle', $id);
	  
	  // $advert est donc une instance de OC\Platformbundle\Entity\Advert
	  // ou null si l'id n'existe pas, d'où ce if :
	  if (null === $advert) {
	    throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas");
	  }
	  
	  // Le render ne change pas, on passait avant un tableau, maintenant un objet
	  return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
	    'advert' => $advert,
		'listApplications' => $listApplications,
		'listAdvertSkills' => $listAdvertSkills
	  ));
  
	/*
    $advert = array(
      'title'   => 'Recherche développpeur Symfony2',
      'id'      => $id,
      'author'  => 'Alexandre',
      'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
      'date'    => new \Datetime()
    );
	
	return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
	  'advert' => $advert
	));
	*/
  }

  public function addAction(Request $request)
  {
	// On recupere l'EntityManager
	$em = $this->getDoctrine()->getManager();
  
	// Creation de l'entite
	$advert = new Advert();
	$advert->setTitle("Recherche développeur Symfony2.");
	$advert->setAuthor("Alexandre");
	$advert->setContent("Nous recherchons un développeur Symfony2 débutant sur Lyon. Blablabla");

	
	
    // On récupère toutes les compétences possibles
    $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();
	  
	// Pour chaque compétence
	foreach($listSkills as $skill) {
	  // On crée une novelle " relation entre 1 annonce et 1 compétence "
	  $advertSkill = new AdvertSkill();
	  
	  // On lie a l'annonce, qui est ici toujours la meme
	  $advertSkill->setAdvert($advert);
	  // On la lie à la compétence, qui change ici dans la boucle foreach
	  $advertSkill->setSkill($skill);
	  
	  // Arbitrement, ont dit que chaque compétence est requise au niveau 'Expert'
	  $advertSkill->setLevel('Expert');
	  
	  // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
	  $em->persist($advertSkill);
	  
	  // On déclence l'enregistrement
	//  $em->flush();
	}
	
	
	
	// Creation d'une premiere candidature
	$application1 = new Application();
	$application1->setAuthor('Marine');
	$application1->setContent("J'ai toutes les qualités requises");
	
	// Creation d'une deuxieme candidature par exemple
	$application2 = new Application();
	$application2->setAuthor('Pierre');
    $application2->setContent("Je suis très motivé");
	
	// On lie les candidatures à l'annonce
	$application1->setAdvert($advert);
	$application2->setAdvert($advert);
	
	
	// On recupere l'EntityManager
	$em = $this->getDoctrine()->getManager();
	
	// Etape 1 : On "persiste" l'entite
	$em->persist($advert);
	
	// Etape 1 bis : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
	// définie dans l'entité Application et non Advert. On doit tout persister à la main ici.
	$em->persist($application1);
	$em->persist($application2);
	

	
	
	// Creation de l'entité Image
	$image = new Image();
	$image->setUrl("http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg");
	$image->setAlt("Job de rêve");
	
	// On lit l'image à l'annonce
	$advert->setImage($image);
	
	
	
		/*
	// On peut ne pas définir ni la date ni la publication
	// car ces attributs sont définies automatiquement dans le constructeur
	
	// On récupere l'EntityManager
	$em = $this->getDoctrine()->getManager();
	
	// Etape 1 : On "persiste" l'entité
	$em->persist($advert);
	
	// Etape 1 bis: si on n'avait pas défini le cascade={"persist"},
	// on devrait persister à la main l'entité$image
	// $em->persist($image);
	*/
	// Etape 2 : On "flush" tout ce qui a été persisté avant
	$em->flush();
	
	// Reste de la méthode qu'on avait déjà écrit
	if( $request->isMethod('POST') ) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
	  return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
	}
	
	return $this->render('OCPlatformBundle:Advert:add.html.twig');
	
	
	/*
    // On recupere le service
	$antispam = $this->container->get('oc_platform.antispam');
	
	// Je pars du principe que $text contient le texte d'un message quelconque
	$text = '...';
	if($antispam->isSpam($text)) {
		throw new \Exception('Votre message a été détecté comme spam !');
	}
	*/
  }

  public function editAction($id, Request $request)
  {
	/*
    $advert = array(
      'title'   => 'Recherche développpeur Symfony2',
      'id'      => $id,
      'author'  => 'Alexandre',
      'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
      'date'    => new \Datetime()
    );
    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
	*/
	
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // La méthode findAll retourne toutes les catégories de la base de données
    $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

    // On boucle sur les catégories pour les lier à l'annonce
    foreach ($listCategories as $category) {
      $advert->addCategory($category);
    }

    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    // Étape 2 : On déclenche l'enregistrement
    $em->flush();

    // … reste de la méthode
	
  }

  public function deleteAction($id)
  {
    $em = $this->getRepository('OCPlatformBundle:Advert')->find($id);
    
	if(null === $advert) {
	  throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
	}
	
	// On boucle sur les catégories de l'annonce pour les supprimer
	foreach($advert->getCategories() as $category) {
	  $advert->removeCategory($category);
	}

    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    // On déclenche la modification
    $em->flush();
    //return $this->render('OCPlatformBundle:Advert:delete.html.twig');
  }
  
  // Et voici un autre exemple, qui modifierait l'Image d'une annonce déjà existante.
  //  Ici je vais prendre une méthode de contrôleur arbitraire, mais vous savez tout ce qu'il faut pour l'implémenter réellement :
  public function editImageAction($advertId)
  {
    $em = $this->getDoctrine()->getManager();
	
	// On récupère l'annonce
	$advert = $em->getRepository('OCPlatformBundle:Advert')->find($advertId);
	
	// On modifie l'URL de l'image par exemple
	$advert->getImage()->setUrl('test.png');
	
  // On n'a pas besoin de persister l'annonce ni l'image.
  // Rappelez-vous, ces entités sont automatiquement persistées car
  // on les a récupérées depuis Doctrine lui-même
    
	//On déclenche la modification
	$em->flush();
	
	return new Response('OK');
  
  }
}