<?php

namespace App\Controller;

use App\Service\ChatGptAI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ChatGptAI $chatGptAI): Response
    {
       $result =  $chatGptAI->start();
       dump($result);
        $result = $chatGptAI->continue($result['choices'][0] );
        dump($result);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',

        ]);
    }





}
