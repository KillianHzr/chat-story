<?php

namespace App\Controller;

use App\Entity\Choice;
use App\Entity\Story;
use App\Repository\ChoiceRepository;
use App\Repository\RoomRepository;
use App\Repository\StoryRepository;
use App\Service\ChatGptAI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/story')]
class StoryController extends AbstractController

{
    private $ChatGptAI;

    public function __construct(ChatGptAI $ChatGptAI)
    {
        $this->ChatGptAI = $ChatGptAI;
    }

    /**
     * @Route("/story/start", name="story_start")
     */

    #[Route('/story', name: 'story_start')]
    public function startStory(): JsonResponse
    {
        // Prompt pour proposer trois thèmes
        $prompt = "Propose trois thèmes intéressants pour une histoire interactive. Ces thèmes doivent être distincts et captivants.";

        $response = $this->ChatGptAI->requestChatGPT($prompt);

        // Dump and Die pour afficher la réponse JSON brute
        dd($response);


        return $this->json($response);
    }

    /**
     * @Route("/story/progress", name="story_progress")
     */
    public function progressStory(string $currentStory): JsonResponse
    {
        // Prompt pour générer la suite de l'histoire principale et proposer trois choix
        $prompt = "Voici l'histoire actuelle : \"$currentStory\". Propose la suite de cette histoire, suivie de trois choix possibles pour la faire progresser. Sépare bien la suite de l'histoire et les trois choix.";

        $response = $this->ChatGptAI->requestChatGPT($prompt);
        return $this->json($response);
    }

    /**
     * @Route("/story/summary", name="story_summary")
     */
    public function summarizeStory(string $currentStory): JsonResponse
    {
        // Prompt pour résumer l'histoire et éventuellement rajouter un élément pour relancer l'intrigue
        $prompt = "Résume cette histoire : \"$currentStory\". Ensuite, propose un nouvel élément pour relancer l'intrigue ou éviter que l'histoire tourne en rond.";

        $response = $this->ChatGptAI->requestChatGPT($prompt);
        return $this->json($response);
    }

    #[Route(name: 'app_send_vote', methods: ['GET', 'POST'])]
    public function sendVote(Request $request, ChoiceRepository $choiceRepository, RoomRepository $roomRepository, EntityManagerInterface $entityManager): Response
    {
        $responseData = json_decode($request->getContent(), true);
        $roomId = $responseData['roomId'];
        $choiceId = $responseData['choiceId'];

        $currentRoom = $roomRepository->find($roomId);

        $choice = $choiceRepository->find($choiceId);
        $choice->setVoteCount($choice->getVoteCount() + 1);
        $entityManager->persist($choice);
        $entityManager->flush();

        return new Response(status: Response::HTTP_OK, headers: ['Content-Type' => 'application/json']);
    }

    #[Route('/vote/update' ,name: 'app_update_vote', methods: ['GET', 'POST'])]
    public function updateVote(Request $request, ChatGptAI $chatGptAI, StoryRepository $storyRepository, RoomRepository $roomRepository, EntityManagerInterface $entityManager): Response
    {
        $responseData = json_decode($request->getContent(), true);
        $roomId = $responseData['roomId'];

        $currentRoom = $roomRepository->find($roomId);
        $currentRoom->setUpdatedAt(new \DateTime());

        $lastRoomId =  $roomRepository->getLastStory($currentRoom);
        $lastStory = $storyRepository->find($lastRoomId);

        $lastStoryChoices = $lastStory->getChoices();
        //get the choice with the highest vote count
        $maxVoteCount = 0;
        $maxVoteChoice = null;
        foreach ($lastStoryChoices as $choice) {
            if ($choice->getVoteCount() > $maxVoteCount) {
                $maxVoteCount = $choice->getVoteCount();
                $maxVoteChoice = $choice;
            }
        }

        if (!$maxVoteChoice) {
            $maxVoteChoice = $lastStoryChoices->get(rand(0, $lastStoryChoices->count() - 1));
        }

        $prompt = "Voici l'histoire actuelle :". $lastStory->getStoryText() . ". Propose la suite de cette histoire grâce au choix suivant : ".$maxVoteChoice->getChoiceText();

        $result = $chatGptAI->continue($prompt);

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
