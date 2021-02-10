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

if (!defined('_PS_VERSION_')) {
    exit;
}

require _PS_MODULE_DIR_ . 'producttabs/AutoloaderProductTabs.php';
AutoloaderProductTabs::register();

class Producttabs extends Module
{
    public function __construct()
    {
        $this->name = 'producttabs';
        $this->tab = 'front_office_features';
        $this->version = '2.0.1';
        $this->author = 'belvg';
        $this->need_instance = 0;
        $this->module_key = 'f1989b2b5ab214da706c559fe95f16eb';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Tabs');
        $this->description = $this->l('Module creates a new tabs on the frontend product page');

        $this->ps_versions_compliancy = array('min' => '1.7.2', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $product_tab_settings = new ProductTabSettings();
        $product_tab = new ProductTab();
        Configuration::updateValue('PRODUCT_TAB_TYPE', array_shift($product_tab_settings->tab_types));
        Configuration::updateValue('PRODUCT_TAB_CONTENT_TYPE', array_shift($product_tab->tab_content_types));
        Configuration::updateValue('PRODUCT_TAB_COLOR', '#2fb5d2');
        Configuration::updateValue('PRODUCT_TAB_FONT_COLOR', '#ffffff');
        Configuration::updateValue('PRODUCT_TAB_COLOR_SELECTED', '#00d4ff');
        Configuration::updateValue('PRODUCT_TAB_FONT_COLOR_SELECTED', '#ffffff');

        $tab = new Tab();
        $tab->active = true;
        $tab->class_name = 'AdminProductTab';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Product Tab');
        }

        $tab->id_parent = Tab::getIdFromClassName('IMPROVE');
        $tab->position = 0;
        $tab->module = $this->name;
        $tab->save();

        return parent::install() &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionProductDelete') &&
            $this->registerHook('header') &&
            $this->installModuleTab('AdminProductTabs', $this->l('Product Tabs'), 0, 0, false) &&
            $this->installModuleTab('AdminProductTabsSettings', $this->l('Product Tab Settings'), $tab->id) &&
            $this->installModuleTab('AdminProductTabGlobal', $this->l('Global Product Tabs'), $tab->id) &&
            ProductTab::install() &&
            GlobalProductTab::install() &&
            ProductTabSettings::install();
    }

    public function uninstall()
    {
        Configuration::deleteByName('PRODUCT_TAB_TYPE');

        return parent::uninstall() &&
            $this->uninstallModuleTab('AdminProductTab') &&
            $this->uninstallModuleTab('AdminProductTabs') &&
            $this->uninstallModuleTab('AdminProductTabsSettings') &&
            $this->uninstallModuleTab('AdminProductTabGlobal') &&
            ProductTab::uninstall() &&
            GlobalProductTab::uninstall() &&
            ProductTabSettings::uninstall();
    }

    public function hookHeader()
    {
        $product_id = Tools::getValue('id_product');
        if ($product_id) {
            $this->context->controller->addCSS($this->local_path . 'views/css/front.css');
            $this->context->controller->addJS($this->local_path . 'views/js/product_tab.js');

            $producttabs = ProductTab::getTabsByProductId($product_id);
            $product_global_tabs = GlobalProductTab::getTabsByProductId($product_id);
            $tabs = array_merge($producttabs, $product_global_tabs);

            $tabs = HelperTabList::render($product_id, $tabs);

            $id_product_tab_settings = ProductTabSettings::getIdByProductId($product_id);
            $product_tab_settings = new ProductTabSettings($id_product_tab_settings);

            if (empty($producttabs) || !Validate::isLoadedObject($product_tab_settings)) {
                $product_tab_settings->tab_type = Configuration::get('PRODUCT_TAB_TYPE');
            }

            Media::addJsDef(array(
                'producttabs' => $tabs,
                'product_tab_settings' => $product_tab_settings,
            ));

            $style = $this->context->smarty->createTemplate($this->local_path . 'views/css/style-tpl.css');
            $style->assign(array(
                'color' => Configuration::get('PRODUCT_TAB_COLOR'),
                'color_selected' => Configuration::get('PRODUCT_TAB_COLOR_SELECTED'),
                'font_color' => Configuration::get('PRODUCT_TAB_FONT_COLOR'),
                'font_color_selected' => Configuration::get('PRODUCT_TAB_FONT_COLOR_SELECTED'),
            ));
            return $style->fetch();
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product_tab_settings = ProductTabSettings::getIdByProductId($params['id_product']);
        $product_tab_settings = new ProductTabSettings($id_product_tab_settings);

        $tab_types = array();
        foreach ($product_tab_settings->tab_types as $tab_type) {
            $tab_types[$tab_type] = Tools::ucfirst($tab_type);
        }
        $this->context->smarty->assign(array(
            'product_tab_get_list' => $this->context->link->getAdminLink('AdminProductTabs')
                . '&ajax=1&action=getproducttablist',
            'product_tab_get_form' => $this->context->link->getAdminLink('AdminProductTabs')
                . '&ajax=1&action=getproducttabform',
            'save_product_tab' => $this->context->link->getAdminLink('AdminProductTabs')
                . '&ajax=1&action=saveproducttab',
            'delete_product_tab' => $this->context->link->getAdminLink('AdminProductTabs')
                . '&ajax=1&action=deleteproducttab',
            'active_product_tab' => $this->context->link->getAdminLink('AdminProductTabs')
                . '&ajax=1&action=activeproducttab',
            'position_product_tab' => $this->context->link->getAdminLink('AdminProductTabs')
                . '&ajax=1&action=positionproducttab',
            'tab_type_product_tab' => $this->context->link->getAdminLink('AdminProductTabs')
                . '&ajax=1&action=settabtypeproducttab',
            'id_product' => $params['id_product'],
            'product_tab_settings' => $product_tab_settings,
            'tab_types' => $tab_types,
            'product_tab_default_type' => Configuration::get('PRODUCT_TAB_TYPE'),
        ));

        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/product_tab_extra.tpl');
    }

    public function hookActionProductDelete($params)
    {
        $product_tab = new ProductTab(ProductTab::getIdByProductId($params['id_product']));
        $product_tab->delete();

        $product_tab_settings = new ProductTabSettings(ProductTabSettings::getIdByProductId($params['id_product']));
        $product_tab_settings->delete();
    }

    /**
     * Add new page in back office
     *
     * @param type $class_name
     * @param type $tab_name
     * @param type $id_parent
     * @param type $position
     *
     * @return type
     */
    public function installModuleTab($class_name, $tab_name, $id_parent = 0, $position = 0, $active = true)
    {
        $tab = new Tab();
        $tab->active = $active;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        $tab->id_parent = $id_parent;
        $tab->position = $position;
        $tab->module = $this->name;

        return $tab->save();
    }

    /**
     * Delete custom page of back office
     *
     * @param type $class_name
     *
     * @return type
     */
    public function uninstallModuleTab($class_name)
    {
        $id_tab = Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }
}
