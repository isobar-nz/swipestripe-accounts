<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\Core\Injector\Injector;
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
     * @return AccountSettingsFormInterface
     */
    public function AccountSettingsForm(): AccountSettingsFormInterface
    {
        return Injector::inst()->create(AccountSettingsFormInterface::class, Security::getCurrentUser(), $this, __FUNCTION__);
    }
}
