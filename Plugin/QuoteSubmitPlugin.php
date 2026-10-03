<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Plugin;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Panth\ZipcodeValidation\Model\OrderAddressValidator;

class QuoteSubmitPlugin
{
    private OrderAddressValidator $orderAddressValidator;

    public function __construct(OrderAddressValidator $orderAddressValidator)
    {
        $this->orderAddressValidator = $orderAddressValidator;
    }

    public function beforeSubmit(QuoteManagement $subject, Quote $quote, $orderData = [])
    {
        if (!$quote->isVirtual()) {
            $this->orderAddressValidator->assertValid($quote->getShippingAddress(), (int) $quote->getStoreId());
        }
        return null;
    }
}
