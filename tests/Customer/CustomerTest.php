<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Tests\Customer;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Accounts\Order\OrderExtension;
use SwipeStripe\Accounts\SwipeStripeAccountPermissions;
use SwipeStripe\Accounts\Tests\Fixtures;
use SwipeStripe\Order\Order;
use SwipeStripe\ShopPermissions;

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
        /** @var Member|MemberExtension $customer */
        $customer = $this->objFromFixture(Member::class, 'customer');

        /** @var Order|OrderExtension $order */
        $order = Order::create();
        $order->MemberID = $customer->ID;
        $order->write();

        /** @var Order|OrderExtension $order2 */
        $order2 = Order::create();
        $order2->MemberID = $customer->ID;
        $order2->write();

        /** @var Order|OrderExtension $order3 */
        $order3 = Order::create();
        $order3->MemberID = $customer->ID;
        $order3->write();

        $order4ID = Order::create()->write();

        $memberOrderIDs = $customer->Orders()->column('ID');
        $expectedOrderIDs = [$order->ID, $order2->ID, $order3->ID];
        sort($memberOrderIDs);
        sort($expectedOrderIDs);

        $this->assertEquals($expectedOrderIDs, $memberOrderIDs);
        $this->assertNotContains($order4ID, $memberOrderIDs);
    }

    /**
     *
     */
    public function testCanViewCustomers()
    {
        /** @var Member|MemberExtension $customer */
        $customer = $this->objFromFixture(Member::class, 'customer');

        $this->assertFalse($customer->canView(
            $this->createMemberWithPermission(ShopPermissions::VIEW_ORDERS)
        ));

        $this->assertTrue($customer->canView(
            $this->createMemberWithPermission(SwipeStripeAccountPermissions::VIEW_CUSTOMERS)
        ));
    }

    /**
     *
     */
    public function testCanEditCustomers()
    {
        /** @var Member|MemberExtension $customer */
        $customer = $this->objFromFixture(Member::class, 'customer');

        $this->assertFalse($customer->canEdit(
            $this->createMemberWithPermission(SwipeStripeAccountPermissions::VIEW_CUSTOMERS)
        ));

        $this->assertTrue($customer->canEdit(
            $this->createMemberWithPermission(SwipeStripeAccountPermissions::EDIT_CUSTOMERS)
        ));
    }
}
