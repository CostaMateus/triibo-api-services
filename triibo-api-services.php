<?php
/**
 * Plugin Name: 	#Triibo_API_Services
 * Plugin URI: 		https://triibo.com.br
 * Description: 	Disponibiliza os serviços de API da Triibo (Gateway/Node/PHP) em um único plugin. Demais plugins dependerão deste.
 * Version: 		1.1.1
 * Author: 			Mateus Costa
 * Author URI: 		https://costamateus.com.br/
 * Text Domain: 	triibo-api-services
 *
 * @package 		Triibo_Api_Services
 * @version 		1.1.1
 */
defined( "ABSPATH" ) || exit;

if ( ! in_array( "woocommerce/woocommerce.php", apply_filters( "active_plugins", get_option( "active_plugins" ) ) ) ) return;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( "TRIIBO_API_SERVICES_VERSION", "1.1.1"  );
define( "TRIIBO_API_SERVICES_FILE",    __FILE__ );

/**
 * The core plugin class that is used to define admin-specific hooks.
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
