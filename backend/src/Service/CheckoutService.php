<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

/**
 * CheckoutService
 * Maneja el proceso de checkout y pago (simulado)
 */
class CheckoutService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Procesar el checkout de un pedido
     *
     * @param Order $order Pedido para procesar
     * @return Order Pedido actualizado con el pago
     * @throws \Exception si el pedido no puede procesarse
     */
    public function processCheckout(Order $order): Order
    {
        // Validar que el pedido pueda procesarse
        if (!$order->canBeCheckedOut()) {
            throw new \Exception('El pedido no puede procesarse. Verifique el estado del pedido y los ítems.');
        }

        // Create simulated payment
        $payment = $this->simulatePayment($order);
        $order->setPayment($payment);

        // Update order status
        $order->setStatus(Order::STATUS_PROCESSING);

        // If payment is successful, mark order as completed
        if ($payment->getStatus() === Payment::STATUS_COMPLETED) {
            $order->setStatus(Order::STATUS_COMPLETED);
        }

        $this->entityManager->flush();

        return $order;
    }

    /**
     * Simular el procesamiento del pago
     * Para el MVP, esto siempre tiene éxito
     */
    private function simulatePayment(Order $order): Payment
    {
        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setAmount($order->getTotal());
        $payment->setMethod(Payment::METHOD_SIMULATED);
        
        // Simulate successful payment
        $payment->markAsCompleted();

        $this->entityManager->persist($payment);

        return $payment;
    }
}
