<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Security\Member;
use SwipeStripe\Accounts\AccountCreationEmail;
use SwipeStripe\Order\Order;
use SwipeStripe\ORM\FieldType\DBAddress;

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
}
