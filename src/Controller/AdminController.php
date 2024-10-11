<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(UserRepository $userRepository, RoomRepository $roomRepository): Response
    {
        $userCount = $userRepository->count([]);
        $roomCount = $roomRepository->count([]);

        return $this->render('admin/dashboard/index.html.twig', [
            'dashboard_data' => [
                'user_count' => $userCount,
                'room_count' => $roomCount,
                'revenue_today' => 1500,
            ],
        ]);
    }

}
