<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Tests\Order;

use SilverStripe\Control\Director;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SwipeStripe\Accounts\Order\OrderExtension;
use SwipeStripe\Accounts\Tests\Fixtures;
use SwipeStripe\Accounts\Tests\PublishesFixtures;
use SwipeStripe\Order\Order;
use SwipeStripe\Order\ViewOrderPage;

/**
 * Class ViewOrderPageTest
 * @package SwipeStripe\Accounts\Tests\Order
 */
class ViewOrderPageTest extends FunctionalTest
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
     * @var ViewOrderPage
     */
    private $viewOrderPage;

    /**
     * @var Member
     */
    private $adminMember;

    /**
     * @var Member
     */
    private $customerMember;

    /**
     *
     */
    public function testLink()
    {
        /** @var Order|OrderExtension $order */
        $order = Order::create();
        $order->IsCart = false;
        $order->MemberID = $this->idFromFixture(Member::class, 'customer');
        $order->Lock(false);
        $order->write();

        $this->assertStringStartsWith($this->viewOrderPage->Link(), $order->Link());
        $this->assertNotContains($order->GuestToken, $order->Link());
    }

    /**
     *
     */
    public function testCantViewCartAsOrder()
    {
        /** @var Order|OrderExtension $order */
        $order = Order::singleton()->createCart();

        $orderUrlWithoutToken = $this->viewOrderPage->Link("{$order->ID}");
        $orderUrlWithToken = $this->viewOrderPage->Link("{$order->ID}/{$order->GuestToken}");

        // Customer
        $this->logInAs($this->customerMember);
        $this->assertSame(404, $this->get($orderUrlWithoutToken)->getStatusCode());
        $this->assertSame(404, $this->get($orderUrlWithToken)->getStatusCode());

        // Customer, owns order
        $order->MemberID = $this->customerMember->ID;
        $order->write();
        $this->assertSame(404, $this->get($orderUrlWithoutToken)->getStatusCode());
        $this->assertSame(404, $this->get($orderUrlWithToken)->getStatusCode());
    }

    /**
     *
     */
    public function testCanViewOrderAsGuest()
    {
        $this->withAutoFollowRedirection(false, function () {
            /** @var Order|OrderExtension $order */
            $order = Order::create();
            $order->IsCart = false;
            $order->MemberID = $this->customerMember->ID;
            $order->Lock(false);
            $order->write();

            // Can't view account order with or without token
            $orderUrlWithoutToken = $this->viewOrderPage->Link("{$order->ID}");
            $response = $this->get($orderUrlWithoutToken);

            $this->assertTrue($response->isRedirect());
            $this->assertStringStartsWith(Director::absoluteURL(Security::login_url()), $response->getHeader('Location'));


            $orderUrlWithToken = $this->viewOrderPage->Link("{$order->ID}/{$order->GuestToken}");
            $this->get($orderUrlWithToken);
            $response = $this->mainSession->followRedirection(); // Follow redirect from /ID/token to /ID

            $this->assertTrue($response->isRedirect());
            $this->assertStringStartsWith(Director::absoluteURL(Security::login_url()), $response->getHeader('Location'));
        });
    }

    /**
     * Customer can view their own order.
     */
    public function testCanViewOrderAsCustomer()
    {
        /** @var Order|OrderExtension $order */
        $order = Order::create();
        $order->IsCart = false;
        $order->MemberID = $this->customerMember->ID;
        $order->Lock(false);
        $order->write();

        $orderUrlWithoutToken = $this->viewOrderPage->Link("{$order->ID}");
        $orderUrlWithToken = $this->viewOrderPage->Link("{$order->ID}/{$order->GuestToken}");

        $this->logInAs($this->customerMember);
        $this->assertSame(200, $this->get($orderUrlWithoutToken)->getStatusCode());
        $this->assertSame(200, $this->get($orderUrlWithToken)->getStatusCode());

        // Can't view another customer's order
        $order->MemberID = $this->adminMember->ID;
        $order->write();
        $this->assertSame(404, $this->get($orderUrlWithoutToken)->getStatusCode());
        $this->assertSame(404, $this->get($orderUrlWithToken)->getStatusCode());
    }

    /**
     *
     */
    protected function setUp()
    {
        $this->registerPublishingBlueprint(ViewOrderPage::class);

        parent::setUp();

        $this->viewOrderPage = $this->objFromFixture(ViewOrderPage::class, 'view-order');

        $this->adminMember = $this->createMemberWithPermission('ADMIN');
        $this->customerMember = $this->objFromFixture(Member::class, 'customer');
    }

    /**
     * @param bool $value
     * @param callable $function
     */
    protected function withAutoFollowRedirection(bool $value, callable $function): void
    {
        $original = $this->autoFollowRedirection;
        try {
            $this->autoFollowRedirection = $value;
            $function();
        } finally {
            $this->autoFollowRedirection = $original;
        }
    }
}
