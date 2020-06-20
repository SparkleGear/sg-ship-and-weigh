<?php
/**
 * @link              author-uri
 * @since             1.0.0
 * @package           SparkleGear_Ship_and_Weigh
 *
 * @wordpress-plugin
 * Plugin Name:       SparkleGear Ship and Weigh
 * Plugin URI:        https://github.com/SparkleGear/sg-ship-and-weigh
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Jesse Looney
 * Author URI:        author-uri
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sg-ship-and-weigh
 * Domain Path:       /languages
 */
defined( 'ABSPATH' ) or die( 'Direct access blocked.' );

/**
 * Handle plugin inludes, actions, and activation
 * 
 * @since 1.0.0
 */
class SG_Ship_And_Weigh {

    /**
     * Filepath of the admin directory
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    protected string $admin_root_path;

    /**
     * Site URL of the admin directory
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    protected string $admin_root_url;

    /**
     * Specification for allowed settings and their defaults,
     * types, and sanatize callbacks
     * 
     * @since 1.0.0
     * 
     * @var array
     */
    protected array $settings_spec;

    /**
     * SG_Ship_And_Weigh constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->admin_root_path = plugin_dir_path( __FILE__ ) . 'admin/';
        $this->admin_root_url = plugin_dir_url( __FILE__ ) . 'admin/';

        $this->includes();
        $this->init_hooks();

        $this->settings_spec = SG_Ship_And_Weigh_Settings_Specification::get_settings_specification();
    }

    /**
     * Include necessary files useing require_once()
     * 
     * @since 1.0.0
     */
    protected function includes() {
        require_once( $this->admin_root_path
            . '/class-sg-ship-and-weigh-admin-api.php'
        );
        require_once( $this->admin_root_path
            . '/class-sg-ship-and-weigh-admin-menu.php'
        );
        require_once( $this->admin_root_path
            . '/class-sg-ship-and-weigh-admin-settings.php'
        );
        require_once( $this->admin_root_path
            . '/class-sg-ship-and-weigh-settings-specification.php'
        );
    }

    /**
     * Initialize plugin hooks
     * 
     * @since 1.0.0
     */
    protected function init_hooks() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'rest_api_init', array( $this, 'init_api' ) );
    }

    /**
     * Initialize classes
     * 
     * @since 1.0.0
     */
    public function init() {
        new SG_Ship_And_Weigh_Admin_Menu( $this->admin_root_url, $this->admin_root_path, $this->settings_spec );
    }

    /**
     * Initialize API classes
     * 
     * @since 1.0.0
     */
    public function init_api() {
        ( new SG_Ship_And_Weigh_Admin_API( $this->settings_spec ) )->add_routes();
    }

    /**
     * Flush rewrite rules on activation
     * 
     * @since 1.0.0
     */
    public function activate() {
        flush_rewrite_rules();
    }

    /**
     * Flush rewrite rules on deactivation
     * 
     * @since 1.0.0
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

if ( class_exists( 'SG_Ship_And_Weigh' ) ) {
    $sgShipAndWeigh = new SG_Ship_and_Weigh();
}

register_activation_hook( __FILE__, array( $sgShipAndWeigh, 'activate' ) );

register_deactivation_hook( __FILE__, array( $sgShipAndWeigh, 'deactivate' ) );