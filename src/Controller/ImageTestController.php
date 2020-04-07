<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\ImageFileType;
use App\Entity\File;
use App\Entity\Location;
use Symfony\Component\HttpFoundation\Request;
use Intervention\Image\ImageManager;

class ImageTestController extends AbstractController
{
	/**
     * @Route("/test", name="testhomepage")
     */
    public function index(Request $request)
    {
    	if (!isset($params)) $params = array();

    	return $this->render('test/index.html.twig', [
            'params' => $params,
        ]);
    }

    /**
     * @Route("/test/image/upload/{locationID}", name="testiupload")
     */
    public function iupload($locationID = false, Request $request)
    {
    	// formularz
    	$file = new File();
    	$form = $this->createForm(ImageFileType::class, $file);

    	$form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()) {
	        $file = $form->getData();
	        $file->setTime(new \DateTime('now', new \DateTimeZone('+0200')));
            if ($locationID) {
                $location = $this->getDoctrine()
                    ->getRepository(Location::class)
                    ->find($locationID);
                $file->setLocation($location);
            }
	        $imageFile = $form->get('name')->getData();
	        if ($imageFile) {
	        	$originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME).'.'.$imageFile->guessExtension();
	        	$imageFile->move(
                    $this->getParameter('upload_image_directory'),
                    $originalFilename
                );
                $newFilePath = $this->getParameter('upload_image_directory').'/'.$originalFilename;
                if (file_exists($newFilePath)) {
		        	$manager = new ImageManager(array('driver' => 'gd'));
		        	$image = $manager->make($newFilePath)
		        		->resize(1000, null, function ($constraint) {
								    $constraint->aspectRatio();
								    $constraint->upsize();
								});
		        }
                $file->setName($originalFilename);
	        }
	        $entityManager = $this->getDoctrine()->getManager();
	        $entityManager->persist($file);
	        $entityManager->flush();
            if ($locationID) {
                return $this->redirectToRoute('testiupload', ['locationID' => $locationID]);
            } else {
	           return $this->redirectToRoute('testiupload');
            }
	    }

	    // odczyt plikow po uploadzie
        if ($locationID) {
            $location = $this->getDoctrine()
                    ->getRepository(Location::class)
                    ->find($locationID);
            $files = $location->getFiles();
        } else {
            $files = $this->getDoctrine()
                    ->getRepository(File::class)
                    ->findAll();
        }

        $uploadDirectory = explode('public', $this->getParameter('upload_image_directory'))[1];
	    

        return $this->render('test/iupload.html.twig', [
            'form' => $form->createView(),
            'file' => $file,
            'files' => $files,
            'upload_directory' => $uploadDirectory,
        ]);
    }

    /**
     * @Route("/test/image/delete/{id}", name="deletefile")
     */
    public function delete(int $id)
    {
    	$params = array();

    	$file = $this->getDoctrine()
        	->getRepository(File::class)
        	->find($id);

        if ($file) {
        	$filePath = $this->getParameter('upload_image_directory').'/'.$file->getName();
        	if (unlink($filePath)) {
        		$entityManager = $this->getDoctrine()->getManager();
		        $entityManager->remove($file);
		        $entityManager->flush();
		        return $this->redirectToRoute('testiupload');
        	}
        }
        return $this->render('test/index.html.twig', [
            'params' => $params,
        ]);
    }	

}
