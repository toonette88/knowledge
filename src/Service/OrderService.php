<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\User;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createOrder(User $user, array $items): Order
    {
        $order = new Order();
        $order->setUser($user);
        $order->setStatus(OrderStatus::PENDING);
        $total = 0;

        foreach ($items as $item) {
            $orderDetail = new OrderDetail();
            $orderDetail->setOrder($order);

            if ($item instanceof Course) {
                $orderDetail->setCourse($item);
                $orderDetail->setUnitPrice($item->getPrice());
            } elseif ($item instanceof Lesson) {
                $orderDetail->setLesson($item);
                $orderDetail->setUnitPrice($item->getPrice());
            }

            $total += $orderDetail->getUnitPrice();
            $order->addOrderDetail($orderDetail);
        }

        $order->setTotal($total);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}
