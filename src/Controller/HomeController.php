<?php
// src/Controller/HomeController.php

namespace App\Controller;

use App\Repository\ToolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function index(ToolRepository $toolRepository): Response
    {
        $tools = $toolRepository->findAll();

        //-- le fichier sera donc dans templates/home/index.html.twig
        return $this->render('home/index.html.twig', [
            'tools' => $tools,
        ]);
    }
}
