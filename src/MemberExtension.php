<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;

/**
 * Class MemberExtension
 * @package SwipeStripe\Accounts
 * @property Member|MemberExtension $owner
 */
class MemberExtension extends DataExtension
{
    /**
     *
     */
    public function onAfterWrite()
    {
        // Send email on account creation by SwipeStripe
        if ($this->owner->isChanged('ID', Member::CHANGE_VALUE) &&
            $this->owner->getField(AccountCreationEmail::SEND_EMAIL_FLAG)) {

            $this->owner->setField(AccountCreationEmail::SEND_EMAIL_FLAG, false);
            AccountCreationEmail::create($this->owner)->send();
        }
    }
}
