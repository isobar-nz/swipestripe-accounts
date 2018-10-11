<?php
declare(strict_types=1);

namespace SwipeStripe\Accounts\Customer\Admin;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\ORM\Filterable;
use SilverStripe\Security\Member;
use SwipeStripe\Order\Order;

/**
 * Class GridFieldDetailFormCustomerItemRequest
 * @package SwipeStripe\Accounts\Customer\Admin
 */
class GridFieldDetailFormCustomerItemRequest extends GridFieldDetailForm_ItemRequest
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'ItemEditForm',
    ];

    /**
     * @inheritDoc
     */
    public function ItemEditForm()
    {
        $form = parent::ItemEditForm();

        if ($this->getRecord() instanceof Member) {
            $form = $this->updateMemberForm($form);
        }

        return $form;
    }

    /**
     * @param Form $form
     * @return Form
     */
    protected function updateMemberForm(Form $form): Form
    {
        $mainTabFields = $form->Fields()->findOrMakeTab('Root.Main')->Fields();

        // Remove fields that shouldn't be changed by customer admins only (should be done via security)
        $mainTabFields->removeByName([
            'DirectGroups',
            'FailedLoginCount',
            'Locale',
            'Password',
            'Permissions',
        ]);

        // Make email read-only - if this is changed, customer can't log in
        $mainTabFields->dataFieldByName('Email')->setReadonly(true);

        // Re-order fields
        $mainTabFields->insertBefore('DefaultBillingAddress', $mainTabFields->dataFieldByName('Email'));
        $mainTabFields->insertAfter('Email', $mainTabFields->dataFieldByName('FirstName'));
        $mainTabFields->insertAfter('FirstName', $mainTabFields->dataFieldByName('Surname'));
        $mainTabFields->insertAfter('Surname', $mainTabFields->dataFieldByName('OrderStatusNotifications'));

        $ordersGridField = $form->Fields()->dataFieldByName('Orders');
        if ($ordersGridField instanceof GridField && $ordersGridField->getModelClass() === Order::class) {
            // Stop admins from linking/unlinking orders
            $ordersGridField->setConfig(GridFieldConfig_RecordViewer::create());

            // Hide carts from orders view
            $list = $ordersGridField->getList();
            if ($list instanceof Filterable) {
                $ordersGridField->setList($list->filter('IsCart', false));
            }
        }

        $this->extend('updateMemberForm', $form);
        return $form;
    }
}
