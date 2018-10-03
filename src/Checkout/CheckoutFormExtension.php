<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Checkout;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SwipeStripe\Accounts\AccountCreationEmail;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Accounts\Order\OrderExtension;
use SwipeStripe\Order\Checkout\CheckoutForm;
use SwipeStripe\Order\Order;

/**
 * Class CheckoutFormExtension
 * @package SwipeStripe\Accounts\Checkout
 * @property CheckoutForm|CheckoutFormExtension $owner
 */
class CheckoutFormExtension extends Extension
{
    const GUEST_OR_ACCOUNT_FIELD = 'GuestOrAccount';
    const ACCOUNT_PASSWORD_FIELD = 'AccountPassword';

    const CHECKOUT_GUEST = 'Guest';
    const CHECKOUT_CREATE_ACCOUNT = 'Account';

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
     *
     */
    public function beforeLoadDataFromCart(): void
    {
        /** @var Order|OrderExtension $cart */
        $cart = $this->owner->getCart();
        $cart->populateCustomerDefaults(Security::getCurrentUser());
    }

    /**
     * @param FieldList $fields
     */
    public function updateFields(FieldList $fields): void
    {
        if (Security::getCurrentUser()) {
            return;
        }

        $fields->add(OptionsetField::create(static::GUEST_OR_ACCOUNT_FIELD, '', [
            static::CHECKOUT_GUEST          => 'Checkout as guest',
            static::CHECKOUT_CREATE_ACCOUNT => 'Create an account',
        ], static::CHECKOUT_CREATE_ACCOUNT));

        $fields->add(CheckoutPasswordField::create(static::ACCOUNT_PASSWORD_FIELD));
    }

    /**
     * @param array $data
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function beforeInitPayment(array $data): void
    {
        if (Security::getCurrentUser() === null && $data[static::GUEST_OR_ACCOUNT_FIELD] === static::CHECKOUT_CREATE_ACCOUNT) {
            $newAccount = $this->createCustomerAccount($data);
            $this->identityStore->logIn($newAccount, false, $this->owner->getController()->getRequest());
            Security::setCurrentUser($newAccount);
        }

        $this->owner->getCart()->MemberID = Security::getCurrentUser() ? Security::getCurrentUser()->ID : 0;
    }

    /**
     * @param array $data
     * @return Member
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function createCustomerAccount(array $data): Member
    {
        /** @var Member|MemberExtension $member */
        $member = Member::create();
        $member->Email = $data['CustomerEmail'];
        $member->setName($data['CustomerName']);
        $member->changePassword($data[static::ACCOUNT_PASSWORD_FIELD]['_Password'], false);
        $member->DefaultBillingAddress->copyFromArray($data, 'BillingAddress');

        $member->setField(AccountCreationEmail::SEND_EMAIL_FLAG, true);
        $member->write();

        return $member;
    }
}
