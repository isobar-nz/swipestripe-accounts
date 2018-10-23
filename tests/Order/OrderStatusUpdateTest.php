<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Tests\Order;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Accounts\Order\OrderExtension;
use SwipeStripe\Accounts\Tests\Fixtures;
use SwipeStripe\Accounts\Tests\PublishesFixtures;
use SwipeStripe\Order\Order;
use SwipeStripe\Order\Status\OrderStatus;
use SwipeStripe\Order\Status\OrderStatusUpdate;
use SwipeStripe\Order\ViewOrderPage;

/**
 * Class OrderStatusUpdateTest
 * @package SwipeStripe\Accounts\Tests\Order
 */
class OrderStatusUpdateTest extends SapphireTest
{
    use PublishesFixtures;

    /**
     * @var array
     */
    protected static $fixture_file = [
        Fixtures::BASE_COMMERCE_PAGES,
        Fixtures::CUSTOMERS,
    ];

    /**
     * @var bool
     */
    protected $usesDatabase = true;

    /**
     * @var Order|OrderExtension
     */
    protected $order;

    /**
     *
     */
    public function testGuestIsNotified()
    {
        $order = $this->order;
        $customerEmail = 'guest@example.com';
        $order->CustomerEmail = $customerEmail;
        $order->write();

        $this->assertFalse($order->IsMutable());

        $this->createCustomerInvisibleUpdate($order)->write();
        $this->assertNull($this->findEmail($customerEmail));

        $this->createNonNotifyingUpdate($order)->write();
        $this->assertNull($this->findEmail($customerEmail));

        $this->createNotifyingUpdate($order)->write();
        $this->assertEmailSent($customerEmail);
    }

    /**
     *
     */
    public function testCustomerOptInIsNotified()
    {
        $order = $this->order;
        /** @var Member|MemberExtension $customer */
        $customer = $this->objFromFixture(Member::class, 'customer');
        $order->CustomerEmail = $customer->Email;
        $order->MemberID = $customer->ID;
        $order->write();

        $this->assertFalse($order->IsMutable());

        $this->createCustomerInvisibleUpdate($order)->write();
        $this->assertNull($this->findEmail($customer->Email));

        $this->createNonNotifyingUpdate($order)->write();
        $this->assertNull($this->findEmail($customer->Email));

        $this->createNotifyingUpdate($order)->write();
        $this->assertEmailSent($customer->Email);
    }

    /**
     *
     */
    public function testCustomerOptOutIsNotNotified()
    {
        $order = $this->order;
        /** @var Member|MemberExtension $customer */
        $customer = $this->objFromFixture(Member::class, 'customer-no-notifications');
        $order->CustomerEmail = $customer->Email;
        $order->MemberID = $customer->ID;
        $order->write();

        $this->assertFalse($order->IsMutable());

        $this->createCustomerInvisibleUpdate($order)->write();
        $this->assertNull($this->findEmail($customer->Email));

        $this->createNonNotifyingUpdate($order)->write();
        $this->assertNull($this->findEmail($customer->Email));

        $this->createNotifyingUpdate($order)->write();
        $this->assertNull($this->findEmail($customer->Email));
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->registerPublishingBlueprint(ViewOrderPage::class);

        parent::setUp();

        $order = Order::create();
        $order->IsCart = false;
        $order->Lock(false);

        $this->order = $order;
    }

    /**
     * @param Order $order
     * @return OrderStatusUpdate
     */
    protected function createNotifyingUpdate(Order $order): OrderStatusUpdate
    {
        $update = $this->createOrderUpdate($order);
        $update->CustomerVisible = true;
        $update->NotifyCustomer = true;
        return $update;
    }

    /**
     * @param Order $order
     * @return OrderStatusUpdate
     */
    protected function createOrderUpdate(Order $order): OrderStatusUpdate
    {
        $update = OrderStatusUpdate::create();
        $update->Status = OrderStatus::CONFIRMED;
        $update->OrderID = $order->ID;
        return $update;
    }

    /**
     * @param Order $order
     * @return OrderStatusUpdate
     */
    protected function createCustomerInvisibleUpdate(Order $order): OrderStatusUpdate
    {
        $update = $this->createOrderUpdate($order);
        $update->CustomerVisible = false;
        $update->NotifyCustomer = true;
        return $update;
    }

    /**
     * @param Order $order
     * @return OrderStatusUpdate
     */
    protected function createNonNotifyingUpdate(Order $order): OrderStatusUpdate
    {
        $update = $this->createOrderUpdate($order);
        $update->CustomerVisible = true;
        $update->NotifyCustomer = false;
        return $update;
    }
}
