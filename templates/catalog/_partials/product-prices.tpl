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
{if $product.show_price}
    <div class="product-prices">
        <div class="container ml-n1">
            <div class="row">
                <div class="col-md-12">
                    <div class="current-price">
                        <div class="d-flex flex-row bd-highlight mb-3 mt-n2">
                            <div class="p-2 bd-highlight">
                                <span
                                    class="current-price-display price{if $product.has_discount} current-price-discount{/if}">{$product.price}
                                </span>
                            </div>
                            <div class="p-2 bd-highlight">
                                {block name='product_price'}
                                    <div
                                        class="product__product-price product-price {if $product.has_discount}has-discount{/if}">
                                        {block name='product_discount'}
                                            {if $product.has_discount}
                                                <p class="font-weight-bold mt-3 mb-n1" style="font-size: 10px;">PVP</p>
                                                <span class="product-discount">
                                                    {hook h='displayProductPriceBlock' product=$product type="old_price"}
                                                    <span class="regular-price">{$product.regular_price}</span>
                                                </span>
                                            {/if}
                                        {/block}

                                        {block name='product_unit_price'}
                                            {if $displayUnitPrice}
                                                <p class="product-unit-price sub">
                                                    {l s='(%unit_price%)' d='Shop.Theme.Catalog' sprintf=['%unit_price%' => $product.unit_price_full]}
                                                </p>
                                            {/if}
                                        {/block}
                                    </div>
                                {/block}
                            </div>
                            <div class="mr-auto p-2 bd-highlight mt-1 dto">
                                {if $product.has_discount}
                                    {if $product.discount_type === 'percentage'}
                                        <p class="font-weight-bold p-1 mt-2 mb-n1" style="font-size: 10px;">DTO.</p>
                                        <span
                                            class="discount discount-percentage">{l s='%percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]}</span>
                                    {else}
                                        <span class="discount discount-amount">
                                            {l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]}
                                        </span>
                                    {/if}
                                {/if}
                            </div>
                            <div class="price-count p-2 bd-hightlight d-none d-sm-block mt-2">
                                ¾
                            </div>
                        </div>
                    </div>
                    <div class="ml-1 d-flex flex-row bd-highlight">
                        <div class="mt-n4 bd-hightlight">
                            <img src="https://i.imgur.com/rNmtmpK.png" class="img-fluid notallowed">
                        </div>
                        <div class="mt-n3 ml-1 bd-hightlight reviews">
                            ¡Sé el primero en comentar!
                        </div>
                        <div class="mt-n3 ml-1 bd-hightlight d-none d-sm-block" style="font-size: 12px; margin-top: -20px!important;">
                            |
                        </div>
                        <div class="mt-n3 ml-1 bd-hightlight reviews d-none d-sm-block">
                            Review
                        </div>
                    </div>
                    <div class="mt-n1 mb-n3 d-flex p-2 bd-highlight">
                        <p class="text-left">Vendido y enviado por <b>WebImpacto</b>
                            <a href="#" style="color: #e1600a"> ¿Qué
                                es esto?</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>


        {block name='product_without_taxes'}
            {if $priceDisplay == 2}
                <p class="product-without-taxes">
                    {l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $product.price_tax_exc]}</p>
            {/if}
        {/block}

        {block name='product_pack_price'}
            {if $displayPackPrice}
                <p class="product-pack-price">
                    <span>{l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}</span>
                </p>
            {/if}
        {/block}

        {block name='product_ecotax'}
            {if $product.ecotax.amount > 0}
                <p class="price-ecotax">
                    {l s='Including %amount% for ecotax' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.ecotax.value]}
                    {if $product.has_discount}
                        {l s='(not impacted by the discount)' d='Shop.Theme.Catalog'}
                    {/if}
                </p>
            {/if}
        {/block}

        {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}

        {* <div class="tax-shipping-delivery-label">
            {if !$configuration.taxes_enabled}
                {l s='No tax' d='Shop.Theme.Catalog'}
            {elseif $configuration.display_taxes_label}
                {$product.labels.tax_long}
            {/if}
            {hook h='displayProductPriceBlock' product=$product type="price"}
            {hook h='displayProductPriceBlock' product=$product type="after_price"}
            {if $product.additional_delivery_times == 1}
                {if $product.delivery_information}
                    <span class="delivery-information">{$product.delivery_information}</span>
                {/if}
            {elseif $product.additional_delivery_times == 2}
                {if $product.quantity > 0}
                    <span class="delivery-information">{$product.delivery_in_stock}</span>
                    {* Out of stock message should not be displayed if customer can't order the product. *}
    {* {elseif $product.quantity <= 0 && $product.add_to_cart_url}
                    <span class="delivery-information">{$product.delivery_out_stock}</span>
                {/if}
            {/if}
        </div>  *}
    </div>
{/if}