<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat', methods: ['GET', 'POST'])]
    public function index(Request $request, HubInterface $hub, UserRepository $userRepository): Response
    {
        if($request->isMethod('POST')) {

            $requestData = json_decode($request->getContent(), true);
            $message = $requestData['message'];
            $author = $requestData['author'] ?? 'anonymous';

            if (empty($message)) {
                return new Response('Message cannot be empty', Response::HTTP_BAD_REQUEST);
            }
            $update = new Update(
                'https://example.com/books/1',
                json_encode(['author' => $author, 'message' => $message])
            );
            $hub->publish($update);

            return new Response('published!');
        }
        $message = new Message();

        $user = $this->getUser();
        $username = $user->getUsername();

        return $this->render('chat/index.html.twig', [
            'username' => $username,
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
