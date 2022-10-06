<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used in the admin area.
 *
 * @since      1.0.0
 * @package    Triibo_Api_Services
 * @subpackage Triibo_Api_Services/includes
 * @author     Mateus Costa <mateus@costamateus.com.br>
 */

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks.
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 */
class Triibo_Api_Services
{
	protected $loader;
	protected $plugin_name;
	protected $version;
	protected $prefix;
	protected $sufix;
	protected $page;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since 	1.0.0
	 */
	public function __construct()
	{
		$this->plugin_name = "triibo-api-services";
		$this->version     = defined( "TRIIBO_API_SERVICES_VERSION" ) ? TRIIBO_API_SERVICES_VERSION : "1.0.0";

		require_once( plugin_dir_path( dirname( __FILE__ ) ) . "includes/class-triibo-api-services-loader.php" );

		$this->loader = new Triibo_Api_Services_Loader();
		$this->loader->add_action( "admin_enqueue_scripts", $this, "enqueue_styles"  );
		$this->loader->add_action( "admin_enqueue_scripts", $this, "enqueue_scripts" );

		add_action( "admin_menu", [ $this, "add_admin_menu"            ], 9 );
		add_action( "admin_init", [ $this, "register_and_build_fields" ]    );

		add_filter( "plugin_action_links_" . plugin_basename( TRIIBO_API_SERVICES_FILE ), [ $this, "plugin_action_links" ] );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 	1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . "assets/css/admin.css", [], $this->version, "all" );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 	1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . "assets/js/admin.js", [ "jquery" ], $this->version, false );
	}

	/**
	 * Add submenu on WP Settings menu
	 *
	 * @since 	1.0.0
	 */
	public function add_admin_menu()
	{
		add_submenu_page(
			"options-general.php",
			"Serviços de API Triibo",
			"Triibo APIs",
			"manage_options",
			$this->plugin_name . "-settings",
			[ $this, "display_admin_menu_settings" ]
		);
	}

	public function display_admin_menu_settings()
	{
		if ( !current_user_can( "manage_options" ) ) wp_die( __( "Você não tem permissões suficientes para acessar esta página." ) );

		$active_tab = isset( $_GET[ "tab" ] ) ? $_GET[ "tab" ] : "general";

		if ( isset( $_GET[ "error_message" ] ) )
		{
			add_action( "admin_notices", [ $this, "triibo_api_services_settings_messages" ] );
			do_action( "admin_notices", $_GET[ "error_message" ] );
		}

		require_once ( plugin_dir_path( dirname( __FILE__ ) ) . "templates/" . $this->plugin_name . "-admin-settings-display.php" );
	}

	/**
	 * Handles errors and displays messages accordingly
	 *
	 * @since 	1.0.0
	 *
	 * @param string $error_message
	 */
	public function triibo_api_services_settings_messages( $error_message )
	{
		switch ( $error_message )
		{
			case "1":
				$message       = __( "There was an error adding this setting. Please try again. If this persists, shoot us an email.", $this->plugin_name );
				$err_code      = esc_attr(  "triibo_api_services_example_setting" );
				$setting_field = "triibo_api_services_example_setting";
			break;
		}

		$type = "error";

		add_settings_error( $setting_field, $err_code, $message, $type );
	}

	/**
	 * Register and build the plugin fields
	 *
	 * @since 	1.0.0
	 */
	public function register_and_build_fields()
	{
		$this->prefix = "triibo_api_services_";
		$this->sufix  = "_section";

		$this->page   = $this->prefix . "general_settings";

		$gate_fields  = [
			[
				"key"   => "gate_status",
				"value" => "Ativar / Desativar",
				"label" => "Ativar API Gateway"
			],[
				"key"   => "gate_env",
				"value" => "Homologação",
				"label" => "Habilitar API de homologação"
			],[
				"key"   => "gate_hml_tkn",
				"value" => "Token (homol)",
			],[
				"key"   => "gate_hml_chn_id",
				"value" => "Channel ID (homol)",
			],[
				"key"   => "gate_hml_chn_tkn",
				"value" => "Channel Token (homol)",
			],[
				"key"   => "gate_prd_tkn",
				"value" => "Token",
			],[
				"key"   => "gate_prd_chn_id",
				"value" => "Channel ID",
			],[
				"key"   => "gate_prd_chn_tkn",
				"value" => "Channel Token",
			],
		];

		$node_fields  = [
			[
				"key"   => "node_status",
				"value" => "Ativar / Desativar",
				"label" => "Ativar API Node"
			],[
				"key"   => "node_env",
				"value" => "Homologação",
				"label" => "Habilitar API de homologação"
			],[
				"key"   => "node_hml_user",
				"value" => "Usuário (homol)",
			],[
				"key"   => "node_hml_pass",
				"value" => "Senha (homol)",
			],[
				"key"   => "node_prd_user",
				"value" => "Usuário",
			],[
				"key"   => "node_prd_pass",
				"value" => "Senha",
			],
		];

		$php_fields   = [
			[
				"key"   => "php_status",
				"value" => "Ativar / Desativar",
				"label" => "Ativar API PHP"
			],[
				"key"   => "php_env",
				"value" => "Homologação",
				"label" => "Habilitar API de homologação"
			],[
				"key"   => "php_hml_key",
				"value" => "Key (homol)",
			],[
				"key"   => "php_hml_tkn",
				"value" => "Token (homol)",
			],[
				"key"   => "php_prd_key",
				"value" => "key",
			],[
				"key"   => "php_prd_tkn",
				"value" => "Token",
			],
		];

		register_setting( $this->page, $this->prefix . "options" );

		$this->add_allowed_options( $this->page, [ $gate_fields, $node_fields, $php_fields ] );

		add_settings_section( $this->prefix . "gateway" . $this->sufix, "API Gateway", [ $this, "message_section" ], $this->page );
		add_settings_section( $this->prefix . "node"    . $this->sufix, "API Node",    [ $this, "message_section" ], $this->page );
		add_settings_section( $this->prefix . "php"     . $this->sufix, "API PHP",     [ $this, "message_section" ], $this->page );

		foreach ( $gate_fields as $index => $field )
		{
			$this->add_settings_field( "gateway", $index, $field );
			$this->register_settings( $field );
		}

		foreach ( $node_fields as $index => $field )
		{
			$this->add_settings_field( "node",    $index, $field );
			$this->register_settings( $field );
		}

		foreach ( $php_fields  as $index => $field )
		{
			$this->add_settings_field( "php",     $index, $field );
			$this->register_settings( $field );
		}
	}

	/**
	 * Adds plugin fields to allowed options
	 *
	 * @since 	1.0.0
	 *
	 * @param string $page
	 * @param array $data
	 */
	public function add_allowed_options( $page, $data )
	{
		$new_options = [ $page => [] ];

		foreach ( $data as $api )
			foreach ( $api as $field )
				$new_options[ $page ][] = $this->prefix . $field[ "key" ];

		add_allowed_options( $new_options );
	}

	/**
	 * Configure each plugin field
	 *
	 * @since 	1.0.0
	 *
	 * @param string $section
	 * @param string $index
	 * @param array $field
	 */
	public function add_settings_field( $section, $index, $field )
	{
		$id      = $this->prefix . $field[ "key" ];
		$title   = $field[ "value" ];
		$section = $this->prefix . $section . $this->sufix;

		add_settings_field(
            $id, $title, [ $this, "render_settings_field" ], $this->page, $section,
			[
				"type"             => "input",
				"subtype"          => ( in_array( $index, [ 0, 1 ] ) ) ? "checkbox"        : "text",
				"label"            => ( in_array( $index, [ 0, 1 ] ) ) ? $field[ "label" ] : "",
				"id"               => $id,
				"name"             => $id,
				"required"         => true,
				"get_options_list" => "",
				"value_type"       => "normal",
				"wp_data"          => "option"
			]
		);
	}

	/**
	 * Register each plugin field
	 *
	 * @since 	1.0.0
	 *
	 * @param array $field
	 */
	public function register_settings( $field )
	{
		$name = $this->prefix . $field[ "key" ];

		register_setting( $this->page, $name );
	}

	/**
	 * Echos out any content at the top of the section (between heading and fields).
	 *
	 * @since 	1.0.0
	 *
	 * @param array $args
	 */
	public function message_section( $args )
	{
		if ( strpos( $args[ "id" ], "gateway" ) !== false ) echo "<p>Configurações da API Gateway</p>";
		if ( strpos( $args[ "id" ], "node"    ) !== false ) echo "<p>Configurações da API Node.</p>";
		if ( strpos( $args[ "id" ], "php"     ) !== false ) echo "<p>Configurações da API PHP.</p>";
	}

	/**
	 * Renders each field according to the configured args
	 *
	 * @since 	1.0.0
	 *
	 * @param array $args
	 */
	public function render_settings_field( $args )
	{
		// $args = [
		// 	"type"            => "input",
		// 	"subtype"         => "",
		// 	"id"              => $this->plugin_name."_example_setting",
		// 	"name"            => $this->plugin_name."_example_setting",
		// 	"required"        => true, // true or false
		// 	"get_option_list" => "",
		// 	"value_type"      => "", // serialized OR normal,
		// 	"wp_data"         => "", // option     OR post_meta,
		// 	"post_id"         => "",
		// ];

		if ( $args[ "wp_data" ] == "option" )
		{
			$wp_data_value = get_option( $args[ "name" ] );
		}
		elseif ( $args[ "wp_data" ] == "post_meta" )
		{
			$wp_data_value = get_post_meta( $args[ "post_id" ], $args[ "name" ], true );
		}

		switch ( $args[ "type" ] )
		{
			case "input":
				$status = 0;

				if ( strpos( $args[ "id" ], "gateway" ) !== false ) $status = get_option( "triibo_api_services_gate_status" );
				if ( strpos( $args[ "id" ], "node"    ) !== false ) $status = get_option( "triibo_api_services_node_status" );
				if ( strpos( $args[ "id" ], "php"     ) !== false ) $status = get_option( "triibo_api_services_php_status"  );

				$value = ( $args[ "value_type" ] == "serialized" ) ? serialize( $wp_data_value ) : $wp_data_value;

				if ( $args[ "subtype" ] != "checkbox")
				{
					$prependStart = ( isset( $args[ "prepend_value" ] ) ) ? "<div class='input-prepend'> <span class='add-on'>{$args[ "prepend_value" ]}</span>" : "";
					$prependEnd   = ( isset( $args[ "prepend_value" ] ) ) ? "</div>"                   : "";
					$step         = ( isset( $args[ "step"          ] ) ) ? "step='{$args[ "step" ]}'" : "";
					$min          = ( isset( $args[ "min"           ] ) ) ? "min='{$args[ "min" ]}'"   : "";
					$max          = ( isset( $args[ "max"           ] ) ) ? "max='{$args[ "max" ]}'"   : "";
					$required     = ( $args[ "required" ] && $status    ) ? "required='required'"      : "";

					if ( isset( $args[ "disabled" ] ) )
					{
						// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart . "
							<input type='{$args[ "subtype" ]}' id='{$args[ "id" ]}_disabled' {$step} {$max} {$min} name='{$args[ "name" ]}_disabled' disabled value='" . esc_attr( $value ) . "' />
							<input type='hidden' id='{$args[ "id" ]}' {$step} {$max} {$min} name='{$args[ "name" ]}' value='" . esc_attr( $value ) . "' />" . $prependEnd;
					}
					else
					{
						echo $prependStart . "
							<input type='{$args[ "subtype" ]}' id='{$args[ "id" ]}' {$required} {$step} {$max} {$min} name='{$args[ "name" ]}' value='" . esc_attr( $value ) . "' />" . $prependEnd;
					}
				}
				else
				{
					$checked = ( $value ) ? "checked" : "";
					echo "<input type='{$args[ "subtype" ]}' id='{$args[ "id" ]}' name='{$args[ "name" ]}' {$checked} />";
					echo "<label for='{$args[ "id" ]}' >{$args[ "label" ]}</label>";
				}
			break;

			// default:
			// 	# code...
			// break;
		}
	}

	public function plugin_action_links( $links )
	{
        $url            = esc_url( admin_url( "options-general.php?page=" . $this->plugin_name . "-settings" ) );
		$plugin_links   = [ "<a href='{$url}' >Configurações</a>" ];

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 	1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}
}
