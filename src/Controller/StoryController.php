<?php

namespace App\Controller;

use App\Service\ChatGptAI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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
}
