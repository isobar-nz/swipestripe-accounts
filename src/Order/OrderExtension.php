<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Order;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Order\Order;

/**
 * Class OrderExtension
 * @package SwipeStripe\Accounts\Order
 * @property Order|OrderExtension $owner
 * @property int $MemberID
 * @method Member Member()
 */
class OrderExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $has_one = [
        'Member' => Member::class,
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Member.Email',
    ];

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

    /**
     * @param null|Member $member
     * @return bool|null
     */
    public function canViewOrderPage(?Member $member = null): ?bool
    {
        $member = $member ?? Security::getCurrentUser();

        if ($this->owner->MemberID) {
            if ($member !== null && $member->ID === $this->owner->MemberID) {
                // Allow member to view their own order
                return true;
            }

            if ($member === null || !Permission::checkMember($member, 'ADMIN')) {
                // Guests and non-admins don't have permission to view another account's orders
                return false;
            }
        }

        return null;
    }
}
