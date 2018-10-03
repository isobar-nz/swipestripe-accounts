<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Order;

use SilverStripe\ORM\DataExtension;
use SwipeStripe\Order\Order;
use SwipeStripe\Order\OrderConfirmationPage;
use SwipeStripe\Order\ViewOrderPage;

/**
 * Class ViewOrderPageExtension
 * @package SwipeStripe\Accounts\Order
 * @property ViewOrderPage|ViewOrderPageExtension $owner
 */
class ViewOrderPageExtension extends DataExtension
{
    /**
     * @param Order|OrderExtension $order
     * @param string $link
     */
    public function updateLinkForOrder(Order $order, string &$link): void
    {
        if ($order->MemberID && !$this->owner instanceof OrderConfirmationPage) {
            // Remove guest token for account order
            $link = $this->owner->Link($order->ID);
        }
    }
}
