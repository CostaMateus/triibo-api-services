<?php
/**
 * Provide a admin area view for the plugin
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 * @package    Triibo_Api_Services
 * @subpackage Triibo_Api_Services/templates
 * @author     Mateus Costa <mateus@costamateus.com.br>
 */
?>

<div class="wrap">
	<h2>Settings Page Settings</h2>

	<?php settings_errors(); ?>

	<form method="POST" action="options.php">
		<?php
			settings_fields( "triibo_api_services_general_settings" );
			do_settings_sections( "triibo_api_services_general_settings" );
			submit_button();

			// echo "ENV | ";
			// echo get_option( "triibo_api_services_gate_env"    ) ? "true " : "false ";
			// echo get_option( "triibo_api_services_node_env"    ) ? "true " : "false ";
			// echo get_option( "triibo_api_services_php_env"     ) ? "true " : "false ";

			// echo "<br>STATUS | ";
			// echo get_option( "triibo_api_services_gate_status" ) ? "true " : "false ";
			// echo get_option( "triibo_api_services_node_status" ) ? "true " : "false ";
			// echo get_option( "triibo_api_services_php_status"  ) ? "true " : "false ";
		?>
	</form>
</div>