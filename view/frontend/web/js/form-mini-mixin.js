define(['jquery'], function ($) {
    'use strict';

    if (!window.searchAutocomplete) {
        window.searchAutocomplete = { enable: false };
    }

    let quickSearchWidgetMixin = null;
    if (window.searchAutocomplete.enable) {
        quickSearchWidgetMixin = {
            options: {
                template:
                    '<li class="<%- data.row_class %>" id="qs-option-<%- data.index %>" role="option">' +
                    '<a href="<%- data.url %>">' +
                    '<span class="qs-option-name">' +
                    ' <%- data.title %>' +
                    '</span>' +
                    '</a>' +
                    '</li>',
            }
        };
    }

    return function (targetWidget) {
        if (window.searchAutocomplete.enable) {
            $.widget('mage.quickSearch', targetWidget, quickSearchWidgetMixin);
        }

        return $.mage.quickSearch;
    };
});
