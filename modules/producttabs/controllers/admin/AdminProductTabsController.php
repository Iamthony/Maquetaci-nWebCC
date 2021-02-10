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

class AdminProductTabsController extends ModuleAdminController
{

    public function ajaxProcessDeleteProductTab()
    {
        try {
            $product_tab = new ProductTab(Tools::getValue('id_product_tab'));
            $product_tab->delete();

            $this->ajaxProcessGetProductTabList();
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    public function ajaxProcessGetProductTabList()
    {
        try {
            $producttabs = ProductTab::getTabsByProductId(Tools::getValue('id_product'), false);

            $this->context->smarty->assign(array(
                'producttabs' => $producttabs,
                'producttabs_link_get' => $this->context->link->getAdminLink('AdminProductTabs')
                    . '&ajax=1&action=getproducttab',
                'producttabs_link_delete' => $this->context->link->getAdminLink('AdminProductTabs')
                    . '&ajax=1&action=deleteproducttab',
            ));

            die($this->context->smarty->fetch(PRODUCT_TABS_DIR . 'views/templates/admin/list.tpl'));
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    public function ajaxProcessGetProductTabForm()
    {
        try {
            $languages = Language::getLanguages();
            $product_tab = new ProductTab(Tools::getValue('id_product_tab'));

            // Added values of object Group
            $category_groups_ids = $product_tab->getGroups();

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

            $groupBox = array();
            foreach ($groups as $group) {
                $groupBox[$group['id_group']] = array(
                    'name' => 'groupBox_'.$group['id_group'],
                    'value' => (int)in_array($group['id_group'], $category_groups_ids),
                    'name' => $group['name'],
                );
            }

            $tab_content_types = array();
            foreach ($product_tab->tab_content_types as $tab_type) {
                $tab_content_types[$tab_type] = Tools::str_replace_once('_', ' ', Tools::ucfirst($tab_type));
            }

            $this->context->smarty->assign(array(
                'product_tab' => $product_tab,
                'languages' => $languages,
                'groupBoxes' => $groupBox,
                'tab_content_types' => $tab_content_types,
            ));

            die($this->context->smarty->fetch(PRODUCT_TABS_DIR . 'views/templates/admin/form.tpl'));
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    public function ajaxProcessSaveProductTab()
    {
        try {
            $product_tab = new ProductTab(Tools::getValue('id_product_tab'));

            $languages = Language::getLanguages();
            foreach ($languages as $lang) {
                $title = Tools::getValue('title');
                $content = Tools::getValue('content');
                $id_lang = $lang['id_lang'];

                $product_tab->title[$id_lang] = $title[$id_lang];
                $product_tab->content[$id_lang] = $content[$id_lang];
            }

            $product_tab->active = (int)Tools::getValue('active');
            $product_tab->product_id = Tools::getValue('id_product');
            $product_tab->tab_content_type = Tools::getValue('tab_content_type');
            $product_tab->save();

            $this->ajaxProcessGetProductTabList();
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    public function ajaxProcessActiveProductTab()
    {
        try {
            $product_tab = new ProductTab(Tools::getValue('id_product_tab'));
            $product_tab->active = (int)Tools::getValue('active');
            echo $product_tab->save();
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    public function ajaxProcessPositionProductTab()
    {
        try {
            foreach (Tools::getValue('product_tab_positions') as $id_product_tab => $position) {
                $product_tab = new ProductTab((int)$id_product_tab);
                $product_tab->position = (int)$position;
                if (!$product_tab->save()) {
                    echo 0;
                    exit;
                }
            }

            echo 1;
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    public function ajaxProcessSetTabTypeProductTab()
    {
        try {
            $id_product_tab_settings = ProductTabSettings::getIdByProductId(Tools::getValue('id_product'));
            $product_tab_settings = new ProductTabSettings($id_product_tab_settings);
            $product_tab_settings->tab_type = Tools::getValue('tab_type');
            $product_tab_settings->product_id = (int)Tools::getValue('id_product');
            echo $product_tab_settings->save();
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }
}
