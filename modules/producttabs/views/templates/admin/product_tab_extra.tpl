{**
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
*}

<div class="container" id="product_tab">
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <button type="button" class="btn btn-primary-outline sensitive add" id="get_product_tab_form" style="margin-top: 35px;">
                    <i class="material-icons">add_circle</i> {l s='Add a product tab' mod='producttabs'}
                </button>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="form-group">
                <h2>
                    {l s='Tab types' mod='producttabs'}:
                    <span class="help-box" data-toggle="popover" data-content="Tab types." data-original-title="" title=""></span>
                </h2>
                {assign var="selected_tab_type" value=$product_tab_settings->tab_type}
                <select name="tab_type" class="form-control" id="tab_type">
                    {foreach $tab_types as $key => $tab_type}
                        <option value="{$key}" {if !empty($selected_tab_type) && $selected_tab_type == $key || $product_tab_default_type == $key}selected{/if}>{$tab_type}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="edit-spinner">
        <div class="col-lg-5"></div>
        <div class="col-lg-1">
            <div class="js-spinner spinner btn-primary-reverse onclick pull-left m-r-1"></div>
        </div>
        <div class="col-lg-5"></div>
    </div>
    <div class="row">
        <div class="col-lg-12" id="product_tab_body">

        </div>
    </div>
</div>
<script type="application/javascript">
    (function($){
        $(function () {

            $('#module_producttabs h2.name').text('Module Product Tabs from BELVG');

            productTab = {

                product_tab_get_list: '{$product_tab_get_list}',
                product_tab_get_form: '{$product_tab_get_form}',
                save_product_tab: '{$save_product_tab}',
                delete_product_tab: '{$delete_product_tab}',
                active_product_tab: '{$active_product_tab}',
                position_product_tab: '{$position_product_tab}',
                get_product_tab: '{$get_product_tab}',
                tab_type_product_tab: '{$tab_type_product_tab}',
                id_product: '{$id_product|intval}',
                get_product_tab_form: $('#get_product_tab_form'),
                product_tab_body: $('#product_tab_body'),
                tab_type: $('#tab_type'),

                init: function () {

                    productTab.updateProductTabList();
                    productTab.get_product_tab_form.on('click', function () {
                        productTab.getProductTab(0);
                    });
                    productTab.tab_type.on('change', productTab.setTabType);
                    productTab.setTabType();

                },
                updateProductTabList: function () {

                    var data = {
                        id_product: productTab.id_product,
                    };

                    productTab.showSinner();
                    productTab.query(productTab.product_tab_get_list, data, function (data) {
                        productTab.product_tab_body.html(data);
                        productTab.hideSinner();
                        productTab.setSortable();
                    });
                },
                getProductTab: function (id_product_tab) {

                    var data = {
                        id_product_tab: id_product_tab,
                    };

                    productTab.showSinner();
                    productTab.query(productTab.product_tab_get_form, data, function (data) {
                        productTab.product_tab_body.html(data);
                        productTab.hideSinner();
                    });
                },
                saveProductTab: function (id_product_tab, title, content, active, groupBox, tab_content_type) {
                    var data = {
                        id_product: productTab.id_product,
                        id_product_tab: id_product_tab,
                        title: title,
                        content: content,
                        active: active,
                        groupBox: groupBox,
                        tab_content_type: tab_content_type,
                    };

                    productTab.showSinner();
                    productTab.query(productTab.save_product_tab, data, function (data) {
                        productTab.product_tab_body.html(data);
                        productTab.hideSinner();
                        productTab.setSortable();
                    });
                },
                deleteProductTab: function (id_product_tab) {
                    var data = {
                        id_product_tab: id_product_tab,
                        id_product: productTab.id_product,
                    };

                    productTab.showSinner();
                    productTab.query(productTab.delete_product_tab, data, function (data) {
                        productTab.product_tab_body.html(data);
                        productTab.hideSinner();
                        productTab.setSortable();
                        showSuccessMessage(translate_javascripts['Delete']);
                    });
                },
                setSwitch: function () {
                    $('[data-toggle="active-switch"]').each(function () {
                        var e = $(this);
                        if (void 0 == e.data("activated")) {
                            e.data("activated", !0);
                            var t = e.prop("checked") ? "-checked" : "";
                            e.wrap('<div class="switch-input ' + t + '"></div>');
                            var i = e.parent();
                            i.addClass(e.attr("class")), e.on("change", function () {
                                i.toggleClass("-checked", e.prop("checked"))
                            }), i.click(function (t) {
                                if (t.srcElement == i[0] || void 0 == t.srcElement) return e.prop("checked", !e.prop("checked")), !1
                            })
                        }
                    });
                },
                setActive: function (id_product_tab, active) {
                    var data = {
                        id_product_tab: id_product_tab,
                        active: active,
                    };

                    productTab.query(productTab.active_product_tab, data, function (data) {
                        if(parseInt(data)) {
                            showSuccessMessage(translate_javascripts['Form update success']);
                        } else {
                            showErrorMessage(translate_javascripts['Form update errors']);
                        }
                    });
                },
                setPosition: function (product_tab_positions) {
                    var data = {
                        product_tab_positions: product_tab_positions,
                    };

                    productTab.query(productTab.position_product_tab, data, function (data) {
                        if(parseInt(data)) {
                            showSuccessMessage(translate_javascripts['Form update success']);
                        } else {
                            showErrorMessage(translate_javascripts['Form update errors']);
                        }
                    });
                },
                setTabType: function () {
                    var data = {
                        id_product: productTab.id_product,
                        tab_type: productTab.tab_type.val(),
                    };

                    productTab.query(productTab.tab_type_product_tab, data, function (data) {
                        if(parseInt(data)) {
                            showSuccessMessage(translate_javascripts['Form update success']);
                        } else {
                            showErrorMessage(translate_javascripts['Form update errors']);
                        }
                    });

                },
                showSinner: function () {
                    productTab.productTabBodyClean();
                    $('#edit-spinner').show();
                },
                hideSinner: function () {
                    $('#edit-spinner').hide();
                },
                setSortable: function () {
                    $('#sortable').sortable({
                        stop: function( event, ui ) {
                            var position = 0;
                            var product_tab_positions = {};
                            $('#product_tab_list tbody tr').each(function () {
                                var id_product_tab = $(this).attr('data-id_product_tab');
                                product_tab_positions[id_product_tab] = ++position;
                            });

                            productTab.setPosition(product_tab_positions);
                        }
                    });
                    $('#sortable').disableSelection();
                },
                productTabBodyClean: function () {
                    productTab.product_tab_body.html('');
                },
                query: function (path, data, success) {
                    $.ajax({
                        url: path,
                        data: data,
                        success: success,
                        dataType: 'html',
                        method: 'POST',
                    });
                }
            };

            productTab.init();
        });
    })(jQuery);
</script>