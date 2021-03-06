<?php
/**
 * Add menus to the Admin Control Panel
 * 
 * @since 1.0.0
 */
defined( 'ABSPATH' ) or die( 'Direct access blocked.' );

class SG_Ship_And_Weigh_Admin_Menu {

    /**
     * Shipping menu slug
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    protected string $shipping_slug = 'sg-ship-and-weigh-shipping-menu';

    /**
     * Settings menu slug
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    protected string $settings_slug = 'sg-ship-and-weigh-settings-menu';

    /**
     * Shipping confirmation page menu slug
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    protected string $shipping_confirmation_slug = 'sg-ship-and-weigh-shipping-confirmation';

    /**
     * URL of the admin includes and assets
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    protected string $assets_root_url;

    /**
     * Path to the admin includes and assets
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    protected string $assets_root_path;

    /**
     * SG_Ship_And_Weigh_Admin_Menu constructor
     * 
     * @since 1.0.0
     * 
     * @param string $assets_root_url URL of the includes and assets
     * @param string $assets_root_path Path to the includes and assets
     */
    public function __construct( string $assets_root_url, string $assets_root_path ) {
        $this->assets_root_url = $assets_root_url;
        $this->assets_root_path = $assets_root_path;

        $this->init_hooks();
    }

    /**
     * Initialize hooks for the menu pages
     * 
     * @since 1.0.0
     */
    public function init_hooks() {
        add_action( 'admin_menu', array( $this, 'add_shipping_menu' ) );
        add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );

        add_action( 'admin_menu', array( $this, 'add_shipping_confirmation_page' ) );
        add_action( 'admin_head', array( $this, 'hide_shipping_confirmation_page') );
    }

    /**
     * Enqueue CSS and JS for page and pass an object to the created page
     * 
     * @since 1.0.0
     * 
     * @param string $slug Slug of the page to be created
     * @param string $script_url URL from assets_root_url to the page script
     * @param string $style_url URL from assets_root_url to the page stylesheet
     * @param string $object_name JS object name to be passed to wp_localize_script()
     * @param string $object JS object to be passed to wp_localize_script()
     * @param string (default [ 'jquery' ]) $script_deps Array of script dependencies
     * @param string (default []) $style_deps Array of style dependencies
     */
    public function enqueue_assets(
        string $slug,
        string $script_url, string $style_url,
        string $object_name, array $object,
        array $script_deps = [ 'jquery' ],
        array $style_deps = []
    ) {
        wp_enqueue_script( $slug, $this->assets_root_url . $script_url, $script_deps );
        wp_enqueue_style( $slug, $this->assets_root_url . $style_url, $style_deps );
        wp_localize_script( $slug, $object_name, $object );
    }

    /**
     * Load and enqueue assets for shipping_menu
     * 
     * @since 1.0.0
     */
    public function load_shipping_menu() {
        // Register dependencies
        wp_register_script(
            $this->shipping_slug . '-vuejs',
            'https://cdn.jsdelivr.net/npm/vue/dist/vue.js',
        );

        wp_register_script(
            $this->shipping_slug . '-uuidjs',
            'https://cdn.jsdelivr.net/npm/uuid@latest/dist/umd/uuidv4.min.js'
        );

        wp_register_script(
            $this->shipping_slug . '-selectize',
            'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js',
        );
        wp_register_style(
            $this->shipping_slug . '-selectize',
            'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css',
        );

        $this->enqueue_assets(
            $this->shipping_slug,
            'js/sg-ship-and-weigh-shipping-menu.js',
            'css/sg-ship-and-weigh-menu.css',
            'SHIP_AND_WEIGH',
            array(
                'strings' => array(
                    'recipient_added' => __( 'Recipient Added', 'text-domain' ),
                    'recipient_removed' => __( 'Recipient Removed', 'text-domain' ),
                    'error' => __( 'Error', 'text-domain' ),
                ),
                'api'     => array(
                    'url' => array(
                        'settings'   => esc_url_raw( rest_url( 'sg-ship-and-weigh-api/v1/settings' ) ),
                        'recipients' => esc_url_raw( rest_url( 'sg-ship-and-weigh-api/v1/recipients' ) ),
                        'address_verification' => esc_url_raw( rest_url( 'sg-ship-and-weigh-api/v1/easypost/verify-address' ) ),
                        'rates' => esc_url_raw( rest_url( 'sg-ship-and-weigh-api/v1/easypost/rates' ) ),
                        'buy_shipment' => esc_url_raw( rest_url( 'sg-ship-and-weigh-api/v1/easypost/buy-shipment' ) ),
                    ),
                    'nonce'          => wp_create_nonce( 'wp_rest' ),
                ),
                'debug'   => WP_DEBUG,
                'shipping_confirmation_url' => esc_url_raw( admin_url() . 'admin.php?page=' . $this->shipping_confirmation_slug ),
            ),
            $script_deps = [
                'jquery',
                $this->shipping_slug . '-vuejs',
                $this->shipping_slug . '-selectize',
                $this->shipping_slug . '-uuidjs',
            ],
            $style_deps = [
                $this->shipping_slug . '-selectize',
            ],
        );
        include( $this->assets_root_path . 'pages/sg-ship-and-weigh-shipping-menu.php' );
    }

    /**
     * Create plugin shipping menu
     * 
     * @since 1.0.0
     */
    public function add_shipping_menu() {
        add_menu_page(
            'Generate Label - Ship and Weigh',
            'Ship and Weigh',
            'manage_options',
            $this->shipping_slug,
            array( $this, 'load_shipping_menu' ),
        );
    }

    /**
     * Load and enqueue assets for settings_menu
     * 
     * @since 1.0.0
     */
    public function load_settings_menu() {
        // Register dependencies
        wp_register_script(
            $this->settings_slug . '-vuejs',
            'https://cdn.jsdelivr.net/npm/vue/dist/vue.js',
        );

        wp_register_script(
            $this->settings_slug . '-selectize',
            'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js',
        );
        wp_register_style(
            $this->settings_slug . '-selectize',
            'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css',
        );

        $this->enqueue_assets(
            $this->settings_slug,
            'js/sg-ship-and-weigh-settings-menu.js',
            'css/sg-ship-and-weigh-menu.css',
            'SHIP_AND_WEIGH',
            array(
                'strings'  => array(
                    'saved' => __( 'Settings Saved', 'text-domain' ),
                    'error' => __( 'Error', 'text-domain' ),
                ),
                'api'      => array(
                    'url' => array(
                        'settings'   => esc_url_raw( rest_url( 'sg-ship-and-weigh-api/v1/settings' ) ),
                        'address_verification' => esc_url_raw( rest_url( 'sg-ship-and-weigh-api/v1/easypost/verify-address' ) ),
                    ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                ),
                'debug'    => WP_DEBUG,
            ),
            $script_deps = [
                'jquery',
                $this->settings_slug . '-vuejs',
                $this->settings_slug . '-selectize',
            ],
            $style_deps = [
                $this->settings_slug . '-selectize',
            ],
        );
        include( $this->assets_root_path . 'pages/sg-ship-and-weigh-settings-menu.php' );
    }

    /**
     * Create plugin settings menu
     * 
     * @since 1.0.0
     */
    public function add_settings_menu() {
        add_submenu_page(
            $this->shipping_slug,
            'Settings - Ship and Weigh',
            'Settings',
            'manage_options',
            $this->settings_slug,
            array( $this, 'load_settings_menu' ),
        );
    }

    public function load_shipping_confirmation_page() {
        $this->enqueue_assets(
            $this->shipping_confirmation_slug,
            'js/sg-ship-and-weigh-shipping-confirmation-page.js',
            'css/sg-ship-and-weigh-menu.css',
            'SHIP_AND_WEIGH',
            array(
                'api' => array(
                    'url' => array(
                        'refund' => esc_url_raw( rest_url( 'sg-ship-and-weigh-api/v1/easypost/refund' ) ),
                    ),
                ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'debug' => WP_DEBUG,
            ),
        );
        include( $this->assets_root_path . 'pages/sg-ship-and-weigh-shipping-confirmation-page.php' );
    }

    public function add_shipping_confirmation_page() {
        add_submenu_page(
            $this->shipping_slug,
            'Shipping Confirmation - Ship and Weigh',
            'Shipping Confirmation',
            'manage_options',
            $this->shipping_confirmation_slug,
            array( $this, 'load_shipping_confirmation_page' ),
        );
    }

    /**
     * Hide shipping confirmation page
     * 
     * @since 1.0.0
     */
    public function hide_shipping_confirmation_page() {
        remove_submenu_page(
            $this->shipping_slug,
            $this->shipping_confirmation_slug,
        );
    }
}