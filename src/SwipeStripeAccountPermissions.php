<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts;

use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

/**
 * Class SwipeStripeAccountPermissions
 * @package SwipeStripe\Accounts
 */
class SwipeStripeAccountPermissions implements PermissionProvider
{
    const VIEW_CUSTOMERS = self::class . '.VIEW_CUSTOMERS';
    const EDIT_CUSTOMERS = self::class . '.MANAGE_CUSTOMERS';

    /**
     * @inheritDoc
     */
    public function providePermissions(): array
    {
        $permissionCategory = _t(Permission::class . '.SWIPESTRIPE_ACCOUNTS_CATEGORY', 'SwipeStripe Accounts');

        return [
            self::VIEW_CUSTOMERS => [
                'name'     => _t(self::VIEW_CUSTOMERS, "View Customers' Details"),
                'category' => $permissionCategory,
                'help'     => _t(self::VIEW_CUSTOMERS . '_HELP', "View customers' details in the CMS."),
            ],
            self::EDIT_CUSTOMERS => [
                'name'     => _t(self::EDIT_CUSTOMERS, 'Edit Customers'),
                'category' => $permissionCategory,
                'help'     => _t(self::EDIT_CUSTOMERS . '_HELP', 'Edit customers in the CMS.'),
            ],
        ];
    }
}
