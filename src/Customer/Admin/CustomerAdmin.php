<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer\Admin;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Security\Member;
use SwipeStripe\Accounts\Customer\MemberExtension;
use SwipeStripe\Accounts\SwipeStripeAccountPermissions;

/**
 * Class CustomerAdmin
 * @package SwipeStripe\Accounts\Customer
 */
class CustomerAdmin extends ModelAdmin
{
    /**
     * @var string
     */
    private static $url_segment = 'swipestripe/accounts/customers';

    /**
     * @var string
     */
    private static $menu_title = 'Customers';

    /**
     * @var string
     */
    private static $menu_icon_class = 'font-icon-torsos-all';

    /**
     * @var array
     */
    private static $required_permission_codes = [
        SwipeStripeAccountPermissions::VIEW_CUSTOMERS,
    ];

    /**
     * @var array
     */
    private static $managed_models = [
        Member::class,
    ];

    /**
     * @inheritDoc
     */
    public function getList()
    {
        return parent::getList()->filter('Groups.Code', MemberExtension::CUSTOMERS_GROUP);
    }

    /**
     * @inheritDoc
     */
    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        $memberGridField = $form->Fields()->dataFieldByName($this->sanitiseClassName(Member::class));

        if ($memberGridField instanceof GridField) {
            $detailForm = $memberGridField->getConfig()->getComponentByType(GridFieldDetailForm::class);

            if ($detailForm instanceof GridFieldDetailForm) {
                $detailForm->setItemRequestClass(GridFieldDetailFormCustomerItemRequest::class);
            }
        }

        return $form;
    }
}
