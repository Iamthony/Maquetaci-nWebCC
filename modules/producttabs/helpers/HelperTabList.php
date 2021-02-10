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

class HelperTabList
{

    const TMP_DIR = _PS_MODULE_DIR_ . 'producttabs/views/templates/';

    public static function render($product_id, $tabs)
    {
        $context = Context::getContext();

        foreach ($tabs as &$tab) {
            if (!isset($tab['tab_content_type'])) {
                continue;
            }

            switch ($tab['tab_content_type']) {
                case 'contact_form':
                    $file = self::TMP_DIR . 'front/contact.tpl';

                    $contact_form = $context->smarty->createTemplate($file);
                    $tab['content'] = $contact_form->fetch();
                    break;
                case 'description':
                    $product = new Product($product_id, false, $context->language->id);
                    $tab['content'] = $product->description;

                    break;
                case 'product_details':
                    $file = self::TMP_DIR . 'front/product-details.tpl';

                    $product_controller = new ProductController();
                    $product_controller->init();


                    $product_details = $context->smarty->createTemplate($file);
                    $product_details->assign(array(
                        'product' => $product_controller->getTemplateVarProduct(),
                    ));

                    $tab['content'] = $product_details->fetch();
                    break;
                case 'product_attachments':
                    $file = self::TMP_DIR . 'front/product-attachments.tpl';

                    $product_controller = new ProductController();
                    $product_controller->init();

                    $product_details = $context->smarty->createTemplate($file);
                    $product_details->assign(array(
                        'product' => $product_controller->getTemplateVarProduct(),
                    ));

                    $tab['content'] = $product_details->fetch();
                    break;
            }
        }
        return $tabs;
    }
}
