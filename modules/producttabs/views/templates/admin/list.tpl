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

{if !empty($producttabs)}
    <table class="table table-striped" id="product_tab_list">
        <thead>
        <tr>
            <th width="5%"></th>
            <th width="65%">{l s='Title' mod='producttabs'}</th>
            <th width="10%">{l s='Active' mod='producttabs'}</th>
            <th width="10%">{l s='Position' mod='producttabs'}</th>
            <th width="10%">&nbsp;</th>
            <th width="10%">&nbsp;</th>
        </tr>

        </thead>
        <tbody id="sortable">
        {foreach $producttabs as $key => $product_tab}
            <tr class="ui-state-default" data-position="{$key|intval}" data-id_product_tab="{$product_tab.id_product_tab|intval}">
                <td>{$product_tab.id_product_tab|intval}</td>
                <td>{$product_tab.title}</td>
                <td>
                    <input name="active" class="product_tab_active" data-toggle="active-switch" type="checkbox" data-id_product_tab="{$product_tab.id_product_tab|intval}" {if $product_tab.active}value="1" checked="checked"{else}value="0"{/if}>
                </td>
                <td><i style="font-size: 30px; cursor: move;">&#8597;</i></td>
                <td>
                    <button type="button" class="btn btn-primary-outline sensitive edit_product_tab" data-id_product_tab="{$product_tab.id_product_tab|intval}">
                        <i class="material-icons">edit_circle</i>&nbsp;
                    </button>
                </td>
                <td>
                    <button type="button" class="btn btn-primary-outline sensitive delete_product_tab" data-id_product_tab="{$product_tab.id_product_tab|intval}">
                        <i class="material-icons">delete_circle</i>&nbsp;
                    </button>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <div class="modal fade in" id="product_tab_deletion_modal" tabindex="-1" style="display: none;">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">{l s='Delete products' mod='producttabs'}?</h4>
                </div>

                <div class="modal-body">
                    {l s='These products will be deleted for good. Please confirm.' mod='producttabs'}
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-tertiary-outline btn-lg" data-dismiss="modal">{l s='Close' mod='producttabs'}</button>
                    <button type="button" value="confirm" class="btn btn-primary btn-lg">
                        {l s='Delete now' mod='producttabs'}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        (function ($) {
            $(function () {

                //set switch to active-disactive button
                productTab.setSwitch();

                $('[data-toggle="active-switch"]').on('change', function () {

                    var id_product_tab = $(this).data('id_product_tab');

                    if ($(this).parent().hasClass('-checked')) {
                        productTab.setActive(id_product_tab, 1);
                    } else {
                        productTab.setActive(id_product_tab, 0);
                    }
                });

                $('.edit_product_tab').on('click', function () {

                    try {
                        var id_product_tab = $(this).data('id_product_tab');
                        productTab.getProductTab(id_product_tab);
                    } catch (e) {
                        console.trace(e);
                    }
                });

                $('.delete_product_tab').on('click', function () {

                    var id_product_tab = $(this).data('id_product_tab');
                    // Confirmation popup and callback...
                    $('#product_tab_deletion_modal').modal('show');
                    $('#product_tab_deletion_modal button[value="confirm"]').off('click');
                    $('#product_tab_deletion_modal button[value="confirm"]').on('click', function () {
                        $('#product_tab_deletion_modal').modal('hide');

                        setTimeout(function () {
                            try {
                                productTab.deleteProductTab(id_product_tab);
                            } catch (e) {
                                console.trace(e);
                            }
                        }, 500);
                    });
                });
            });
        })(jQuery);
    </script>
{/if}
