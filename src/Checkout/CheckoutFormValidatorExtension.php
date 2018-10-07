<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Checkout;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SwipeStripe\Order\Checkout\CheckoutForm;
use SwipeStripe\Order\Checkout\CheckoutFormValidator;

/**
 * Class CheckoutFormValidatorExtension
 * @package SwipeStripe\Accounts\Checkout
 * @property CheckoutFormValidator|CheckoutFormValidatorExtension $owner
 */
class CheckoutFormValidatorExtension extends Extension
{
    /**
     * @param CheckoutForm|CheckoutFormExtension $form
     * @param array $data
     */
    public function beforeRequiredFields(CheckoutForm $form, array $data): void
    {
        if (Security::getCurrentUser()) {
            return;
        }

        $guestOrAccount = $data[CheckoutFormExtension::GUEST_OR_ACCOUNT_FIELD];
        /** @var CheckoutPasswordField $passwordField */
        $passwordField = $form->Fields()->dataFieldByName(CheckoutFormExtension::ACCOUNT_PASSWORD_FIELD);
        $passwordField->canBeEmpty = $guestOrAccount !== CheckoutFormExtension::CHECKOUT_CREATE_ACCOUNT; // Allow empty for guest/unselected
        $passwordField->setMustBeEmpty($guestOrAccount === CheckoutFormExtension::CHECKOUT_GUEST); // Must be empty for guest

        $this->owner->addRequiredField(CheckoutFormExtension::GUEST_OR_ACCOUNT_FIELD);
    }

    /**
     * @param CheckoutForm|CheckoutFormExtension $form
     * @param array $data
     */
    public function validate(CheckoutForm $form, array $data): void
    {
        if (Security::getCurrentUser() || $data[CheckoutFormExtension::GUEST_OR_ACCOUNT_FIELD] !== CheckoutFormExtension::CHECKOUT_CREATE_ACCOUNT) {
            return;
        }

        if (Member::get()->find('Email', $data['CustomerEmail']) !== null) {
            $loginUrl = Controller::join_links(Security::login_url(),
                sprintf('?BackURL=%1$s', rawurlencode($form->getController()->Link())));

            $this->owner->validationError('CustomerEmail', _t(self::class . '.EMAIL_TAKEN',
                'An account with that email already exists. Do you want to <a href="{login_url}">login</a> instead?', [
                    'login_url' => $loginUrl,
                ]), ValidationResult::TYPE_ERROR, ValidationResult::CAST_HTML);
        }

        $passwordValid = Member::singleton()->changePassword($data[CheckoutFormExtension::ACCOUNT_PASSWORD_FIELD],
            false);

        if (!$passwordValid->isValid()) {
            $result = $this->owner->getResult();

            foreach ($passwordValid->getMessages() as $code => $message) {
                $result->addFieldError(CheckoutFormExtension::ACCOUNT_PASSWORD_FIELD, $message['message'],
                    $message['messageType'], $code, $message['messageCast']);
            }
        }
    }
}