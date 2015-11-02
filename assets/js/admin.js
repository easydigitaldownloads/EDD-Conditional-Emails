/*global jQuery, document*/
jQuery(document).ready(function ($) {
    'use strict';

    $('select[id="edd-conditional-email-condition"]').change(function () {
        var selectedItem = $('select[id="edd-conditional-email-condition"] option:selected');

        if (selectedItem.val() === 'purchase-status') {
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'table-row');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'table-row');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'none');
        } else if (selectedItem.val() === 'abandoned-cart') {
            $('select[id="edd-conditional-email-status-from"]').val('pending');
            $('select[id="edd-conditional-email-status-to"]').val('abandoned');
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'none');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'none');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'none');
        } else if (selectedItem.val() === 'purchase-amount') {
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'none');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'none');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'table-row');
        }
    }).change();

    $('select[id="edd-conditional-email-send-to"]').change(function () {
        var selectedItem = $('select[id="edd-conditional-email-send-to"] option:selected');

        if (selectedItem.val() === 'custom') {
            $('input[id="edd-conditional-email-custom-email"]').closest('tr').css('display', 'table-row');
        } else {
            $('input[id="edd-conditional-email-custom-email"]').closest('tr').css('display', 'none');
        }
    }).change();
});
