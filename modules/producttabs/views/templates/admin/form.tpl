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

<!-- Modal -->
<div id="product_tab_form">
    <div role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{l s='New product tab' mod='producttabs'}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <h2>
                        {l s='Tab types' mod='producttabs'}<span style="color: red;">*</span>
                        <span class="help-box" data-toggle="popover" data-content="Tab types." data-original-title="" title=""></span>
                    </h2>
                    {assign var="selected_tab_type" value=$product_tab->tab_content_type}
                    <select name="tab_content_type" class="form-control" id="tab_content_type">
                        {foreach $tab_content_types as $key => $tab_content_type}
                            <option value="{$key}" {if !empty($selected_tab_type) && $selected_tab_type == $key}selected{/if}>{$key}</option>
                        {/foreach}
                    </select>
                </div>
                {foreach $languages as $lang}
                    <div class="lang_{$lang.iso_code}">
                        <div class="form-group">
                            <h2>{l s='Title' mod='producttabs'}<span style="color: red;">*</span></h2>
                            <input type="text" name="title" data-id_lang="{$lang.id_lang|intval}" data-iso_code="{$lang.iso_code}" class="product_tab_title form-control" placeholder="Title" value="{$product_tab->title[$lang.id_lang]}">
                        </div>
                    </div>
                {/foreach}
                <div id="product_tab_fields">
                    {foreach $languages as $lang}
                        <div class="lang_{$lang.iso_code}">
                            <div class="form-group">
                                <h2>Content</h2>
                                <div class="tab-content bordered">
                                    <textarea name="content" data-id_lang="{$lang.id_lang|intval}" class="product_tab_content form-control autoload_rte">{$product_tab->content[$lang.id_lang]}</textarea>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="fixed-width-xs">
						<span class="title_box">
							<input type="checkbox" name="checkme" id="checkme"
                                   onclick="checkDelBoxes(this.form, 'groupBox[]', this.checked)">
						</span>
                                </th>
                                <th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='producttabs'}</span></th>
                                <th>
						<span class="title_box">
                            {l s='Group name' mod='producttabs'}
						</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $groupBoxes as $key => $groupBox}
                                <tr>
                                    <td>
                                        <input type="checkbox" name="groupBox[]" data-id_group="{$key|intval}" class="groupBox" id="{$groupBox.name}" value="1"
                                               {if $groupBox.value}checked="checked"{/if}>
                                    </td>
                                    <td>{$key}</td>
                                    <td>
                                        <label for="{$groupBox.name}">{$groupBox.name}</label>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <label for="product_tab_active">{l s='Active' mod='producttabs'}</label>
                    <input name="active" id="product_tab_active" data-toggle="active-switch" type="checkbox" {if $product_tab->active}value="1" checked="checked"{else}value="0"{/if}>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="dismiss_product_tab_form">{l s='Close' mod='producttabs'}</button>
                <button type="button" class="btn btn-primary" data-id_product_tab="{$product_tab->id|intval}" id="save_product_tab">{l s='Save tab' mod='producttabs'}</button>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    (function ($) {
        $(function () {

            //set switch to active-disactive button
            productTab.setSwitch();

            //if product_tab is active set switch in active state
            $('[data-toggle="active-switch"]').on('change', function () {

                var product_tab_form = $('#product_tab_form');
                var product_tab_active = product_tab_form.find('#product_tab_active');
                if ($(this).parent().hasClass('-checked')) {
                    product_tab_active.attr('checked', true);
                    product_tab_active.val(1);
                } else {
                    product_tab_active.attr('checked', false);
                    product_tab_active.val(0);
                }
            });

            //set tiny editor to textarea field
            tinySetup({
                editor_selector :"autoload_rte",
                setup : function(ed) {
                    ed.on('keydown', function(ed, e) {
                        tinyMCE.triggerSave();
                        textarea = $('#'+tinymce.activeEditor.id);
                        var max = textarea.parent('div').find('span.counter').data('max');
                        if (max != 'none')
                        {
                            count = tinyMCE.activeEditor.getBody().textContent.length;
                            rest = max - count;
                            if (rest < 0)
                                textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum '+ max +' caractères : '+rest+'</span>');
                            else
                                textarea.parent('div').find('span.counter').html(' ');
                        }
                    });
                    ed.on('blur', function(ed) {
                        tinyMCE.triggerSave();
                    });
                }
            });

            $('#form_switch_language').on('change', function () {
                var product_tab_form = $('#product_tab_form');
                var form_group_lang = product_tab_form.find('.lang_' + $(this).val());
                product_tab_form.find('[class^="lang_"]').hide();
                form_group_lang.show();
            });
            $('#form_switch_language').change();

            //set handler on dismiss button of form
            $('#dismiss_product_tab_form').on('click', function () {
                try {
                    productTab.updateProductTabList();
                } catch (e) {
                    console.trace(e);
                }
            });

            $('#save_product_tab').on('click', function () {
                try {

                    var product_tab_form = $('#product_tab_form');

                    var title = {};
                    var id_lang = 0;
                    var is_empty = false;
                    product_tab_form.find('input[name="title"]').each(function () {
                        id_lang = $(this).data('id_lang');
                        var iso_code = $(this).data('iso_code');
                        title[id_lang] = $(this).val();

                        if (!title[id_lang].length) {
                            showErrorMessage('Fill "Title" in ' + iso_code.toUpperCase() + '.');
                            is_empty = true;
                        }
                    });

                    if (is_empty) {
                        return;
                    }

                    var content = {};
                    product_tab_form.find('textarea[name="content"]').each(function () {
                        id_lang = $(this).data('id_lang');
                        content[id_lang] = $(this).val();
                    });

                    var active = $('#product_tab_active').val();

                    var groupBox = {};
                    product_tab_form.find('input[name="groupBox[]"]:checked').each(function () {
                        var id_group = $(this).data('id_group');
                        groupBox[id_group] = id_group;
                    });

                    var id_product_tab = $(this).data('id_product_tab');
                    var tab_content_type = $('#tab_content_type').val();

                    productTab.saveProductTab(id_product_tab, title, content, active, groupBox, tab_content_type);

                } catch (e) {
                    console.trace(e);
                }
            });

            $('#tab_content_type').on('change', function () {

                switch ($(this).val()) {

                    case 'contact_form':
                    case 'description':
                    case 'product_details':
                    case 'product_attachments':
                        $('#product_tab_fields').hide();
                        break;
                    case 'content':
                        $('#product_tab_fields').show();
                        break;
                }
            });
            $('#tab_content_type').change();

        });
    })(jQuery);
</script>