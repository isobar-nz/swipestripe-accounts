<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Order;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Order\Order;
use SwipeStripe\Order\Status\OrderStatusUpdate;

/**
 * Class OrderStatusUpdateExtension
 * @package SwipeStripe\Accounts\Order
 * @property OrderStatusUpdate|OrderStatusUpdateExtension $owner
 */
class OrderStatusUpdateExtension extends DataExtension
{
    /**
     * @param bool $shouldSend
     */
    public function shouldSendNotification(bool &$shouldSend): void
    {
        /** @var Order|OrderExtension $order */
        $order = $this->owner->Order();
        /** @var Member|MemberExtension $member */
        $member = $order->Member();

        $shouldSend = $shouldSend && (!$member->exists() || $member->OrderStatusNotifications);
    }
}
