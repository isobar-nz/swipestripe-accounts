<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\Security\InheritedPermissions;
use SwipeStripe\RequiredSinglePage;

/**
 * Class MyOrdersPage
 * @package SwipeStripe\Accounts\Customer
 */
class MyOrdersPage extends \Page
{
    use RequiredSinglePage;

    /**
     * @see $CanViewType
     * @var array
     */
    private static $defaults = [
        'CanViewType' => InheritedPermissions::LOGGED_IN_USERS,
    ];
}
