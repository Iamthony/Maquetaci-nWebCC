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

(function ($) {
    $(function () {
        var default_tabs = $('.tabs').get();
        $('.tabs').after('<div id="producttabs"></div>');
        $('.tabs').remove();

        if ($(default_tabs).length) {

            var tabs = [];
            $.merge(tabs, producttabs);

            if (tabs.length > 0) {
                switch (product_tab_settings.tab_type) {

                    case "vertical":

                        var tpl = $('<div id="vertical">');
                        for (var i in tabs) {
                            tpl.append('<section>'
                                + '<h3>'
                                + tabs[i].title
                                + '</h3>'
                                + '<div>'
                                + tabs[i].content
                                + '</div>'
                                + '</section>');
                        }

                        $('#producttabs').append(tpl);

                        break;
                    case "accordion":

                        var tpl = $('<div id="accordion">');
                        for (var i in tabs){

                            tpl.append('<h1>'
                                + tabs[i].title
                                + '</h1>');

                            tpl.append('<div>'
                                + tabs[i].content
                                + '</div>');
                        }

                        $('#producttabs').append(tpl);
                        $( "#accordion" ).accordion();

                        break;
                    default:
                    case "horizontal":

                        var tpl = $('<div class="tabs">');
                        tpl.append('<ul class="nav nav-tabs"></ul>');
                        tpl.append('<div class="tab-content" id="tab-content"></div>');
                        for (var i in tabs){

                            tpl.find('ul.nav-tabs')
                                .append('<li class="nav-item">' +
                                    '<a class="nav-link ' + (i == 0 ? 'active' : '') + '" data-toggle="tab" href="#product_tab-' + i + '">'
                                    + tabs[i].title
                                    + '</a>' +
                                    '</li>');

                            tpl.find('div.tab-content')
                                .append('<div class="tab-pane fade in ' + (i == 0 ? 'active' : '') + '" id="product_tab-' + i + '">'
                                    + tabs[i].content
                                    + '</div>');
                        }
                        $('#producttabs').append(tpl);
                        $('.tabs').tabs();

                        break;
                }

            }

        } else {
            throw new Error(
                'Module Product Tabs: Default tabs of Classic template not found. '
                + 'Add they to product page.'
            );
        }
    });
})(jQuery);