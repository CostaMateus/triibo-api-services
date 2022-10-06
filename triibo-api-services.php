<?php
/**
 * @link 			https://triibo.com.br
 * @since 			1.0.0
 * @package 		Triibo_Api_Services
 *
 * Plugin Name: 	#Triibo API Services
 * Plugin URI: 		https://triibo.com.br
 * Description: 	Disponibiliza os serviços de API da Triibo (Gateway/Node/PHP) em um único plugin. Demais plugins dependerão deste.
 * Version: 		1.0.0
 * Author: 			Mateus Costa
 * Author URI: 		https://costamateus.com.br/
 * Text Domain: 	triibo-api-services
 */

// If this file is called directly, abort.
defined( "ABSPATH" ) || exit;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( "TRIIBO_API_SERVICES_VERSION", "1.0.0"  );
define( "TRIIBO_API_SERVICES_FILE",    __FILE__ );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-triibo-api-services.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_triibo_api_services()
{
	$plugin = new Triibo_Api_Services();
	$plugin->run();
}

run_triibo_api_services();
