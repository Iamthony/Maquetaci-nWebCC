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
{extends file=$layout}

{block name='head_seo' prepend}
    <link rel="canonical" href="{$product.canonical_url}">
{/block}

{block name='content'}

    <section id="main">
        <div class="row">
            <div class="col-lg-6">
                {block name='page_content_container'}
                    <section class="page-content--product" id="content">
                        {block name='page_content'}

                            {block name='product_flags'}
                                {include file='catalog/_partials/product-flags.tpl'}
                            {/block}

                            {block name='product_cover_thumbnails'}
                                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
                            {/block}

                        {/block}
                    </section>
                {/block}
            </div>
            <div class="col-lg-6">
                {block name='page_header_container'}
                    {block name='page_header'}
                        <h1 class="h1">{block name='page_title'}{$product.name}{/block}</h1>
                    {/block}
                {/block}

                {block name='product_prices'}
                    {include file='catalog/_partials/product-prices.tpl'}
                {/block}

                {block name='product_additional_info'}
                    {include file='catalog/_partials/product-additional-info.tpl'}
                {/block}

                <div class="product-information">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-txt">
                                <b style="font-weight: 600; color: black;">
                                    Descripción:</b>
                            </div>
                            <div class="col-xs-12 col-sm-9 col-txt">
                                {block name='product_description_short'}
                                    <div id="product-description-short-{$product.id}">{$product.description_short nofilter}
                                    </div>
                                {/block}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-txt">
                                <b style="font-weight: 600; color: black;">Envío:</b>
                            </div>
                            <div class="col-xs-12 col-sm-9 col-txt ml-n1 mb-2">
                                  Desde {$product.additional_shipping_cost}<strong> GRATIS</strong> con webImpacto Premium
                            </div>
                        </div>

                        {block name='product_quantity'}
                            {include file='catalog/_partials/product-quantity.tpl'}
                        {/block}

                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-txt">
                                <b style="font-weight: 600; color: black;">
                                    Tamaño:</b>
                            </div>
                            <div class="col-xs-12 col-sm-9 col-txt mb-2">
                                {block name='product_variants'}
                                    {include file='catalog/_partials/product-variants.tpl'}
                                {/block}
                            </div>
                        </div>

                        {block name='promotion'}
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-txt">
                                    <b style="font-weight: 600; color: black;">Promoción:</b>
                                </div>
                                <div class="col-xs-12 col-sm-9">
                                    <div class="tabPromo font-weight-bold mb-2" role="tab">
                                        <a href="" id="offert">
                                            Ofertas especiales de la semana
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-txt">
                                    <b style="font-weight: 600; color: black;">Disponibilidad:</b>
                                </div>
                                <div class="col-xs-12 col-sm-9">
                                    <div class="accordion">
                                        <div class="contentBx">
                                            <div class="label-wrapper font-weight-bold" style="font-size: 14px;">¡En
                                                stock
                                                {$product.quantity} artículos! ¡Recíbelo mañana!</div>
                                            <div class="content">
                                                <p style="font-size: 13px;">Plazo de entrega estimado para envíos a
                                                    península
                                                </p>
                                            </div>
                                        </div>
                                        <div class="contentBx">
                                            <div class="label-wrapper2 shadow-sm mb-2 bg-white rounded p-1"
                                                style="font-size: 13px;">
                                                ¿Recoges en tienda?
                                            </div>
                                            <div class="content2">
                                                <p style="font-size: 13px;">*Realiza tu pedido ahora y te avisamos cuando
                                                    esté
                                                    preparado.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-txt">
                                    <b style="font-weight: 600; color: black;">Financiación:</b>
                                </div>
                                <div class="col-xs-12 col-sm-9">
                                    <div class="ficha-product py-2 px-1" style="font-size: 13px;">
                                        <div class="d-flex bd-highlight" style="height: 20px;">
                                            <div class="bd-highlight">
                                                <img src="https://i.imgur.com/dW0YMo3.png">
                                                <span>
                                                    <b> Aplazame </b>
                                                </span>
                                            </div>
                                            <div class="mr-auto bd-highlight">De 2 a 30 Meses (inmediata)</div>
                                            <div class="bd-highlight">
                                                <p style="color:#f60">+Info
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-actions">
                                {block name='product_buy'}
                                    <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                                        <input type="hidden" name="token" value="{$static_token}">
                                        <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                                        <input type="hidden" name="id_customization" value="{$product.id_customization}"
                                            id="product_customization_id">



                                        {block name='product_pack'}
                                            {if $packItems}
                                                <section class="product-pack mb-4">
                                                    <p class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</p>
                                                    {foreach from=$packItems item="product_pack"}
                                                        {block name='product_miniature'}
                                                            {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack}
                                                        {/block}
                                                    {/foreach}
                                                </section>
                                            {/if}
                                        {/block}

                                        {block name='product_discounts'}
                                            {include file='catalog/_partials/product-discounts.tpl'}
                                        {/block}

                                        {block name='product_add_to_cart'}
                                            {include file='catalog/_partials/product-add-to-cart.tpl'}
                                        {/block}


                                        {block name='product_refresh'}
                                            {if !isset($product.product_url)}
                                                <input class="product-refresh ps-hidden-by-js" name="refresh" type="submit"
                                                    value="{l s='Refresh' d='Shop.Theme.Actions'}">
                                            {/if}
                                        {/block}
                                    </form>
                                {/block}
                            </div>
                            {* {block name='hook_display_reassurance'}
                                    {hook h='displayReassurance'}
                                {/block} *}
                            </div>
                        </div>
                    </div>
                </div>
            {/block}

            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        {block name='product_tabs'}
                            {include file='catalog/_partials/product-tabs.tpl'}
                        {/block}
                    </div>
                </div>
            </div>


            {block name='product_accessories'}
                {if $accessories}
                    <section class="product-accessories mt-3">
                        <p class="products-section-title">{l s='You might also like' d='Shop.Theme.Catalog'}</p>
                        <div class="products">
                            {foreach from=$accessories item="product_accessory"}
                                {block name='product_miniature'}
                                    {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory}
                                {/block}
                            {/foreach}
                        </div>
                    </section>
                {/if}
            {/block}

            {block name='product_footer'}
                {hook h='displayFooterProduct' product=$product category=$category}
            {/block}

            {block name='product_images_modal'}
                {include file='catalog/_partials/product-images-modal.tpl'}
            {/block}

            {block name='page_footer_container'}
                <footer class="page-footer">{block name='page_footer'}{/block}</footer>
            {/block}
        </section>

    {/block}