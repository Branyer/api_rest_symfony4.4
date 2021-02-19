<?php

namespace App\Controller;

use App\Entity\Movie;
use FOS\RestBundle\Controller\AbstractFOSRestController;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest; 

use Symfony\Component\HttpFoundation\Request;

use App\Form\MovieType;

/**
* Movie controller
* @Route("/api", name="api_")
*/



class MovieController extends AbstractFOSRestController
{
    /**
     * List all Movies
     * @Rest\Get("/movies")
     * @return Response
     */
    public function getMovieAction(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movies = $repository->findAll();
        return $this->handleView($this->view($movies));
    }

    /**
     * Create Movie
     * @Rest\Post("/movies")
     * @return Response
     */
    public function postMovieAction(Request$request)
    {
        $movie= new Movie();
        $form=$this->createForm(MovieType::class,$movie);
        $data=json_decode($request->getContent(),true);
        $form->submit($data);

        if($form->isSubmitted()&&$form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($movie);
            $em->flush();
            return $this->handleView($this->view(['status'=>'ok'],Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }
}