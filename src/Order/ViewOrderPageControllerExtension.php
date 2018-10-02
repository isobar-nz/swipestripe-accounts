<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Order;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SwipeStripe\Order\Order;
use SwipeStripe\Order\ViewOrderPageController;

/**
 * Class ViewOrderPageControllerExtension
 * @package SwipeStripe\Accounts\Order
 * @property ViewOrderPageController|ViewOrderPageControllerExtension $owner
 */
class ViewOrderPageControllerExtension extends Extension
{
    /**
     * @param null|Order $order
     * @param null|Member $currentUser
     * @param HTTPResponse $response
     */
    public function updateDisallowedResponse(?Order &$order, ?Member &$currentUser, HTTPResponse &$response): void
    {
        if ($currentUser === null) {
            $response = Security::permissionFailure($this->owner);
        }
    }
}
