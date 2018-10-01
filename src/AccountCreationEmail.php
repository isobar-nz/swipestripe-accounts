<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts;

use SilverStripe\Security\Member;
use SilverStripe\SiteConfig\SiteConfig;
use SwipeStripe\SwipeStripeEmail;

/**
 * Class AccountCreationEmail
 * @package SwipeStripe\Accounts
 */
class AccountCreationEmail extends SwipeStripeEmail
{
    const SEND_EMAIL_FLAG = self::class . '.SEND_ON_CREATE';

    /**
     * @var Member
     */
    protected $member;

    /**
     * @param Member $member
     * @inheritdoc
     */
    public function __construct(
        Member $member,
        $from = null,
        $to = null,
        ?string $subject = null,
        ?string $body = null,
        $cc = null,
        $bcc = null,
        ?string $returnPath = null
    ) {
        $this->member = $member;

        $subject = $subject ?? _t(self::class . '.SUBJECT', 'Your {site} account has been created', [
                'site'        => SiteConfig::current_site_config()->Title,
                'member_name' => $member->getName(),
            ]);
        $to = $to ?? [$member->Email => $member->getName()];

        parent::__construct($from, $to, $subject, $body, $cc, $bcc, $returnPath);
    }

    /**
     * @return Member
     */
    public function getMember(): Member
    {
        return $this->member;
    }
}
