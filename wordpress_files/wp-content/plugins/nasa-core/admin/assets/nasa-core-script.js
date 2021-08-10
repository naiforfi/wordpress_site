jQuery(document).ready(function ($) {
    'use strict';
    if (typeof wp === 'undefined') {
        var wp = window.wp;
    }
    
    $('body').on('click', '.nasa-clear-variations-cache', function() {
        var _this = $(this);
        var _ok = $(_this).attr('data-ok');
        var _miss = $(_this).attr('data-miss');
        var _fail = $(_this).attr('data-fail');
        if(!$(_this).hasClass('nasa-disable')) {
            $(_this).addClass('nasa-disable');
            $.ajax({
                url: ajax_admin_nasa_core,
                type: 'get',
                dataType: 'html',
                data: {
                    action: 'nasa_clear_all_cache'
                },
                beforeSend: function() {
                    if($('.nasa-admin-loader').length) {
                        $('.nasa-admin-loader').show();
                    }
                },
                success: function(res){
                    $(_this).removeClass('nasa-disable');
                    if($('.nasa-admin-loader').length) {
                        $('.nasa-admin-loader').hide();
                    }
                    
                    if(res === 'ok') {
                        alert(_ok);
                    } else {
                        alert(_miss);
                    }
                },
                error: function () {
                    $(_this).removeClass('nasa-disable');
                    if($('.nasa-admin-loader').length) {
                        $('.nasa-admin-loader').hide();
                    }
                    
                    alert(_fail);
                }
            });
        }
    });
    
    if($('.term-parent-wrap select[name="parent"]').val() === '-1') {
        $('.nasa-term-root').show();
        if ($('.nasa-term-root select').length) {
            $('.nasa-term-root select').each(function () {
                var _val = $(this).val();
                var _name = $(this).attr('name');
                $('.nasa-term-root-child.' + _name).hide();
                if (_val) {
                    $('.nasa-term-root-child.nasa-term-' + _name + '-' + _val).show();
                }
            });
        }
    } else {
        $('.nasa-term-root, .nasa-term-root-child').hide();
    }

    $('body').on('change', '.term-parent-wrap select[name="parent"]', function() {
        var _val = $(this).val();
        if(_val === '-1') {
            $('.nasa-term-root').show();
            
            if ($('.nasa-term-root select').length) {
                $('.nasa-term-root select').each(function () {
                    var _val = $(this).val();
                    var _name = $(this).attr('name');
                    
                    $('.nasa-term-root-child.' + _name).hide();
                    if (_val) {
                        $('.nasa-term-root-child.nasa-term-' + _name + '-' + _val).show();
                    }
                });
            }
        } else {
            $('.nasa-term-root, .nasa-term-root-child').hide();
        }
    });
    
    $('body').on('change', '.nasa-term-root select', function() {
        var _val = $(this).val();
        var _name = $(this).attr('name');

        $('.nasa-term-root-child.' + _name).hide();
        if (_val) {
            $('.nasa-term-root-child.nasa-term-' + _name + '-' + _val).show();
        }
    });
    
    if ($('.nasa-select-main').length) {
        $('.nasa-select-main').each(function() {
            var _this = $(this);
            var _panel = $(_this).parents('.woocommerce_options_panel');
            var _selected = $(_this).val();
            
            $(_panel).find('select.nasa-select-child').parents('.form-field').hide();
            $(_panel).find('select.nasa-v-' + _selected).parents('.form-field').show();
        });
        
        $('body').on('change', '.nasa-select-main', function() {
            var _this = $(this);
            var _panel = $(_this).parents('.woocommerce_options_panel');
            var _selected = $(_this).val();
            
            $(_panel).find('select.nasa-select-child').parents('.form-field').hide();
            $(_panel).find('select.nasa-v-' + _selected).parents('.form-field').show();
        });
    }
    
    /**
     * Add Gallery
     * 
     * @param {type} $
     * @returns {undefined}
     */
    $('body').on('click', '.nasa-add-gallery', function(event) {
        var _this = $(this);

        event.preventDefault();

        var _wrap = $(_this).parents('.nasa-gallery-wrapper');
        var product_gallery_frame;

        var $image_gallery_ids = $(_wrap).find('.nasa-gallery-images-input');
        var $product_images = $(_wrap).find('.nasa-gallery-images-list');

        // If the media frame already exists, reopen it.
        if (product_gallery_frame) {
            product_gallery_frame.open();
            return;
        }

        // Create the media frame.
        product_gallery_frame = wp.media.frames.product_gallery = wp.media({
            // Set the title of the modal.
            title: $(_this).data('choose'),
            button: {
                text: $(_this).data('update')
            },
            states: [
                new wp.media.controller.Library({
                    title: $(_this).data('choose'),
                    filterable: 'all',
                    multiple: true
                })
            ]
        });

        // When an image is selected, run a callback.
        product_gallery_frame.on('select', function () {
            var selection = product_gallery_frame.state().get('selection');
            var attachment_ids = $image_gallery_ids.val();

            selection.map(function (attachment) {
                attachment = attachment.toJSON();

                if (attachment.id) {
                    attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $product_images.append('<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="javascript:void(0);" class="delete">' + $(_this).data('text') + '</a></li></ul></li>');
                }
            });

            $image_gallery_ids.val(attachment_ids);
        });

        // Finally, open the modal.
        product_gallery_frame.open();
    });
    
    // Remove Image from Gallery
    $('body').on('click', '.nasa-gallery-images-list .actions a.delete', function () {
        var _this = $(this);
        
        var _wrap = $(_this).parents('.nasa-gallery-wrapper');
        var _list = $(_this).parents('.nasa-gallery-images-list');
        $(_this).parents('li.image').remove();

        var attachment_ids = '';

        $(_list).find('li.image').each(function () {
            var attachment_id = $(this).attr('data-attachment_id');
            attachment_ids = attachment_ids + attachment_id + ',';
        });

        $(_wrap).find('.nasa-gallery-images-input').val(attachment_ids);
        
        return false;
    });
    
    /**
     * Sort by drag and drop
     * 
     * @param {type} $
     * @returns {undefined}
     */
    nasaGalleryImages($);
});

/**
 * Sort by drag and drop
 * 
 * @param {type} $
 * @returns {undefined}
 */
function nasaGalleryImages($) {
    $('.nasa-gallery-images-list').each(function () {
        var _this = $(this);
        var _wrap = $(_this).parents('.nasa-gallery-wrapper');
        
        _this.sortable({
            items: 'li.image',
            cursor: 'move',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            forceHelperSize: false,
            helper: 'clone',
            opacity: 0.65,
            placeholder: 'wc-metabox-sortable-placeholder',
            start: function (event, ui) {
                ui.item.css('background-color', '#f6f6f6');
            },
            stop: function (event, ui) {
                ui.item.removeAttr('style');
            },
            update: function () {
                var attachment_ids = '';

                $(_this).find('li.image').css('cursor', 'default').each(function () {
                    var attachment_id = $(this).attr('data-attachment_id');
                    attachment_ids = attachment_ids + attachment_id + ',';
                });

                $(_wrap).find('.nasa-gallery-images-input').val(attachment_ids);
            }
        });
    });
}
