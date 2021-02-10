<?php
/**
 * 2007-2018 Belvg
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author Belvg
 *  @copyright  2007-2018 Belvg
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Belvg
 */

class AdminProductTabGlobalController extends ModuleAdminController
{
    protected $position_identifier = 'id_product_tab_global';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'GlobalProductTab';
        $this->table = 'product_tab_global';

        $this->lang = true;
        $this->deleted = false;
        $this->_defaultOrderBy = 'position';

        parent::__construct();

        $this->fields_list = array(
            'id_product_tab_global' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'title' => array(
                'title' => $this->trans('Title', array(), 'Admin.Global')
            ),
            'content' => array(
                'title' => $this->trans('Description', array(), 'Admin.Global'),
                'callback' => 'getDescriptionClean',
                'orderby' => false
            ),
            'position' => array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center'
            ),
            'active' => array(
                'title' => $this->trans('Displayed', array(), 'Admin.Global'),
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'ajax' => true,
                'orderby' => false
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning')
            )
        );
        $this->specificConfirmDelete = false;

        Shop::addTableAssociation(GlobalProductTab::$definition['table'], array('type' => 'shop'));
        Shop::addTableAssociation(GlobalProductTab::$definition['table'] . '_lang', array('type' => 'fk_shop'));
    }

    public function init()
    {
        // if we are not in a shop context, we remove the position column
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            unset($this->fields_list['position']);
        }

        parent::init();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/global_product_tab.js');
    }

    public function renderList()
    {
        $this->addRowAction('add');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function getList($id_lang, $order_b = null, $order_w = null, $start = 0, $limit = null, $id_lang_s = false)
    {
        $id_lang_s = Context::getContext()->shop->id;
        parent::getList($id_lang, $order_b, $order_w, $start, $limit, $id_lang_s);
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $unidentified = new Group(Configuration::get('PS_UNIDENTIFIED_GROUP'));
        $guest = new Group(Configuration::get('PS_GUEST_GROUP'));
        $default = new Group(Configuration::get('PS_CUSTOMER_GROUP'));

        $unidentified_group_information = sprintf(
            $this->trans(
                '%s - All people without a valid customer account.',
                array(),
                'Admin.Catalog.Feature'
            ),
            '<b>'.$unidentified->name[$this->context->language->id].'</b>'
        );
        $guest_group_information = sprintf(
            $this->trans(
                '%s - Customer who placed an order with the guest checkout.',
                array(),
                'Admin.Catalog.Feature'
            ),
            '<b>'.$guest->name[$this->context->language->id].'</b>'
        );
        $default_group_information = sprintf(
            $this->trans(
                '%s - All people who have created an account on this site.',
                array(),
                'Admin.Catalog.Feature'
            ),
            '<b>'.$default->name[$this->context->language->id].'</b>'
        );

        $tab_content_types = array();
        foreach ($obj->tab_content_types as $type) {
            $tab_content_types[] = array(
                'id' => $type,
                'name' => $type,
            );
        }
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->trans('Product Tab', array(), 'Admin.Catalog.Feature'),
                'icon' => 'icon-tags'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->trans('Tab type', array(), 'Admin.Global'),
                    'name' => 'tab_content_type',
                    'col' => '4',
                    'default_value' => $obj->tab_content_type,
                    'options' => array(
                        'query' => $tab_content_types,
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Title', array(), 'Admin.Global'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                    'class' => 'copy2friendlyUrl',
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Content', array(), 'Admin.Global'),
                    'name' => 'content',
                    'autoload_rte' => true,
                    'lang' => true,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
                ),
                array(
                    'type'  => 'categories',
                    'label' => $this->trans('Category', array(), 'Admin.Catalog.Feature'),
                    'name'  => 'categories',
                    'tree'  => array(
                        'id'                  => 'categories-tree',
                        'selected_categories' => GlobalProductTab::getCategoriesByIdTab($obj->id),
                        'use_checkbox' => true,
                        'disabled_categories' => null,
                        'root_category'       => 0,
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Manufacturers', array(), 'Admin.Global'),
                    'name' => 'manufacturer_id[]',
                    'col' => '4',
                    'multiple' => true,
                    'default_value' => GlobalProductTab::getManufacturersByIdTab($obj->id),
                    'options' => array(
                        'query' => Manufacturer::getManufacturers(),
                        'id' => 'id_manufacturer',
                        'name' => 'name'
                    ),
                    'values' => array(
                        'value' => 1,
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Suppliers', array(), 'Admin.Global'),
                    'name' => 'supplier_id[]',
                    'col' => '4',
                    'multiple' => true,
                    'default_value' => GlobalProductTab::getSuppliersByIdTab($obj->id),
                    'options' => array(
                        'query' => Supplier::getSuppliers(),
                        'id' => 'id_supplier',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Displayed', array(), 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    )
                ),
                array(
                    'type' => 'group',
                    'label' => $this->trans('Group access', array(), 'Admin.Catalog.Feature'),
                    'name' => 'groupBox',
                    'values' => Group::getGroups(Context::getContext()->language->id),
                    'info_introduction' => $this->trans(
                        'You now have three default customer groups.',
                        array(),
                        'Admin.Catalog.Help'
                    ),
                    'unidentified' => $unidentified_group_information,
                    'guest' => $guest_group_information,
                    'customer' => $default_group_information,
                    'hint' => $this->trans(
                        'Mark all of the customer groups which you would like to have access to this category.',
                        array(),
                        'Admin.Catalog.Help'
                    )
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
                'name' => 'submitAdd'.$this->table.(
                    !Tools::isSubmit('add'.$this->table)
                    && !Tools::isSubmit('add'.$this->table.'root') ? '': 'AndBackToParent'
                    )
            )
        );

        // Display this field only if multistore option is enabled AND there are several stores configured
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        // Added values of object Group
        $category_groups_ids = $obj->getGroups();

        $groups = Group::getGroups($this->context->language->id);
        // if empty $carrier_groups_ids : object creation : we set the default groups
        if (empty($category_groups_ids)) {
            $preselected = array(
                Configuration::get('PS_UNIDENTIFIED_GROUP'),
                Configuration::get('PS_GUEST_GROUP'),
                Configuration::get('PS_CUSTOMER_GROUP')
            );
            $category_groups_ids = array_merge($category_groups_ids, $preselected);
        }
        foreach ($groups as $group) {
            $this->fields_value['groupBox_'.$group['id_group']] = Tools::getValue(
                'groupBox_'.$group['id_group'],
                (in_array($group['id_group'], $category_groups_ids))
            );
        }

        return parent::renderForm();
    }

    public static function getDescriptionClean($description)
    {
        return Tools::substr(Tools::getDescriptionClean($description), 0, 100);
    }

    public function ajaxProcessUpdatePositions()
    {
        $positions = Tools::getValue($this->table);
        $success = false;
        if (is_array($positions)) {
            foreach ($positions as $key => $value) {
                $pos = explode('_', $value);
                if (isset($pos[1]) && isset($pos[2])) {
                    $product_tab_global = new GlobalProductTab($pos[2]);
                    if (Validate::isLoadedObject($product_tab_global)) {
                        $product_tab_global->position = $key;
                        if ($product_tab_global->save()) {
                            $success = true;
                        }
                    }
                }
            }
        }

        if ($success) {
            die(true);
        } else {
            die('{"hasError" : true, errors : "Cannot update categories position"}');
        }
    }

    public function ajaxProcessStatusProductTabGlobal()
    {
        if (!$id = (int)Tools::getValue(GlobalProductTab::$definition['primary'])) {
            die(json_encode(array(
                'success' => false,
                'error' => true,
                'text' => $this->trans(
                    'Failed to update the status',
                    array(),
                    'Admin.Notifications.Error'
                )
            )));
        } else {
            $product_tab_global = new GlobalProductTab((int)$id);
            if (Validate::isLoadedObject($product_tab_global)) {
                $product_tab_global->active = $product_tab_global->active == 1 ? 0 : 1;
                $product_tab_global->save() ?
                    die(json_encode(array(
                        'success' => true,
                        'text' => $this->trans(
                            'The status has been updated successfully',
                            array(),
                            'Admin.Notifications.Success'
                        )
                    ))) :
                    die(json_encode(array(
                        'success' => false,
                        'error' => true,
                        'text' => $this->trans(
                            'Failed to update the status',
                            array(),
                            'Admin.Notifications.Success'
                        )
                    )));
            }
        }
    }
}
