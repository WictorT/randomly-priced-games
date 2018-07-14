<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/test")
     */
    public function number()
    {
        return new Response('Hello');
    }
}
