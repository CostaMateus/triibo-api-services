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
	<h2>Triibo API Services</h2>

	<!-- <h3>Plugins do ecossistema</h3> -->

	<?php
		do_action( "triibo_api_service_add_button" );
	?>

</div>
