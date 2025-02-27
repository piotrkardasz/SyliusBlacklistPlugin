<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusBlacklistPlugin\Validator\Constraints\Checkout;

use BitBag\SyliusBlacklistPlugin\Converter\FraudSuspicionCommonModelConverterInterface;
use BitBag\SyliusBlacklistPlugin\Entity\Customer\FraudStatusInterface;
use BitBag\SyliusBlacklistPlugin\Entity\FraudPrevention\FraudSuspicionInterface;
use BitBag\SyliusBlacklistPlugin\Processor\AutomaticBlacklistingRulesProcessorInterface;
use BitBag\SyliusBlacklistPlugin\Resolver\SuspiciousOrderResolverInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Sylius\Component\Customer\Model\CustomerInterface;
use Webmozart\Assert\Assert;

class CheckoutAddressTypeValidator extends ConstraintValidator
{
    /** @var SuspiciousOrderResolverInterface */
    private $suspiciousOrderResolver;

    /** @var AutomaticBlacklistingRulesProcessorInterface */
    private $automaticBlacklistingRulesProcessor;

    /** @var FraudSuspicionCommonModelConverterInterface */
    private $fraudSuspicionCommonModelConverter;

    public function __construct(
        SuspiciousOrderResolverInterface $suspiciousOrderResolver,
        AutomaticBlacklistingRulesProcessorInterface $automaticBlacklistingRulesProcessor,
        FraudSuspicionCommonModelConverterInterface $fraudSuspicionCommonModelConverter
    ) {
        $this->suspiciousOrderResolver = $suspiciousOrderResolver;
        $this->automaticBlacklistingRulesProcessor = $automaticBlacklistingRulesProcessor;
        $this->fraudSuspicionCommonModelConverter = $fraudSuspicionCommonModelConverter;
    }

    public function validate($order, Constraint $constraint): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        if ($customer->getFraudStatus() === FraudStatusInterface::FRAUD_STATUS_WHITELISTED) {
            return;
        }

        if ($customer->getFraudStatus() === FraudStatusInterface::FRAUD_STATUS_BLACKLISTED) {
            $this->buildViolation($constraint);
            return;
        }

        if ($this->automaticBlacklistingRulesProcessor->process($order)) {
            $this->buildViolation($constraint);
            return;
        }

        $fraudSuspicionCommonModelWithBillingAddressType = $this->fraudSuspicionCommonModelConverter->convertOrderObject($order, FraudSuspicionInterface::BILLING_ADDRESS_TYPE);
        $fraudSuspicionCommonModelWithShippingAddressType = $this->fraudSuspicionCommonModelConverter->convertOrderObject($order, FraudSuspicionInterface::SHIPPING_ADDRESS_TYPE);

        if (
            $this->suspiciousOrderResolver->resolve($fraudSuspicionCommonModelWithBillingAddressType) ||
            $this->suspiciousOrderResolver->resolve($fraudSuspicionCommonModelWithShippingAddressType)
        ) {
            $this->buildViolation($constraint);
            return;
        }
    }

    private function buildViolation(Constraint $constraint): void
    {
        $this->context->buildViolation($constraint->message)->setTranslationDomain('validators')->addViolation();
    }
}
