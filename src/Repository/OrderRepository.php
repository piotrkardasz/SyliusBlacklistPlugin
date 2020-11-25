<?php

declare(strict_types=1);

namespace BitBag\SyliusBlacklistPlugin\Repository;

use Tests\BitBag\SyliusBlacklistPlugin\Entity\CustomerInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;

final class OrderRepository extends BaseOrderRepository implements OrderRepositoryInterface
{
    public function findByCustomerPaymentFailuresInCurrentDay(CustomerInterface $customer): string
    {
        return $this->createQueryBuilder('o')
            ->select(['COUNT(o.id)'])
            ->innerJoin('o.customer', 'customer')
            ->where('customer.id = :customerId')
            ->andWhere('o.paymentState = :failedState')
            ->andWhere('o.createdAt >= :yesterdayDate')
            ->setParameters([
                'customerId' => $customer->getId(),
                'failedState' => 'failed',
                'yesterdayDate' => (new \DateTime())->modify('- 1 day')
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByCustomerOrdersInCurrentWeek(CustomerInterface $customer): string
    {
        return $this->createQueryBuilder('o')
            ->select(['COUNT(o.id)'])
            ->innerJoin('o.customer', 'customer')
            ->where('customer.id = :customerId')
            ->andWhere('o.paymentState = :failedState')
            ->andWhere('o.createdAt >= :oneWeekBeforeDate')
            ->setParameters([
                'customerId' => $customer->getId(),
                'failedState' => 'failed',
                'oneWeekBeforeDate' => (new \DateTime())->modify('- 1 week')
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }
}
