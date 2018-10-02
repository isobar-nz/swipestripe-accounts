<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\ORM\SS_List;
use SilverStripe\Security\Security;
use SwipeStripe\Order\Order;

/**
 * Class MyOrdersPageController
 * @package SwipeStripe\Accounts\Customer
 * @property SS_List|Order[] $Orders
 */
class MyOrdersPageController extends \PageController
{
    /**
     * @return SS_List|Order[]
     */
    public function getOrders(): SS_List
    {
        return Security::getCurrentUser()->Orders()->filter('IsCart', false);
    }
}
