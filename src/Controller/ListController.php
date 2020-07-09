<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/list", name="list_index")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!'
        ]);
    }

    /**
     * @Rest\Get("/list/update", name="list_update")
     */
    public function update()
    {
        return $this->json([
            'message' => 'Function update'
        ]);
    }
}
