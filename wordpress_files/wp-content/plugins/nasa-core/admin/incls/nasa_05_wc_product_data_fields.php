<?php
defined('ABSPATH') or die(); // Exit if accessed directly

/**
 * Instantiate Class
 */
add_action('init', array('Nasa_WC_Product_Data_Fields', 'getInstance'), 0);

/**
 * @class 		Nasa_WC_Product_Data_Fields
 * @version		1.0
 * @author 		nasaTheme
 */
class Nasa_WC_Product_Data_Fields {

    protected static $_instance = null;
    public static $plugin_prefix = 'wc_productdata_options_';

    public $options_data = null;
    protected $_custom_fields = array();

    public $variation_data = null;
    protected $_variation_custom_fields = array();
    protected $_personalize = true;

    public $deleted_cache_post = false;

    public static function getInstance() {
        if (!NASA_WOO_ACTIVED) {
            return null;
        }

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Gets things started by adding an action to initialize this plugin once
     * WooCommerce is known to be active and initialized
     */
    public function __construct() {
        global $nasa_opt;
        
        if (isset($nasa_opt['enable_personalize']) && !$nasa_opt['enable_personalize']) {
            $this->_personalize = false;
        }

        $variation_custom_fields = $this->_personalize ? array(
            'nasa_personalize' => array(
                'type' => 'checkbox',
                'label' => esc_html__('Personalize (Allow Customized by Customer)', 'nasa-core'),
            )
        ) : array();

        $this->_variation_custom_fields = apply_filters('nasa_custom_variation_fields', $variation_custom_fields);

        $custom_fields = array();

        /**
         * Additional
         */
        $custom_fields['key'][0] = array(
            'tab_name'    => esc_html__('Additional', 'nasa-core'),
            'tab_id'      => 'additional'
        );

        $custom_fields['value'][0][] = array(
            'id'          => '_bubble_hot',
            'type'        => 'text',
            'label'       => esc_html__('Custom Badge', 'nasa-core'),
            'placeholder' => esc_html__('HOT', 'nasa-core'),
            'class'       => 'large',
            'style'       => 'width: 100%;',
            'description' => esc_html__('Enter badge label (NEW, HOT etc...).', 'nasa-core')
        );

        $custom_fields['value'][0][] = array(
            'id'          => '_product_video_link',
            'type'        => 'text',
            'placeholder' => 'https://www.youtube.com/watch?v=link-test',
            'label'       => esc_html__('Product Video Link', 'nasa-core'),
            'style'       => 'width:100%;',
            'description' => esc_html__('Enter a Youtube or Vimeo Url of the product video here.', 'nasa-core')
        );

        $custom_fields['value'][0][] = array(
            'id'          => '_product_video_size',
            'type'        => 'text',
            'label'       => esc_html__('Product Video Size', 'nasa-core'),
            'placeholder' => esc_html__('800x800', 'nasa-core'),
            'class'       => 'large',
            'style'       => 'width:100%;',
            'description' => esc_html__('Default is 800x800. (Width x Height)', 'nasa-core')
        );

        $custom_fields['value'][0][] = array(
            'id'          => '_product_image_simple_slide',
            'type'        => 'nasa_media',
            'label'       => esc_html__('Image Simple Slide', 'nasa-core'),
            'class'       => 'large',
            'style'       => 'width:100%;',
            'description' => esc_html__('Image show in Product Element with Style is Simple Slide', 'nasa-core')
        );

        /**
         * Specifications 
         */
        $custom_fields['key'][1] = array(
            'tab_name'    => esc_html__('Specifications', 'nasa-core'),
            'tab_id'      => 'specifications'
        );

        $custom_fields['value'][1][] = array(
            'id'          => 'nasa_specifications',
            'type'        => 'editor',
            'label'       => esc_html__('Technical Specifications', 'nasa-core'),
            'description' => esc_html__('Technical Specifications', 'nasa-core')
        );

        /**
         * Layout
         */
        $layouts = nasa_single_product_layouts();
        $imageLayouts = nasa_single_product_images_layout();
        $imageStyles = nasa_single_product_images_style();
        $thumbStyles = nasa_single_product_thumbs_style();
        $tabsStyles = nasa_single_product_tabs_style();

        $custom_fields['key'][2] = array(
            'tab_name'    => esc_html__('Layout', 'nasa-core'),
            'tab_id'      => 'layout'
        );

        $custom_fields['value'][2][] = array(
            'id'          => 'nasa_layout',
            'type'        => 'select',
            'options'     => $layouts,
            'label'       => esc_html__('Layout', 'nasa-core'),
            'class'       => 'select short nasa-select-main'
        );

        $custom_fields['value'][2][] = array(
            'id'          => 'nasa_image_layout',
            'type'        => 'select',
            'options'     => $imageLayouts,
            'label'       => esc_html__('Images Layout', 'nasa-core'),
            'class'       => 'select short nasa-v-new nasa-select-child'
        );

        $custom_fields['value'][2][] = array(
            'id'          => 'nasa_image_style',
            'type'        => 'select',
            'options'     => $imageStyles,
            'label'       => esc_html__('Images Style', 'nasa-core'),
            'class'       => 'select short nasa-v-new nasa-select-child'
        );

        $custom_fields['value'][2][] = array(
            'id'          => 'nasa_thumb_style',
            'type'        => 'select',
            'options'     => $thumbStyles,
            'label'       => esc_html__('Thumbnails Style', 'nasa-core'),
            'class'       => 'select short nasa-v-classic nasa-select-child'
        );

        $custom_fields['value'][2][] = array(
            'id'          => 'nasa_tab_style',
            'type'        => 'select',
            'options'     => $tabsStyles,
            'label'       => esc_html__('Tabs Style', 'nasa-core')
        );

        /**
         * 360 degree
         */
        if (!isset($nasa_opt['product_360_degree']) || $nasa_opt['product_360_degree']) {
            $custom_fields['key'][3] = array(
                'tab_name'    => esc_html__('360&#176; Viewer', 'nasa-core'),
                'tab_id'      => '360_degree'
            );

            $custom_fields['value'][3][] = array(
                'id'          => '_product_360_degree',
                'type'        => 'nasa_media_multi',
                'label'       => esc_html__('Gallery 360&#176; Viewer', 'nasa-core'),
                'class'       => 'large',
                'style'       => 'width:100%;',
                'description' => esc_html__('Add Gallery 360&#176; Viewer', 'nasa-core')
            );
        }
        
        if ($this->_personalize) {
            $custom_fields['key'][4] = array(
                'tab_name'    => esc_html__("Personalize (Simple Product)", 'nasa-core'),
                'tab_id'      => 'personalize'
            );

            $custom_fields['value'][4][] = array(
                'id'    => '_personalize',
                'type'  => 'checkbox',
                'label' => esc_html__('Personalize', 'nasa-core'),
                'description' => esc_html__('Allow Customized by Customer - Only use with Simple Product', 'nasa-core')
            );
        }

        $this->_custom_fields = apply_filters('nasa_product_custom_fields', $custom_fields);

        add_action('woocommerce_init', array(&$this, 'init'));
    }

    /**
     * Init WooCommerce Custom Product Data Fields extension once we know WooCommerce is active
     */
    public function init() {
        global $nasa_opt;

        add_action('woocommerce_product_write_panel_tabs', array($this, 'product_write_panel_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'product_write_panel'));
        add_action('woocommerce_process_product_meta', array($this, 'product_save_data'), 10, 2);

        /**
         * For variable product
         */
        if (!isset($nasa_opt['gallery_images_variation']) || $nasa_opt['gallery_images_variation']) {
            add_action('woocommerce_save_product_variation', array($this, 'nasa_save_variation_gallery'), 10, 1);
            add_action('woocommerce_product_after_variable_attributes', array($this, 'nasa_variation_gallery_admin_html'), 10, 3);
        }

        add_action('woocommerce_save_product_variation', array($this, 'nasa_save_variation_custom_fields'), 10, 1);
        add_action('woocommerce_product_after_variable_attributes', array($this, 'nasa_variation_custom_fields_admin_html'), 10, 3);

        /**
         * Bought together
         */
        add_action('woocommerce_product_options_related', array($this, 'nasa_accessories_product'));
    }

    /**
     * Variation gallery images
     * 
     * @param type $loop
     * @param type $variation_data
     * @param type $variation
     */
    public function nasa_variation_gallery_admin_html($loop, $variation_data, $variation) {
        include NASA_CORE_PLUGIN_PATH . 'admin/views/variation-admin-gallery-images.php';
    }

    /**
     * custom fields variation product
     * 
     * @param type $loop
     * @param type $variation_data
     * @param type $variation
     */
    public function nasa_variation_custom_fields_admin_html($loop, $variation_data, $variation) {
        include NASA_CORE_PLUGIN_PATH . 'admin/views/variation-admin-custom-fields.php';
    }

    /**
     * Adds a new tab to the Product Data postbox in the admin product interface
     */
    public function product_write_panel_tab() {
        $fields = $this->_custom_fields;
        foreach ($fields['key'] as $field) {
            echo '<li class="wc_productdata_options_tab"><a href="#wc_tab_' . $field['tab_id'] . '"><span>' . $field['tab_name'] . '</span></a></li>';
        }
    }

    /**
     * Adds the panel to the Product Data postbox in the product interface
     */
    public function product_write_panel() {
        global $post;
        // Pull the field data out of the database
        $available_fields = array();
        $available_fields[] = maybe_unserialize(get_post_meta($post->ID, 'wc_productdata_options', true));

        if ($available_fields) {
            $fields = $this->_custom_fields;

            // Display fields panel
            foreach ($available_fields as $available_field) {
                foreach ($fields['value'] as $key => $values) {
                    echo '<div id="wc_tab_' . $fields['key'][$key]['tab_id'] . '" class="panel woocommerce_options_panel">';

                    foreach ($values as $v) {
                        $this->wc_product_data_options_fields($v);
                    }

                    echo '</div>';
                }
            }
        }
    }

    /**
     * Create Fields
     */
    public function wc_product_data_options_fields($field) {
        global $thepostid, $post;

        $fieldtype = isset($field['type']) ? $field['type'] : 'text';
        $field_id = isset($field['id']) ? $field['id'] : '';
        $thepostid = empty($thepostid) ? $post->ID : $thepostid;

        if (!$this->options_data) {
            $this->options_data = maybe_unserialize(get_post_meta($thepostid, 'wc_productdata_options', true));
        }

        $options_data = $this->options_data;

        $inputval = '';
        if (isset($options_data[0][$field_id])) {
            $inputval = $options_data[0][$field_id];
        } elseif (isset($field['std'])) {
            $inputval = $field['std'];
        }

        $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
        $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
        $field['class'] = isset($field['class']) ? $field['class'] : 'short';
        $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';

        switch ($fieldtype) {
            case 'number':
                echo
                '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '">' .
                    '<label for="' . esc_attr($field['id']) . '">' . 
                        wp_kses_post($field['label']) . 
                    '</label>' .
                    '<input ' .
                        'type="number" ' .
                        'class="' . esc_attr($field['class']) . '" ' .
                        'name="' . esc_attr($field['name']) . '" ' .
                        'id="' . esc_attr($field['id']) . '" ' .
                        'value="' . esc_attr($inputval) . '" ' .
                        'placeholder="' . esc_attr($field['placeholder']) . '"' . 
                        (isset($field['style']) ? ' style="' . $field['style'] . '"' : '') .
                    ' /> ';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }

                echo '</p>';
                break;

            case 'textarea' :
                echo '<p class="form-field ' . $field['id'] . '_field"><label for="' . $field['id'] . '">' . $field['label'] . '</label><textarea class="' . $field['class'] . '" name="' . $field['id'] . '" id="' . $field['id'] . '" placeholder="' . $field['placeholder'] . '" rows="2" cols="20"' . (isset($field['style']) ? ' style="' . $field['style'] . '"' : '') . '">' . esc_textarea($inputval) . '</textarea>';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
                echo '</p>';
                break;

            case 'editor' :
                $height = isset($field['height']) && (int) $field['height'] ? (int) $field['height'] : 200;
                wp_editor($inputval, $field['id'], array('editor_height' => $height));
                break;

            case 'checkbox':
                $field['class'] = trim('nasa-checkbox ' . str_replace('short', '', $field['class']));
                $field['cbvalue'] = isset($field['cbvalue']) ? $field['cbvalue'] : 'yes';
                echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><input type="checkbox" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['name']) . '" id="' . esc_attr($field['id']) . '" value="' . esc_attr($field['cbvalue']) . '" ' . checked($inputval, $field['cbvalue'], false) . ' /><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label> ';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
                echo '</p>';
                break;

            case 'select':
                $field['class'] = isset($field['class']) ? $field['class'] : 'select short';

                echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label><select id="' . esc_attr($field['id']) . '" name="' . esc_attr($field['id']) . '" class="' . esc_attr($field['class']) . '">';

                foreach ($field['options'] as $key => $value) {
                    echo '<option value="' . esc_attr($key) . '" ' . selected(esc_attr($inputval), esc_attr($key), false) . '>' . esc_html($value) . '</option>';
                }

                echo '</select> ';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
                echo '</p>';
                break;

            case 'radio':
                $field['class'] = isset($field['class']) ? $field['class'] : 'select short';

                echo '<fieldset class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><legend style="float:left; width:150px;">' . wp_kses_post($field['label']) . '</legend><ul class="wc-radios" style="width: 25%; float:left;">';
                foreach ($field['options'] as $key => $value) {
                    echo '<li style="padding-bottom: 3px; margin-bottom: 0;"><label style="float:none; width: auto; margin-left: 0;"><input name="' . esc_attr($field['name']) . '" value="' . esc_attr($key) . '" type="radio" class="' . esc_attr($field['class']) . '" ' . checked(esc_attr($inputval), esc_attr($key), false) . ' /> ' . esc_html($value) . '</label></li>';
                }
                echo '</ul>';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }

                echo '</fieldset>';
                break;

            case 'hidden':
                $field['class'] = isset($field['class']) ? $field['class'] : '';

                echo '<input type="hidden" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" value="' . esc_attr($inputval) . '" /> ';

                break;

            /**
             * Image
             */
            case 'nasa_media':
                include NASA_CORE_PLUGIN_PATH . 'admin/views/media-image.php';

                break;

            /**
             * Images multi
             */
            case 'nasa_media_multi':
                include NASA_CORE_PLUGIN_PATH . 'admin/views/media-multi-images.php';

                break;

            case 'text':
            default :
                echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label><input type="text" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['name']) . '" id="' . esc_attr($field['id']) . '" value="' . esc_attr($inputval) . '" placeholder="' . esc_attr($field['placeholder']) . '"' . (isset($field['style']) ? ' style="' . $field['style'] . '"' : '') . ' /> ';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
                echo '</p>';
                break;
        }
    }

    /**
     * Create Custom Fields for Variation
     */
    public function wc_variation_data_custom_fields($variation_id, $key, $field) {
        $fieldtype = isset($field['type']) ? $field['type'] : 'text';
        $field_id = $key;

        if (!isset($this->variation_data[$variation_id])) {
            $this->variation_data[$variation_id] = maybe_unserialize(get_post_meta($variation_id, 'wc_variation_custom_fields', true));
        }

        $options_data = $this->variation_data[$variation_id];

        $inputval = '';
        if (isset($options_data[$field_id])) {
            $inputval = $options_data[$field_id];
        } elseif (isset($field['std'])) {
            $inputval = $field['std'];
        }

        $field['id'] = isset($field['id']) ? $field['id'] : 'variation-' . $variation_id . '-' . $key;
        $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
        $field['class'] = isset($field['class']) ? $field['class'] : 'short';
        $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
        $field_name = $key . '[' . $variation_id . ']';

        switch ($fieldtype) {
            case 'number':
                echo
                '<p class="form-field form-row form-row-full ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '">' .
                    '<label for="' . esc_attr($field['id']) . '">' . 
                        wp_kses_post($field['label']) . 
                    '</label>' .
                    '<input ' .
                        'type="' . esc_attr($field['type']) . '" ' .
                        'class="' . esc_attr($field['class']) . '" ' .
                        'name="' . esc_attr($field_name) . '[' . $variation_id . ']" ' .
                        'id="' . esc_attr($field['id']) . '" ' .
                        'value="' . esc_attr($inputval) . '" ' .
                        'placeholder="' . esc_attr($field['placeholder']) . '"' . 
                        (isset($field['style']) ? ' style="' . $field['style'] . '"' : '') .
                    ' /> ';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }

                echo '</p>';
                break;

            case 'textarea' :
                echo '<p class="form-field form-row form-row-full ' . $field['id'] . '_field"><label for="' . $field['id'] . '">' . $field['label'] . '</label><textarea class="' . $field['class'] . '" name="' . $field_name . '" id="' . $field['id'] . '" placeholder="' . $field['placeholder'] . '" rows="2" cols="20"' . (isset($field['style']) ? ' style="' . $field['style'] . '"' : '') . '">' . esc_textarea($inputval) . '</textarea>';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
                echo '</p>';
                break;

            case 'checkbox':
                $field['cbvalue'] = isset($field['cbvalue']) ? $field['cbvalue'] : 'yes';

                echo '<p class="form-field form-row form-row-full ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><input type="checkbox" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field_name) . '" id="' . esc_attr($field['id']) . '" value="' . esc_attr($field['cbvalue']) . '" ' . checked($inputval, $field['cbvalue'], false) . ' /><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label> ';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
                echo '</p>';
                break;

            case 'select':
                $field['class'] = isset($field['class']) ? $field['class'] : 'select short';

                echo '<p class="form-field form-row form-row-full ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label><select id="' . esc_attr($field['id']) . '" name="' . esc_attr($field_name) . '" class="' . esc_attr($field['class']) . '">';

                foreach ($field['options'] as $key => $value) {
                    echo '<option value="' . esc_attr($key) . '" ' . selected(esc_attr($inputval), esc_attr($key), false) . '>' . esc_html($value) . '</option>';
                }

                echo '</select> ';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
                echo '</p>';
                break;

            case 'radio':
                $field['class'] = isset($field['class']) ? $field['class'] : 'select short';

                echo '<fieldset class="form-field form-row form-row-full ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><legend style="float:left; width:150px;">' . wp_kses_post($field['label']) . '</legend><ul class="wc-radios" style="width: 25%; float:left;">';
                foreach ($field['options'] as $key => $value) {
                    echo '<li style="padding-bottom: 3px; margin-bottom: 0;"><label style="float:none; width: auto; margin-left: 0;"><input name="' . esc_attr($field_name) . '" value="' . esc_attr($key) . '" type="radio" class="' . esc_attr($field['class']) . '" ' . checked(esc_attr($inputval), esc_attr($key), false) . ' /> ' . esc_html($value) . '</label></li>';
                }
                echo '</ul>';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }

                echo '</fieldset>';
                break;

            case 'text':
            default :
                echo '<p class="form-field form-row form-row-full ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '"><label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label><input type="text" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field_name) . '" id="' . esc_attr($field['id']) . '" value="' . esc_attr($inputval) . '" placeholder="' . esc_attr($field['placeholder']) . '"' . (isset($field['style']) ? ' style="' . $field['style'] . '"' : '') . ' /> ';

                if (!empty($field['description'])) {
                    echo (isset($field['desc_tip']) && false !== $field['desc_tip']) ?
                        '<img class="help_tip" data-tip="' . esc_attr($field['description']) . '" src="' . esc_url(WC()->plugin_url()) . '/assets/images/help.png" height="16" width="16" />' :
                        '<span class="description">' . wp_kses_post($field['description']) . '</span>';
                }
                echo '</p>';
                break;
        }
    }

    /**
     * Bought together
     * 
     * @global type $post
     * @global type $thepostid
     */
    public function nasa_accessories_product() {
        global $post, $thepostid;
        $product_ids = $this->get_accessories_ids($thepostid);
        include NASA_CORE_PLUGIN_PATH . 'admin/views/html-accessories-product.php';
    }

    /**
     * Bought together Post ids
     * 
     * @param type $post_id
     * @return type
     */
    protected function get_accessories_ids($post_id) {
        $ids = get_post_meta($post_id, '_accessories_ids', true);

        return $ids;
    }

    /**
     * Saves the data inputed into the product boxes, as post meta data
     * identified by the name 'wc_productdata_options'
     *
     * @param int $post_id the post (product) identifier
     * @param stdClass $post the post (product)
     */
    public function product_save_data($post_id, $post) {
        /** field name in pairs array * */
        $data_args = array();
        $fields = $this->_custom_fields;

        foreach ($fields['value'] as $key => $datas) {
            foreach ($datas as $k => $data) {
                if (isset($data['id'])) {
                    $data_args[$data['id']] = stripslashes($_POST[$data['id']]);
                }
            }
        }

        $options_value = array($data_args);

        // save the data to the database
        update_post_meta($post_id, 'wc_productdata_options', $options_value);

        /**
         * Accessories for product
         */
        if (isset($_POST['accessories_ids'])) {
            update_post_meta($post_id, '_accessories_ids', $_POST['accessories_ids']);
        } else {
            update_post_meta($post_id, '_accessories_ids', null);
        }

        /**
         * Delete cache by post id
         */
        if (!$this->deleted_cache_post) {
            nasa_del_cache_by_product_id($post_id);
            $this->deleted_cache_post = true;
        }
    }

    /**
     * Save variation gallery
     * 
     * @param type $variation_id
     * return void
     */
    public function nasa_save_variation_gallery($variation_id) {
        if (isset($_POST['nasa_variation_gallery_images'])) {

            if (!$this->deleted_cache_post) {
                global $nasa_product_parent;

                /**
                 * Delete cache by post id
                 */
                if (!$nasa_product_parent) {
                    $parent_id = wp_get_post_parent_id($variation_id);
                    $nasa_product_parent = $parent_id ? wc_get_product($parent_id) : null;
                    $GLOBALS['nasa_product_parent'] = $nasa_product_parent;
                }

                if ($nasa_product_parent) {
                    $productId = $nasa_product_parent->get_id();
                    nasa_del_cache_by_product_id($productId);
                }

                $this->deleted_cache_post = true;
            }

            /**
             * Save gallery for variation
             */
            if (isset($_POST['nasa_variation_gallery_images'][$variation_id])) {
                $galery = trim($_POST['nasa_variation_gallery_images'][$variation_id], ',');
                update_post_meta($variation_id, 'nasa_variation_gallery_images', $galery);

                return;
            }
        }

        delete_post_meta($variation_id, 'nasa_variation_gallery_images');
    }

    /**
     * Save variation gallery
     * 
     * @param type $variation_id
     * return void
     */
    public function nasa_save_variation_custom_fields($variation_id) {
        if (empty($this->_variation_custom_fields)) {
            return;
        }

        if (!$this->deleted_cache_post) {
            global $nasa_product_parent;

            /**
             * Delete cache by post id
             */
            if (!$nasa_product_parent) {
                $parent_id = wp_get_post_parent_id($variation_id);
                $nasa_product_parent = $parent_id ? wc_get_product($parent_id) : null;
                $GLOBALS['nasa_product_parent'] = $nasa_product_parent;
            }

            if ($nasa_product_parent) {
                $productId = $nasa_product_parent->get_id();
                nasa_del_cache_by_product_id($productId);
            }

            $this->deleted_cache_post = true;
        }

        /**
         * Build custom fields data save for variation
         */
        $data_save = array();
        foreach ($this->_variation_custom_fields as $key => $field) {
            if (isset($_POST[$key][$variation_id])) {
                $data_save[$key] = stripslashes($_POST[$key][$variation_id]);
            }
        }

        if (!empty($data_save)) {
            update_post_meta($variation_id, 'wc_variation_custom_fields', $data_save);
        } else {
            delete_post_meta($variation_id, 'wc_variation_custom_fields');
        }
    }
}
