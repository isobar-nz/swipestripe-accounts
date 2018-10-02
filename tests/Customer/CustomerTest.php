<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Tests\Customer;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Accounts\Order\OrderExtension;
use SwipeStripe\Order\Order;
use SwipeStripe\Accounts\Tests\Fixtures;

/**
 * Class CustomerTest
 * @package SwipeStripe\Accounts\Tests\Customer
 */
class CustomerTest extends SapphireTest
{
    /**
     * @var array
     */
    protected static $fixture_file = [
        Fixtures::CUSTOMERS,
    ];

    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     * @throws ValidationException
     */
    public function testMemberOrders()
    {
        /** @var Member|MemberExtension $member */
        $member = $this->objFromFixture(Member::class, 'customer');

        /** @var Order|OrderExtension $order */
        $order = Order::create();
        $order->MemberID = $member->ID;
        $order->write();

        /** @var Order|OrderExtension $order2 */
        $order2 = Order::create();
        $order2->MemberID = $member->ID;
        $order2->write();

        /** @var Order|OrderExtension $order3 */
        $order3 = Order::create();
        $order3->MemberID = $member->ID;
        $order3->write();

        $order4ID = Order::create()->write();

        $memberOrderIDs = $member->Orders()->column('ID');
        $expectedOrderIDs = [$order->ID, $order2->ID, $order3->ID];
        sort($memberOrderIDs);
        sort($expectedOrderIDs);

        $this->assertEquals($expectedOrderIDs, $memberOrderIDs);
        $this->assertNotContains($order4ID, $memberOrderIDs);
    }
}
