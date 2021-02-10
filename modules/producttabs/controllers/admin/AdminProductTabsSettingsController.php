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

class AdminProductTabsSettingsController extends ModuleAdminController
{
    public function __construct()
    {

        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();

        $list = array();
        $product_tab_settings = new ProductTabSettings();
        foreach ($product_tab_settings->tab_types as $type) {
            $list[] = array(
                'name' => Tools::ucfirst($type),
                'id' => $type,
            );
        }

        $fields = array(
            'PRODUCT_TAB_TYPE' => array(
                'title' => $this->trans('Product Tab Type', array(), 'Admin.ProductTab.Settings'),
                'desc' => $this->trans('You can choose global tab type.', array(), 'Admin.ProductTab.Settings'),
                'cast' => 'strval',
                'type' => 'select',
                'list' => $list,
                'identifier' => 'id'
            ),
            'PRODUCT_TAB_COLOR' => array(
                'title' => $this->trans('Background color tabs', array(), 'Admin.ProductTab.Settings'),
                'type' => 'color',
                'size' => 255,
                'name' => 'PRODUCT_TAB_COLOR',
            ),
            'PRODUCT_TAB_FONT_COLOR' => array(
                'title' => $this->trans('Font color tabs', array(), 'Admin.ProductTab.Settings'),
                'type' => 'color',
                'size' => 255,
                'name' => 'PRODUCT_TAB_FONT_COLOR',
            ),
            'PRODUCT_TAB_COLOR_SELECTED' => array(
                'title' => $this->trans('Background color of selected tabs', array(), 'Admin.ProductTab.Settings'),
                'type' => 'color',
                'size' => 255,
                'name' => 'PRODUCT_TAB_COLOR_SELECTED',
            ),
            'PRODUCT_TAB_FONT_COLOR_SELECTED' => array(
                'title' => $this->trans('Font color of selected tabs', array(), 'Admin.ProductTab.Settings'),
                'type' => 'color',
                'size' => 255,
                'name' => 'PRODUCT_TAB_FONT_COLOR_SELECTED',
            ),
        );

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('General', array(), 'Admin.Global'),
                'icon' =>    'icon-cogs',
                'fields' =>    $fields,
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
        );
    }
}
