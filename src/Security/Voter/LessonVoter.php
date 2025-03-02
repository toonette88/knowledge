<?php

namespace App\Security\Voter;

use App\Entity\Lesson;
use App\Repository\OrderRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LessonVoter extends Voter
{
    public const VIEW = 'view_lesson';  // Attribute to check

    private Security $security;
    private OrderRepository $orderRepository;

    public function __construct(Security $security, OrderRepository $orderRepository)
    {
        $this->security = $security;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Determines if the attribute and subject are supported.
     * 
     * @param string $attribute The attribute being checked (VIEW in this case).
     * @param mixed $subject The object being checked (should be an instance of Lesson).
     * 
     * @return bool Whether the voter supports the attribute and subject.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // Only support 'view_lesson' action and Lesson entity
        return $attribute === self::VIEW && $subject instanceof Lesson;
    }

    /**
     * Determines whether the user can perform the action on the given lesson.
     * 
     * @param string $attribute The action being checked (view).
     * @param mixed $lesson The lesson entity being checked.
     * @param TokenInterface $token The current authentication token (user).
     * 
     * @return bool Whether the user has access to the lesson.
     */
    protected function voteOnAttribute(string $attribute, mixed $lesson, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If there's no authenticated user, deny access
        if (!$user || !is_object($user)) {
            return false;
        }

        // Get the user's paid orders
        $orders = $this->orderRepository->findBy(['user' => $user, 'status' => 'Payée']);

        // Loop through the orders and check if the user has access to the lesson
        foreach ($orders as $order) {
            foreach ($order->getOrderDetails() as $orderDetail) {
                $orderLesson = $orderDetail->getLesson();
                $orderCourse = $orderDetail->getCourse();  // Get the course of the order detail

                // If the lesson is specifically purchased, grant access
                if ($orderLesson === $lesson) {
                    return true;
                }

                // If the course containing the lesson is purchased, grant access to all lessons in the course
                if ($orderCourse && $lesson->getCourse() && $lesson->getCourse() === $orderCourse) {
                    return true;
                }
            }
        }

        // Deny access if no match is found
        return false;
    }
}
