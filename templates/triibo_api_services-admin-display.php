<?php
/**
 * Provide a admin area view for the plugin
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @author 		Mateus Costa <mateus@costamateus.com.br>
 * @package 	Triibo_Api_Services
 * @version 	1.0.0
 */
?>

<div class="wrap">
	<h2>Triibo API Services</h2>

	<h3>Plugins do ecossistema</h3>
	<?php do_action( "triibo_api_service_add_button" ); ?>
	<hr>

	<h3>Exemplo de código para adicionar submenu ao menu 'Triibo Services'</h3>
	<pre>
		add_action( "admin_menu", [ $this, "add_submenu_page" ], 11 );
		public function add_submenu_page()
		{
			add_submenu_page(
				Triibo_Api_Services::get_name(),
				"Triibo Assinaturas",
				"Triibo Assinaturas",
				"manage_options",
				"triibo_assinaturas-settings",
				[ $this, "display_admin_menu_settings" ]
			);
		}
	</pre>
	<hr>

	<h3>Exemplo para adicionar link para configurações nesta página</h3>
	<pre>
		add_action( "triibo_api_service_add_button", [ $this, "add_btn" ], 11 );
		public function add_btn()
		{
			$url  = esc_url( admin_url( "admin.php?page=wc-settings&tab=checkout&section=triibo_assinaturas" ) );
			$text = __( "Configurações", "triibo_assinaturas" );
			$link = "&lt;a href='{$url}' &gt;{$text}&lt;/a&gt;";
			echo "&lt;li&gt;&lt;p&gt;Triibo Assinaturas | {$link}&lt;/p&gt;&lt;/li&gt;";
		}
	</pre>

</div>
