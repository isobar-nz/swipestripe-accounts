<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Forms\ConfirmedPasswordField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Member;

/**
 * Class AccountSettingsForm
 * @package SwipeStripe\Accounts\Customer
 */
class AccountSettingsForm extends Form implements AccountSettingsFormInterface
{
    /**
     * @var Member|MemberExtension
     */
    protected $member;

    /**
     * AccountSettingsForm constructor.
     * @param Member $member
     * @param RequestHandler|null $controller
     * @param string $name
     */
    public function __construct(
        Member $member,
        RequestHandler $controller = null,
        string $name = self::DEFAULT_NAME
    ) {
        $this->setMember($member);

        parent::__construct($controller, $name, $this->buildFields(), $this->buildActions());

        if (!$this->getSessionData()) {
            $this->loadDataFrom($member);
        }
    }

    /**
     * @return FieldList
     */
    protected function buildFields(): FieldList
    {
        $fields = FieldList::create(
            $this->member->dbObject('Email')
                ->scaffoldFormField(_t(self::class . '.Email', 'Email'))
                ->setReadonly(true),
            $this->member->dbObject('OrderStatusNotifications')
                ->scaffoldFormField(_t(self::class . '.OrderStatusNotifications',
                    'Send me email notifications for important order updates'))
                ->setDescription(_t(self::class . '.OrderStatusNotifications_Note', 'Updates will always ' .
                    'be sent to the billing email address on your order.')),
            $this->member->DefaultBillingAddress->scaffoldFormField(),
            ConfirmedPasswordField::create('Password', _t(self::class . '.Password', 'Password'))
                ->setRequireExistingPassword(true)
                ->setCanBeEmpty(true)
        );

        $this->extend('updateFields', $fields);
        return $fields;
    }

    /**
     * @return FieldList
     */
    protected function buildActions(): FieldList
    {
        $actions = FieldList::create(
            FormAction::create('SaveChanges', _t(self::class . '.SAVE_CHANGES', 'Save Changes'))
        );

        $this->extend('updateActions', $actions);
        return $actions;
    }

    /**
     * @inheritDoc
     */
    public function getMember(): Member
    {
        return $this->member;
    }

    /**
     * @inheritDoc
     */
    public function setMember(Member $member): AccountSettingsFormInterface
    {
        $this->message = $member;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function buildRequestHandler()
    {
        return AccountSettingsFormRequestHandler::create($this);
    }
}
