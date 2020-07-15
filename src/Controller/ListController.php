<?php

namespace App\Controller;

use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
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
     * @param TaskList $list
     * @return \FOS\RestBundle\View\View
     */
    public function getListAction(TaskList $list)
    {
        //$list = $this->taskListRepository->findOneBy(['id' => $id]);
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
        // TODO: Parametros provenientes de la petición
        $title = $paramFetcher->get('title');

        // TODO: Se comprueba si no es null y se procede con la inserción
        if (!is_null($title)) {
            $list = new TaskList();
            $list->setTitle($title);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            return $this->view($list, Response::HTTP_CREATED);
        }

        // TODO: En caso de fallar, devuelve un mensaje
        return $this->view(['message' => 'Error en el proceso de inserción'], Response::HTTP_BAD_REQUEST);
    }


    /**
     * Ver las tareas del registro
     * @Rest\Get("/lists/{id}/tasks", name="get_lists_tasks")
     * @param TaskList $list
     * @return \FOS\RestBundle\View\View
     */
    public function getListsTasksAction(TaskList $list)
    {
        return $this->view($list->getTasks(), response::HTTP_OK);
    }


    /**
     * Actualizar todos los campos del registro
     * @Rest\Patch("/lists/{id}/title", name="patch_lists_title")
     * @Rest\RequestParam(name="title", description="title update", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @param TaskList $list
     * @return \FOS\RestBundle\View\View
     */
    public function patchTitleListsAction(ParamFetcher $paramFetcher, TaskList $list)
    {
        // TODO: Parametros provenientes de la petición
        $title = $paramFetcher->get('title');

        // TODO: Se comprueba si no es null y se procede con la actialización
        if(!is_null($title)) {
            $list->setTitle($title);
            $this->entityManager->flush();

            return $this->view($list, Response::HTTP_CREATED);
        }

        // TODO: En caso de fallar, devuelve un mensaje
        return $this->view(['message' => 'Error en el proceso de actualizado'], Response::HTTP_BAD_REQUEST);
    }


    /**
     * Actualizar un registro (por patch)
     * @Rest\Patch("/lists/{id}/background", name="patch_lists_background")
     * @Rest\FileParam(name="image", description="La imagen de la lista", nullable=false, image=true)
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @param TaskList $list
     * @return \FOS\RestBundle\View\View
     */
    public function patchBackgroundListsAction(Request $request, ParamFetcher $paramFetcher, TaskList $list)
    {
        // TODO: Se obtiene la imagen actual
        $backgrundactual = $list->getBackground();

        //TODO: Si contiene un elemento se remueve
        if (!is_null($backgrundactual)) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->getUploadsDir() . $backgrundactual);
        }

        // TODO: Se obtiene el nuevo elemento
        /** @var UploadedFile $file */
        $file = $paramFetcher->get('image');

        // TODO: Si recibe correctamente
        if ($file) {
            // TODO: se obtiene el nombre del archivo
            $filename = md5(uniqid()) . '.' . $file->guessClientExtension();
            // TODO: se mueve a la carpeta asignada
            $file->move($this->getUploadsDir(), $filename);

            // TODO: Se actualiza unicamente los campos backgroud y backgroundPath de la entidad
            $list->setBackground($filename);
            $list->setBackgroundPath('/image/uploads/' . $filename);
            $this->entityManager->persist($list);
            $this->entityManager->flush();

            // TODO: Retorna la url perteneciente al background actualizado
            $data = $request->getUriForPath($list->getBackgroundPath());
            Return $this->view($data, Response::HTTP_OK);
        }

        Return $this->view(['message' => 'Algo salió mal con la actualización del background'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Función que retorna la ruta donde se alojan las imagenes
     * que se suben al servidor
     */
    public function getUploadsDir()
    {
        return $this->getParameter('uploads_dir');
    }


    /**
     * @Rest\Delete("/lists/{id}", name="delete_lists")
     * @param TaskList $list
     * @return \FOS\RestBundle\View\View
     */
    public function deleteListAction(TaskList $list)
    {
        if(!is_null($list)) {
            $this->entityManager->remove($list);
            $this->entityManager->flush();

            return $this->view(['message' => 'El registro se borro exitósamente.'], response::HTTP_OK);
        }

        return $this->view(['message' => 'Algo salió mal al intetar borrar un registro'], response::HTTP_BAD_REQUEST);
    }
}
