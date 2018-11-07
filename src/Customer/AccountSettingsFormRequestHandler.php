<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\FormRequestHandler;
use SilverStripe\ORM\ValidationResult;

/**
 * Class AccountSettingsFormRequestHandler
 * @package SwipeStripe\Accounts\Customer
 */
class AccountSettingsFormRequestHandler extends FormRequestHandler
{
    /**
     * @param array $data
     * @param AccountSettingsFormInterface $form
     * @return HTTPResponse
     */
    public function SaveChanges(array $data, AccountSettingsFormInterface $form): HTTPResponse
    {
        $member = $form->getMember();
        $form->saveInto($member);
        $member->write();

        $form->sessionMessage(_t(self::class . '.CHANGES_SAVED', 'Your changes were saved successfully.'),
            ValidationResult::TYPE_GOOD);
        return $this->redirectBack();
    }
}
