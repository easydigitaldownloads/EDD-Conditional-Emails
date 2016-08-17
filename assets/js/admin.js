/*global jQuery, document*/
jQuery(document).ready(function ($) {
    'use strict';

    $('select[id="edd-conditional-email-condition"]').change(function () {
        var selectedItem = $('select[id="edd-conditional-email-condition"] option:selected');

        if (selectedItem.val() === 'purchase-status') {
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'table-row');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'table-row');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'none');

            $('.edd-conditional-email-tags-list span').each(function() {
                if($(this).hasClass('show-on-purchase-status')) {
                    $(this).css('display', 'block');
                } else {
                    $(this).css('display', 'none');
                }
            });
        } else if (selectedItem.val() === 'abandoned-cart') {
            $('select[id="edd-conditional-email-status-from"]').val('pending');
            $('select[id="edd-conditional-email-status-to"]').val('abandoned');
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'none');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'none');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'none');

            $('.edd-conditional-email-tags-list span').each(function() {
                if($(this).hasClass('show-on-abandoned-cart')) {
                    $(this).css('display', 'block');
                } else {
                    $(this).css('display', 'none');
                }
            });
        } else if (selectedItem.val() === 'purchase-amount') {
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'none');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'none');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'table-row');

            $('.edd-conditional-email-tags-list span').each(function() {
                if($(this).hasClass('show-on-purchase-amount')) {
                    $(this).css('display', 'block');
                } else {
                    $(this).css('display', 'none');
                }
            });
        } else if (selectedItem.val() === 'pending-payment') {
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'none');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'none');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'none');

            $('.edd-conditional-email-tags-list span').each(function() {
                if($(this).hasClass('show-on-pending-payment')) {
                    $(this).css('display', 'block');
                } else {
                    $(this).css('display', 'none');
                }
            });
        } else if (selectedItem.val() === 'license-upgrade') {
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'none');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'none');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'none');

            $('.edd-conditional-email-tags-list span').each(function() {
                if($(this).hasClass('show-on-license-upgrade')) {
                    $(this).css('display', 'block');
                } else {
                    $(this).css('display', 'none');
                }
            });
        } else if (selectedItem.val() === 'license-renewal') {
            $('select[id="edd-conditional-email-status-from"]').closest('tr').css('display', 'none');
            $('select[id="edd-conditional-email-status-to"]').closest('tr').css('display', 'none');
            $('input[id="edd-conditional-email-minimum-amount"]').closest('tr').css('display', 'none');

            $('.edd-conditional-email-tags-list span').each(function() {
                if($(this).hasClass('show-on-license-renewal')) {
                    $(this).css('display', 'block');
                } else {
                    $(this).css('display', 'none');
                }
            });
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
