<?php

namespace App\Controller;

use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ListController extends AbstractFOSRestController
{
    private $taskListRepository;
    private $entityManager;

    function __construct(TaskListRepository $taskListRepository, EntityManagerInterface $entityManager)
    {
        $this->taskListRepository = $taskListRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Mostrar Toda el listado
     * @Rest\Get("/lists", name="get_lists")
     */
    public function getListsAction()
    {
        $lists = $this->taskListRepository->findAll();

        return $this->view($lists, Response::HTTP_OK);
    }


    /**
     * Mostrar un registro del listado
     * @Rest\Get("/lists/{id}", name="get_list")
     */
    public function getListAction($id)
    {
        $list = $this->taskListRepository->findOneBy(['id' => $id]);
        return $this->view($list, Response::HTTP_OK);
    }


    /**
     * Crear un nuevo registro
     * @Rest\Post("/lists", name="post_lists")
     * @Rest\RequestParam(name="title", description="title of the list", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function postListsAction(ParamFetcher $paramFetcher)
    {
        $title = $paramFetcher->get('title');

        if($title) {
            $list = new TaskList();
            $list->setTitle($title);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            return $this->view($list, Response::HTTP_CREATED);
        }

        return $this->view(['title' => 'Error en el proceso'], Response::HTTP_BAD_REQUEST);
    }


    /**
     * Ver las tareas del registro
     * @Rest\Get("/api/lists/{id}/tasks", name="get_lists_tasks")
     */
    public function getListsTasksAction($id)
    {

    }


    /**
     * Actualizar el registro (por put)
     * @Rest\Put("/api/lists", name="put_lists")
     */
    public function putListsAction()
    {

    }


    /**
     * Actualizar un registro (por patch)
     * @Rest\Patch("/api/lists/{id}/background", name="background_lists")
     */
    public function patchListsAction($id)
    {

    }
}
