<?php

namespace App\Controller;

use App\Entity\Choice;
use App\Entity\Room;
use App\Entity\Story;
use App\Form\RoomType;
use App\Repository\ChoiceRepository;
use App\Repository\RoomRepository;
use App\Service\ChatGptAI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/room')]
final class RoomController extends AbstractController
{
    #[Route('/{id}', name: 'chat_room_show', methods: ['GET'])]
    public function show(Room $room, RoomRepository $roomRepository): Response
    {
        $stories = $room->getStories()->toArray();
        $storiesData = [];
        foreach ($stories as $story) {
            $choicesArray = $story->getChoices()->toArray();
            $choicesData = [];
            foreach ($choicesArray as $choice) {
                $choicesData[] = [
                    'id' => $choice->getId(),
                    'choiceText' => $choice->getChoiceText(),
                ];
            }
            $storiesData[] = [
                'story' => $story->getStoryText(),
                'choices' => $choicesData,
            ];
        }

        return $this->render('room/chat.html.twig', [
            'room' => $room,
            'stories' => $storiesData,
        ]);
    }

    #[Route(name: 'app_init_story', methods: ['GET', 'POST'])]
    public function initStory(Request $request, HubInterface $hub, ChatGptAI $chatGptAI, RoomRepository $roomRepository, EntityManagerInterface $entityManager): JsonResponse
    {

        $responseData = json_decode($request->getContent(), true);
        $roomId = $responseData['roomId'];
        $result = $chatGptAI->start();

        $currentRoom = $roomRepository->find($roomId);
        if (!$currentRoom) {
            throw $this->createNotFoundException('La room demandée n\'existe pas.');
        }
        $currentRoom->setUpdatedAt(new \DateTime());

        $story = new Story();
        $story->setStoryText($result['story']);
        $story->setCreatedAt(new \DateTime());
        $story->setUpdatedAt(new \DateTime());

        $choices = $result['choices'];
        foreach ($choices as $choice) {
            $newChoice = new Choice();
            $newChoice->setChoiceText($choice);
            $newChoice->setVoteCount(0);
            $newChoice->setSelected(false);
            $story->addChoice($newChoice);
            $entityManager->persist($newChoice);
        }

        $entityManager->persist($story);
        $currentRoom->addStory($story);

        $entityManager->persist($currentRoom);

        $entityManager->flush();

        $choicesArray = $story->getChoices()->toArray();
        $choicesData = [];
        foreach ($choicesArray as $choice) {
            $choicesData[] = [
                'id' => $choice->getId(),
                'choiceText' => $choice->getChoiceText(),
            ];
        }

        return new JsonResponse(data: ['story' => $story->getStoryText(), 'choices' => $choicesData], status: Response::HTTP_OK, headers: ['Content-Type' => 'application/json']);
    }
}
