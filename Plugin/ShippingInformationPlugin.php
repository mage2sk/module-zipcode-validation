<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Panth\ZipcodeValidation\Model\OrderAddressValidator;

class ShippingInformationPlugin
{
    private OrderAddressValidator $orderAddressValidator;

    public function __construct(OrderAddressValidator $orderAddressValidator)
    {
        $this->orderAddressValidator = $orderAddressValidator;
    }

    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $this->orderAddressValidator->assertValid($addressInformation->getShippingAddress());
        return null;
    }
}
