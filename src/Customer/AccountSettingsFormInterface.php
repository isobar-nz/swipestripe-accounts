<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\Forms\Form;
use SilverStripe\Security\Member;

/**
 * Interface AccountSettingsFormInterface
 * @package SwipeStripe\Accounts\Customer
 * @mixin Form
 */
interface AccountSettingsFormInterface
{
    /**
     * @return Member|MemberExtension
     */
    public function getMember(): Member;

    /**
     * @param Member $member
     * @return $this
     */
    public function setMember(Member $member): self;
}
