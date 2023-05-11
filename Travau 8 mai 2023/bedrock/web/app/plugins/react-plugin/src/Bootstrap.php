<?php

namespace Rezah;

use Rezah\Modules\ApiWordpress\Api_Wordpress; 

class Bootstrap {

	public function __construct() {
        add_action('admin_init', [$this, 'admin_init']);
        add_action('init', [$this, 'init']);
        // add_action('after_setup_theme', [$this, 'addImageSize']);
        add_action('init', [$this, 'activateSession'], 1);
        add_action('plugins_loaded', [$this, 'rzhPluginLoaded']);
    
        add_filter('allowed_http_origins', [$this, 'rzhAddAllowedOrigins']);
    }

    public function rzhAddAllowedOrigins($origin) {
        $origins[] = 'https://custom.test';
        return $origins;
      }
    
      public function activateSession() {
        if(!session_id()) {
          session_start();
        }
        /*
        You now can use $_SESSION['your-var'] = 'your-value';
        to set a session variable. Take a look at the PHP documentation on sessions.
        */
      }


	public function init() {
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        }
    
    public function admin_init() {
        add_action('admin_enqueue_scripts', [$this, 'adminAssets']);
    }


    public function rzhPluginLoaded() {
        load_plugin_textdomain( 'rzh_custom', false, RZH_PLUGIN_PATH.'/languages/' );
        $this->loadModules();
    }
        
    public function assets() {
        $this->commonAssets();
        wp_enqueue_style('rzh_front_styles', RZH_PLUGIN_CSS_URL.'/css/custom.css' );
    }
        
    public function adminAssets() {
        $this->commonAssets();
        wp_enqueue_style('rzh_admin_styles', RZH_PLUGIN_CSS_URL.'/admin-custom.css' );
        
    }

    public function commonAssets() {
        wp_enqueue_media();
        wp_enqueue_script('RZH_common_migrate', RZH_PLUGIN_JS_URL.'/jquery-migrate.min.js', ['jquery'], RZH_PLUGIN_VERSION, true);
        wp_enqueue_script('RZH_jquery_ui_script', RZH_PLUGIN_JS_URL.'/jquery-ui.min.js', ['jquery'], RZH_PLUGIN_VERSION, true);
        wp_enqueue_script('RZH_common_fn_script', RZH_PLUGIN_JS_URL.'/common-fn-custom.js', ['jquery'], RZH_PLUGIN_VERSION, true);
        wp_enqueue_script('RZH_common_script', RZH_PLUGIN_JS_URL.'/custom.js', ['jquery'], RZH_PLUGIN_VERSION, true);
        wp_enqueue_script('wp_react', RZH_PLUGIN_ASSETS_URL. '/wp_react/wp-react/dist/react_js_file.js', array(), RZH_PLUGIN_VERSION, true );
        

        wp_localize_script('RZH_common_fn_script', 'jtx_url', [
          'site' => get_option('siteurl'),
          'admin' => get_admin_url(null, 'admin-ajax.php'),
        ]);

        wp_enqueue_style('RZH_jquery_ui_style', RZH_PLUGIN_CSS_URL.'/jquery-ui.min.css', [], RZH_PLUGIN_VERSION, 'all');

    }

    private function loadModules() {

        new Api_Wordpress;

    }

    public function rzhActivation() {
        //on activation de plugin
    }
    
    public function rzhDesactivation() {
        ////on activation de plugin
    }

}