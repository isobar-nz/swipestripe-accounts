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
class AccountSettingsForm extends Form
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
        $this->member = $member;

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
        return FieldList::create(
            $this->member->dbObject('Email')->scaffoldFormField()
                ->setReadonly(true),
            $this->member->dbObject('OrderStatusNotifications')
                ->scaffoldFormField(_t(self::class . '.OrderStatusNotifications',
                    'Send me email notifications for important order updates'))
                ->setDescription(_t(self::class . '.OrderStatusNotifications_Note', 'Updates will always ' .
                    'be sent to the billing email address on your order.')),
            $this->member->DefaultBillingAddress->scaffoldFormField(),
            ConfirmedPasswordField::create('Password')
                ->setRequireExistingPassword(true)
                ->setCanBeEmpty(true)
        );
    }

    /**
     * @return FieldList
     */
    protected function buildActions(): FieldList
    {
        return FieldList::create(
            FormAction::create('SaveChanges', 'Save Changes')
        );
    }

    /**
     * @return HTTPResponse
     */
    public function SaveChanges(): HTTPResponse
    {
        $this->saveInto($this->member);
        $this->member->write();

        $this->sessionMessage('Your changes were saved successfully.', ValidationResult::TYPE_GOOD);
        return $this->getController()->redirectBack();
    }
}
