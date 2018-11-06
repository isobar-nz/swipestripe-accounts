<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SwipeStripe\Accounts\AccountCreationEmail;
use SwipeStripe\Accounts\SwipeStripeAccountPermissions;
use SwipeStripe\Address\DBAddress;
use SwipeStripe\Order\Order;

/**
 * Class MemberExtension
 * @package SwipeStripe\Accounts
 * @property Member|MemberExtension $owner
 * @property DBAddress $DefaultBillingAddress
 * @property bool $OrderStatusNotifications
 * @method HasManyList|Order[] Orders()
 */
class MemberExtension extends DataExtension
{
    const CUSTOMERS_GROUP = 'swipestripe-customers';
    const CUSTOMERS_GROUP_TITLE = 'SwipeStripe Customers';

    /**
     * @var array
     */
    private static $db = [
        'DefaultBillingAddress'    => 'Address',
        'OrderStatusNotifications' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'Orders' => Order::class,
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'OrderStatusNotifications' => true,
    ];

    /**
     *
     */
    public function onAfterWrite()
    {
        // Send email on account creation by SwipeStripe
        if ($this->owner->isChanged('ID', Member::CHANGE_VALUE) &&
            $this->owner->getField(AccountCreationEmail::SEND_EMAIL_FLAG)) {

            $this->owner->setField(AccountCreationEmail::SEND_EMAIL_FLAG, false);
            AccountCreationEmail::create($this->owner)->send();
        }
    }

    /**
     * @return Member|MemberExtension
     */
    public function addToCustomersGroup(): Member
    {
        $this->owner->addToGroupByCode(static::CUSTOMERS_GROUP, static::CUSTOMERS_GROUP_TITLE);
        return $this->owner;
    }

    /**
     * @param Member $member
     * @return bool|null
     */
    public function canView(?Member $member): ?bool
    {
        if (Permission::checkMember($member, SwipeStripeAccountPermissions::VIEW_CUSTOMERS) &&
            $this->owner->inGroup(static::CUSTOMERS_GROUP)) {

            return true;
        }

        return null;
    }

    /**
     * @param null|Member $member
     * @return bool|null
     */
    public function canEdit($member): ?bool
    {
        if (Permission::checkMember($member, SwipeStripeAccountPermissions::EDIT_CUSTOMERS) &&
            $this->owner->inGroup(static::CUSTOMERS_GROUP)) {

            return true;
        }

        return null;
    }
}
