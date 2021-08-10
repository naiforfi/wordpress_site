/**
 * Document nasa-core ready
 */
jQuery(document).ready(function($) {
"use strict";

/**
 * 360 Degree Popup
 */
$('body').on('nasa_before_popup_360_degree', function() {
    $.magnificPopup.close();

    $.magnificPopup.open({
        mainClass: 'my-mfp-zoom-in',
        items: {
            src: '<div class="nasa-product-360-degree"></div>',
            type: 'inline'
        },
        closeMarkup: '<button title="' + $('input[name="nasa-close-string"]').val() + '" type="button" class="mfp-close nasa-stclose-360">×</button>',
        callbacks: {
            beforeClose: function() {
                this.st.removalDelay = 350;
            },
            afterClose: function() {

            }
        }
    });
});

/**
 * Check accessories product
 */
$('body').on('change', '.nasa-check-accessories-product', function () {
    var _urlAjax = null;
    if (
        typeof nasa_ajax_params !== 'undefined' &&
        typeof nasa_ajax_params.wc_ajax_url !== 'undefined'
    ) {
        _urlAjax = nasa_ajax_params.wc_ajax_url.toString().replace('%%endpoint%%', 'nasa_refresh_accessories_price');
    }

    if (_urlAjax) {
        var _this = $(this);

        var _wrap = $(_this).parents('.nasa-accessories-check');

        var _id = $(_this).val();
        var _isChecked = $(_this).is(':checked');

        var _price = $(_wrap).find('.nasa-check-main-product').length ? parseInt($(_wrap).find('.nasa-check-main-product').attr('data-price')) : 0;
        if ($(_wrap).find('.nasa-check-accessories-product').length) {
            $(_wrap).find('.nasa-check-accessories-product').each(function() {
                if ($(this).is(':checked')) {
                    _price += parseInt($(this).attr('data-price'));
                }
            });
        }

        $.ajax({
            url: _urlAjax,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {
                total_price: _price
            },
            beforeSend: function () {
                $(_wrap).append('<div class="nasa-disable-wrap"></div><div class="nasa-loader"></div>');
            },
            success: function (res) {
                if (typeof res.total_price !== 'undefined') {
                    $('.nasa-accessories-total-price .price').html(res.total_price);

                    if (!_isChecked) {
                        $('.nasa-accessories-' + _id).fadeOut(200);
                    } else {
                        $('.nasa-accessories-' + _id).fadeIn(200);
                    }
                }

                $(_wrap).find('.nasa-loader, .nasa-disable-wrap').remove();
            },
            error: function () {

            }
        });
    }
});

/**
 * Add To cart accessories
 */
$('body').on('click', '.add_to_cart_accessories', function() {
    if (
        typeof nasa_ajax_params !== 'undefined' &&
        typeof nasa_ajax_params.wc_ajax_url !== 'undefined'
    ) {
        var _urlAjax = nasa_ajax_params.wc_ajax_url.toString().replace('%%endpoint%%', 'nasa_add_to_cart_accessories');
        var _this = $(this);

        var _wrap = $(_this).parents('.nasa-bought-together-wrap');
        if ($(_wrap).length) {
            var _wrapCheck = $(_wrap).find('.nasa-accessories-check');

            if ($(_wrapCheck).length) {
                var _pid = [];

                // nasa-check-main-product
                if ($(_wrapCheck).find('.nasa-check-main-product').length) {
                    _pid.push($(_wrapCheck).find('.nasa-check-main-product').val());
                }

                // nasa-check-accessories-product
                if ($(_wrapCheck).find('.nasa-check-accessories-product').length) {
                    $(_wrapCheck).find('.nasa-check-accessories-product').each(function() {
                        if ($(this).is(':checked')) {
                            _pid.push($(this).val());
                        }
                    });
                }

                if (_pid.length) {
                    $.ajax({
                        url: _urlAjax,
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        data: {
                            product_ids: _pid
                        },
                        beforeSend: function () {
                            $('.nasa-message-error').hide();
                            $(_wrap).append('<div class="nasa-disable-wrap"></div><div class="nasa-loader"></div>');
                        },
                        success: function (data) {
                            if (data && data.fragments) {
                                $.each(data.fragments, function(key, value) {
                                    $(key).replaceWith(value);
                                });

                                if ($('.cart-link').length) {
                                    $('.cart-link').trigger('click');
                                }
                            } else {
                                if (data && data.error && $('.nasa-message-error').length) {
                                    $('.nasa-message-error').html(data.message);
                                    $('.nasa-message-error').show();
                                }
                            }

                            $(_wrap).find('.nasa-loader, .nasa-disable-wrap').remove();
                        },
                        error: function () {
                            $(_wrap).find('.nasa-loader, .nasa-disable-wrap').remove();
                        }
                    });
                }
            }
        }
    }

    return false;
});

/**
 * Single Attributes Brands
 */
if ($('.single-product .nasa-sa-brands').length) {
    if ($('.single-product .woocommerce-product-rating').length) {
        $('.single-product .woocommerce-product-rating').addClass('nasa-has-sa-brands');
    } else {
        $('.single-product .nasa-sa-brands').addClass('margin-top-10');
    }
    
    $('.single-product .nasa-sa-brands').addClass('nasa-inited');
}

$('body').on('nasa_after_loaded_ajax_complete', function() {
    if ($('.single-product .nasa-sa-brands').length) {
        if ($('.single-product .woocommerce-product-rating').length) {
            $('.single-product .woocommerce-product-rating').addClass('nasa-has-sa-brands');
        } else {
            $('.single-product .nasa-sa-brands').addClass('margin-top-10');
        }

        $('.single-product .nasa-sa-brands').addClass('nasa-inited');
    }
});

/**
 * init Variations forms
 * 
 * @returns {undefined}
 */
setTimeout(function() {
    $('body').trigger('nasa_init_ux_variation_form');
}, 10);

});
