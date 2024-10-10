<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(Request $request): Response
    {
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
        ]);
    }

    #[Route('/publish', name: 'app_chat_publish')]
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'https://example.com/books/1',
            json_encode(['author' => 'Michel', 'message' => 'Yo la team'])
        );

        $hub->publish($update);

        return new Response('published!');
    }
}
