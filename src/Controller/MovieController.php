<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest; 

use Symfony\Component\HttpFoundation\Request;

use App\Form\MovieType;
use App\Entity\Movie;

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
     * List all Movies
     * @Rest\Get("/movies/{id}")
     * @return Response
     */
    public function getMovieByIdAction($id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
         
        return $movie ? $this->handleView($this->view($movie)) : $this->handleView($this->view(['error' => 'movie not found']));
    }

    /**
     * List all Movies
     * @Rest\Put("/movies/edit/{id}")
     * @return Response
     */
    public function editMovie($id, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        $data=json_decode($request->getContent(),true);
        $movie->setTitle($data['title']);
        $entityManager->flush();
         
        return $movie ? $this->handleView($this->view(['status'=>'ok'],Response::HTTP_CREATED)) : $this->handleView($this->view(['error' => 'movie not found']));
    }

    /**
     * List all Movies
     * @Rest\Delete("/movies/delete/{id}")
     * @return Response
     */
    public function removeMovie($id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $repository->find($id);
        
        if($movie) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($movie);
            $entityManager->flush();
        }


        return $movie ? $this->handleView($this->view(['status'=>'ok'],Response::HTTP_GONE)) : $this->handleView($this->view(['error' => 'movie not found']));
    }


    /**
     * Create Movie
     * @Rest\Post("/movies")
     * @return Response
     */
    public function postMovieAction(Request $request)
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
