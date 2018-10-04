<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\Security\InheritedPermissions;
use SwipeStripe\RequiredSinglePage;

/**
 * Class AccountSettingsPage
 * @package SwipeStripe\Accounts\Customer
 */
class AccountSettingsPage extends \Page
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
