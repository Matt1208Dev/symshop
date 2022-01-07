<?php

namespace App\Controller;

use App\Taxes\Detector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        dump('Page de test !!!');
        die();
    }

    /**
     * @Route("/test/{age?0}", name="test", methods={"GET", "POST"}, host="localhost", schemes={"http", "https"})
     */
    public function test(Request $request, $age, Detector $detect)
    {
        dump($detect->detect(99));
        dump($detect->detect(101));

        return new Response("Vous avez $age ans !");
    }
}
