<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello/{prenom?World}", name="hello", methods="GET")
     */
    public function hello($prenom = "World")
    {
        return $this->render('hello.html.twig', ['prenom' => $prenom,
        ]);
    }
}
