<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Location;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends AbstractController
{
    /**
     * @Route("/location/new", name="newlocation")
     */
    public function index(Request $request)
    {
    	$location = new Location();

    	$form = $this->createFormBuilder($location)
            ->add('number', TextType::class, ['label' => 'Numer'])
            ->add('name', TextType::class, ['label' => 'Nazwa'])
            ->add('description', TextareaType::class, ['label' => 'Opis'])
            ->add('submit', SubmitType::class, ['label' => 'Zapisz i przejdż do plików'])
            ->getForm();

        $form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()) {
	        $location = $form->getData();
	        $location->setTime(new \DateTime('now', new \DateTimeZone('+0200')));
	        $entityManager = $this->getDoctrine()->getManager();
	        $entityManager->persist($location);
	        $entityManager->flush();

	        return $this->redirectToRoute('testiupload', ['locationID' => $location->getId()]);
	    }

	    $locations = $this->getDoctrine()
            ->getRepository(Location::class)
            ->findAll();

        return $this->render('location/index.html.twig', [
            'form' => $form->createView(),
            'locations' => $locations,
        ]);
    }
}
