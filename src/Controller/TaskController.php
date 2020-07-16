<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property TaskRepository taskRepository
 * @Route("/api")
 */
class TaskController extends AbstractFOSRestController
{
    private $taskRepository;
    private $entityManager;

    function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager)
    {

        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * remover tareas al registro
     * @Rest\Delete("/tasks/{id}/remove", name="delete_task")
     * @param Task $task
     * @return \FOS\RestBundle\View\View
     */
    public function deleteTaskAction(Task $task) {
        if(!is_null($task)) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return $this->view(['message' => 'Tarea borrada con éxito'], Response::HTTP_OK);
        }

        return $this->view(['message' => 'Algo salió mal en la carga de tareas'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Status
     * @Rest\Get("/tasks/{id}/status", name="status_task")
     * @param Task $task
     * @return \FOS\RestBundle\View\View
     */
    public function statusTaskAction(Task $task) {

    }
}
