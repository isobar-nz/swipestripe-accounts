<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\Security\Security;

/**
 * Class AccountSettingsPageController
 * @package SwipeStripe\Accounts\Customer
 */
class AccountSettingsPageController extends \PageController
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'AccountSettingsForm',
    ];

    /**
     * @return AccountSettingsForm
     */
    public function AccountSettingsForm(): AccountSettingsForm
    {
        return AccountSettingsForm::create(Security::getCurrentUser(), $this, __FUNCTION__);
    }
}
