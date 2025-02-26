<?php

namespace App\Security\Voter;

use App\Entity\Lesson;
use App\Repository\OrderRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LessonVoter extends Voter
{
    public const VIEW = 'view_lesson'; 

    private Security $security;
    private OrderRepository $orderRepository;

    public function __construct(Security $security, OrderRepository $orderRepository)
    {
        $this->security = $security;
        $this->orderRepository = $orderRepository;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof Lesson;
    }

    protected function voteOnAttribute(string $attribute, mixed $lesson, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user || !is_object($user)) {
            return false;
        }

        // On récupère les commandes payées de l'utilisateur
        $orders = $this->orderRepository->findBy(['user' => $user, 'status' => 'Payée']);

        foreach ($orders as $order) {
            foreach ($order->getOrderDetails() as $orderDetail) {
                $orderLesson = $orderDetail->getLesson();
                $orderCourse = $orderDetail->getCourse();  // On vérifie aussi le cursus du détail de la commande

                // Si la leçon est spécifique et correspond à celle demandée
                if ($orderLesson === $lesson) {
                    return true;
                }

                // Si le cursus de la leçon est acheté, donner accès à toutes les leçons de ce cursus
                if ($orderCourse && $lesson->getCourse() && $lesson->getCourse() === $orderCourse) {
                    return true;
                }
            }
        }

        return false;
    }
}
