<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Order;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Order\Order;

/**
 * Class OrderExtension
 * @package SwipeStripe\Accounts\Order
 * @property Order|OrderExtension $owner
 */
class OrderExtension extends DataExtension
{
    /**
     *
     */
    public function populateDefaults()
    {
        $this->populateCustomerDefaults(Security::getCurrentUser());
    }

    /**
     * @param null|Member|MemberExtension $member
     */
    public function populateCustomerDefaults(?Member $member = null): void
    {
        if ($member === null || $this->owner->MemberID === $member->ID) {
            return;
        }

        $this->owner->MemberID = $member->ID;
        $this->owner->CustomerName = $member->getName();
        $this->owner->CustomerEmail = $member->Email;
        $this->owner->BillingAddress->copyFrom($member->DefaultBillingAddress);
    }
}
