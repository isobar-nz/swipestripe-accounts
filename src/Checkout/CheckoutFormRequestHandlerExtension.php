<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Checkout;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SwipeStripe\Accounts\AccountCreationEmail;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Accounts\Order\OrderExtension;
use SwipeStripe\Order\Checkout\CheckoutForm;
use SwipeStripe\Order\Checkout\CheckoutFormRequestHandler;
use SwipeStripe\Order\Order;

/**
 * Class CheckoutFormRequestHandlerExtension
 * @package SwipeStripe\Accounts\Checkout
 * @property CheckoutFormRequestHandler|CheckoutFormRequestHandlerExtension $owner
 */
class CheckoutFormRequestHandlerExtension extends Extension
{
    /**
     * @var IdentityStore
     */
    protected $identityStore;

    /**
     * CheckoutFormExtension constructor.
     */
    public function __construct()
    {
        $this->identityStore = Injector::inst()->get(IdentityStore::class);
    }

    /**
     * @param CheckoutForm|CheckoutFormExtension $form
     * @param array $data
     */
    public function beforeInitPayment(CheckoutForm $form, array $data): void
    {
        /** @var Order|OrderExtension $cart */
        $cart = $form->getCart();

        if (Security::getCurrentUser() === null && !$form->isGuestCheckout()) {
            $newAccount = $this->createCustomerAccount($cart, $data);
            $this->identityStore->logIn($newAccount, false, $form->getController()->getRequest());
            Security::setCurrentUser($newAccount);
        }

        /** @var Member|MemberExtension|null $currentUser */
        $currentUser = Security::getCurrentUser();
        $cart->Member = $currentUser;

        if ($currentUser !== null) {
            $currentUser->addToCustomersGroup();
        }
    }

    /**
     * @param Order $cart
     * @param array $data
     * @return Member
     */
    protected function createCustomerAccount(Order $cart, array $data): Member
    {
        /** @var Member|MemberExtension $member */
        $member = Member::create();
        $member->Email = $data['CustomerEmail'];
        $member->setName($data['CustomerName']);
        $member->changePassword($data[CheckoutFormExtension::ACCOUNT_PASSWORD_FIELD]['_Password'], false);
        $member->DefaultBillingAddress->setValue($cart->BillingAddress);

        $member->setField(AccountCreationEmail::SEND_EMAIL_FLAG, true);
        $member->write();

        return $member;
    }
}
