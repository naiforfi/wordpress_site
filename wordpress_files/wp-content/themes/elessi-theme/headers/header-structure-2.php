<div class="<?php echo esc_attr($header_classes); ?>">
    <?php
    /**
     * Hook - top bar header
     */
    do_action('nasa_topbar_header');
    ?>
    <div class="sticky-wrapper">
        <div id="masthead" class="site-header">
            <?php do_action('nasa_mobile_header'); ?>
            
            <div class="row nasa-hide-for-mobile">
                <div class="large-12 columns nasa-wrap-event-search">
                    <div class="row nasa-elements-wrap">
                        <!-- Group icon header -->
                        <div class="large-4 columns nasa-min-height">
                            <?php echo shortcode_exists('nasa_follow') ? do_shortcode('[nasa_follow]') : '&nbsp;'; ?>
                        </div>

                        <!-- Logo -->
                        <div class="large-4 columns nasa-fixed-height text-center">
                            <?php echo elessi_logo(); ?>
                        </div>

                        <!-- Group icon header -->
                        <div class="large-4 columns">
                            <?php echo $nasa_header_icons; ?>
                        </div>
                    </div>
                    
                    <!-- Search form in header -->
                    <div class="nasa-header-search-wrap">
                        <?php echo elessi_search('icon'); ?>
                    </div>
                </div>
            </div>
            
            <!-- Main menu -->
            <?php if (!$fullwidth_main_menu) : ?>
            <div class="row">
                <div class="large-12 columns">
            <?php endif; ?>
                    <div class="nasa-elements-wrap nasa-elements-wrap-main-menu nasa-hide-for-mobile nasa-bg-dark text-center">
                        <div class="row">
                            <div class="large-12 columns">
                                <div class="wide-nav nasa-wrap-width-main-menu nasa-bg-wrap<?php echo esc_attr($menu_warp_class); ?>">
                                    <div class="nasa-menus-wrapper-reponsive nasa-loading" data-padding_y="<?php echo (int) $data_padding_y; ?>" data-padding_x="<?php echo (int) $data_padding_x; ?>">
                                        <?php elessi_get_main_menu(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php if (!$fullwidth_main_menu) : ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($show_cat_top_filter) && $show_cat_top_filter) : ?>
                <div class="nasa-top-cat-filter-wrap">
                    <?php echo elessi_get_all_categories(false, true); ?>
                    <a href="javascript:void(0);" title="<?php esc_attr_e('Close', 'elessi-theme'); ?>" class="nasa-close-filter-cat nasa-stclose nasa-transition" rel="nofollow"></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
