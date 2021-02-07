{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="product-quantity">
        <div class="row">
            {if !$configuration.is_catalog}
                <div class="col-xs-12 col-sm-3">
                    <b style="font-weight: 600; color: black; font-size: 13px;">
                        Cantidad:</b>
                </div>
                <div class="col-xs-12 col-sm-8 ml-3 mb-2">
                    {block name='product_quantity'}
                        <div class="product-quantity row align-items-center no-gutters ml-n3">
                            <label for="quantity_wanted"
                                class="quantity__label col-auto">{l s='' d='Shop.Theme.Catalog'}</label>
                            <div class="qty col-auto">
                                <input type="number" name="qty" id="quantity_wanted" value="{$product.quantity_wanted}"
                                    class="input-group" min="{$product.minimal_quantity}"
                                    aria-label="{l s='Quantity' d='Shop.Theme.Actions'}"
                                    {if isset($product.product_url)}data-update-url="{$product.product_url}" {/if}>
                            </div>
                        </div>
                    {/block}
                </div>
                {block name='product_availability'}
                    <span id="product-availability">
                        {if $product.show_availability && $product.availability_message}
                            {if $product.availability == 'available'}
                                <i class="material-icons rtl-no-flip product-available text-success">&#xE5CA;</i>
                            {elseif $product.availability == 'last_remaining_items'}
                                <i class="material-icons product-last-itemstext-warning">&#xE002;</i>
                            {else}
                                <i class="material-icons product-unavailable text-danger">&#xE14B;</i>
                            {/if}
                            {$product.availability_message}
                        {/if}
                    </span>
                {/block}

                {block name='product_minimal_quantity'}
                    <p class="product-minimal-quantity">
                        {if $product.minimal_quantity > 1}
                            {l
                                     s='The minimum purchase order quantity for the product is %quantity%.'
                                     d='Shop.Theme.Checkout'
                                     sprintf=['%quantity%' => $product.minimal_quantity]
                                     }
                        {/if}
                    </p>
                {/block}
            {/if}
        </div>
</div>