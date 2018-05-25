<?php
namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class MovieController extends ApiController
{
    /**
    * @Route("/movies")
    * @Method("GET")
    */
    public function index(MovieRepository $movieRepository)
    {
        $movies = $movieRepository->transformAll();

        return $this->respond($movies);
    }

    /**
     * @Route("/movies/{id}")
     * @Method("GET")
     */
    public function show($id, MovieRepository $movieRepository)
    {
        $movie = $movieRepository->find($id);

        if (! $movie) {
            return $this->respondNotFound();
        }

        $movie = $movieRepository->transform($movie);

        return $this->respond($movie);
    }

    /**
    * @Route("/movies")
    * @Method("POST")
    */
    public function create(Request $request, EntityManagerInterface $em)
    {
        // validate the title
        if (! $request->query->get('title')) {
            return $this->respondValidationError('Please provide a title!');
        }

        // persist the new movie
        $movie = new Movie;
        $movie->setTitle($request->query->get('title'));
        $movie->setCount(0);
        $em->persist($movie);
        $em->flush();

        return $this->respondCreated();
    }

    /**
    * @Route("/movies/{id}")
    * @Method("PUT")
    */
    public function update($id, Request $request, EntityManagerInterface $em, MovieRepository $movieRepository)
    {

        $errors = [];

        // validate the title
        if (! $request->query->get('title')) {
            $errors[] = 'Please provide a title!';
        }

        // validate the count
        if (! $request->query->get('count')) {
            $errors[] = 'Please provide a count!';
        }

        if (count($errors)) {
            return $this->respondValidationError($errors);
        }

        $movie = $movieRepository->find($id);

        if (! $movie) {
            return $this->respondNotFound();
        }

        $movie->setTitle($request->query->get('title'));
        $movie->setCount((int) $request->query->get('count'));
        $em->persist($movie);
        $em->flush();

        return $this->respond([]);
    }

}