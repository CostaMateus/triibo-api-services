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
	<h2>Configurações | Triibo API Services</h2>

	<?php settings_errors(); ?>

	<form method="POST" action="options.php">
		<?php
			settings_fields( "triibo_api_services_general_settings" );
			do_settings_sections( "triibo_api_services_general_settings" );
			submit_button();
		?>
	</form>
</div>