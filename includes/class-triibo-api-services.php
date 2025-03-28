<?php
/**
 * The file that defines the core plugin class
 * A class definition that includes attributes and functions used in the admin area.
 *
 * @author 		Mateus Costa <mateus@costamateus.com.br>
 * @package 	Triibo_Api_Services/Classes
 * @version 	1.0.0
 */
defined( "ABSPATH" ) || exit;

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks.
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 */
class Triibo_Api_Services
{
	/**
	 * Plugin domain.
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	protected static $name = "triibo_api_services";

	/**
	 * Plugin loader.
	 *
	 * @since 	1.0.0
	 * @var 	Triibo_Api_Services_Loader
	 */
	protected $loader;

	/**
	 * Plugin domain.
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	protected $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	protected $version;

	/**
	 * Prefix of plugin name.
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	protected $prefix;

	/**
	 * Sufix of plugin name.
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	protected $sufix;

	/**
	 *
	 * @since 	1.0.0
	 * @var 	string
	 */
	protected $page;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since 	1.0.0
	 */
	public function __construct()
	{
		$this->plugin_name = self::$name;
		$this->version     = defined( "TRIIBO_API_SERVICES_VERSION" ) ? TRIIBO_API_SERVICES_VERSION : "1.0.0";

		require_once( plugin_dir_path( dirname( __FILE__ ) ) . "includes/class-triibo-api-services-loader.php" );

		$this->loader      = new Triibo_Api_Services_Loader();
		$this->loader->add_action( "admin_enqueue_scripts", $this, "enqueue_styles"  );
		$this->loader->add_action( "admin_enqueue_scripts", $this, "enqueue_scripts" );

		$this->add_actions();
		$this->add_filters();

		require_once( plugin_dir_path( dirname( __FILE__ ) ) . "includes/apis/class-triibo-api.php"         );
		require_once( plugin_dir_path( dirname( __FILE__ ) ) . "includes/apis/class-triibo-api-gateway.php" );
		require_once( plugin_dir_path( dirname( __FILE__ ) ) . "includes/apis/class-triibo-api-node.php"    );
		require_once( plugin_dir_path( dirname( __FILE__ ) ) . "includes/apis/class-triibo-api-php.php"     );
	}

	/**
	 * Return the plugin name
	 *
	 * @since 	1.0.0
	 * @param 	boolean $config 	If true, return the slug for config page
	 * @return 	string
	 */
	public static function get_name( $config = false )
	{
		$name  = self::$name;
		$name .= $config ? "-settings" : "";

		return $name;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . "assets/css/admin.css", [], $this->version, "all" );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . "assets/js/admin.js", [ "jquery" ], $this->version, false );
	}

	/**
	 * Actions
	 *
	 * @since 	1.13.0 	Add action 'woocommerce_new_product'
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function add_actions() : void
	{
		add_action( "admin_menu",              [ $this, "add_admin_menu"            ], 9     );
		add_action( "admin_init",              [ $this, "register_and_build_fields" ]        );
		add_action( "woocommerce_new_product", [ $this, "attach_product_images"     ], 10, 2 );
	}

	/**
	 * Filters
	 *
	 * @since 	1.12.0 	Add filter 'is_protected_meta'
	 * @since 	1.2.0 	Add filter 'plugin_row_meta'
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function add_filters()
	{
		$basename = plugin_basename( TRIIBO_API_SERVICES_FILE );

		add_filter( "plugin_action_links_{$basename}", [ $this, "plugin_action_links"    ]        );
		add_filter( "plugin_row_meta",                 [ $this, "plugin_row_meta"        ], 10, 3 );
		add_filter( "is_protected_meta",               [ $this, "triibo_protected_metas" ], 10, 3 );
	}

	/**
	 * Add submenu on WP Settings menu
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function add_admin_menu()
	{
		add_menu_page(
			"Triibo Services",
			"Triibo Services",
			'manage_options',
			$this->plugin_name,
			[ $this, "display_admin_menu" ],
			plugins_url( "assets/images/triibo20.png", TRIIBO_API_SERVICES_FILE ),
            65
		);

		add_submenu_page(
			$this->plugin_name, // "options-general.php",
			"Triibo API Services",
			"Configurações APIs",
			"manage_options",
			$this->plugin_name . "-settings",
			[ $this, "display_admin_menu_settings" ]
		);
	}

	/**
	 *
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function display_admin_menu()
	{
		if ( !current_user_can( "manage_options" ) ) wp_die( __( "Você não tem permissões suficientes para acessar esta página." ) );

		$active_tab = isset( $_GET[ "tab" ] ) ? $_GET[ "tab" ] : "general";

		if ( isset( $_GET[ "error_message" ] ) )
		{
			add_action( "admin_notices", [ $this, "triibo_api_services_settings_messages" ] );
			do_action( "admin_notices", $_GET[ "error_message" ] );
		}

		require_once ( plugin_dir_path( dirname( __FILE__ ) ) . "templates/" . $this->plugin_name . "-admin-display.php" );
	}

	/**
	 * Undocumented function
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
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
	 * @param 	string $error_message
	 */
	public function triibo_api_services_settings_messages( $error_message )
	{
		switch ( $error_message )
		{
			case "1":
				$message       = __( "There was an error adding this setting. Please try again. If this persists, shoot us an email.", $this->plugin_name );
				$err_code      = esc_attr(  "triibo_api_services-example-setting" );
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
	 * @return 	void
	 */
	public function register_and_build_fields()
	{
		$this->prefix = "triibo_api_services_";
		$this->sufix  = "_section";

		$this->page   = $this->prefix . "general_settings";

		$gate_fields  = [
			[
				"key"   => "gate_status",
				"title" => "Ativar / Desativar",
				"label" => "Ativar API Gateway",
			],[
				"key"   => "gate_env",
				"title" => "Homologação",
				"label" => "Habilitar API de homologação",
				"value" => true,
			],[
				"key"   => "gate_hml_tkn",
				"title" => "Token (homol)",
			],[
				"key"   => "gate_hml_chn_id",
				"title" => "Channel ID (homol)",
			],[
				"key"   => "gate_hml_chn_tkn",
				"title" => "Channel Token (homol)",
			],[
				"key"   => "gate_prd_tkn",
				"title" => "Token",
			],[
				"key"   => "gate_prd_chn_id",
				"title" => "Channel ID",
			],[
				"key"   => "gate_prd_chn_tkn",
				"title" => "Channel Token",
			],
		];

		$node_fields  = [
			[
				"key"   => "node_status",
				"title" => "Ativar / Desativar",
				"label" => "Ativar API Node",
			],[
				"key"   => "node_env",
				"title" => "Homologação",
				"label" => "Habilitar API de homologação",
				"value" => true,
			],[
				"key"   => "node_hml_user",
				"title" => "Usuário (homol)",
			],[
				"key"   => "node_hml_pass",
				"title" => "Senha (homol)",
			],[
				"key"   => "node_prd_user",
				"title" => "Usuário",
			],[
				"key"   => "node_prd_pass",
				"title" => "Senha",
			],
		];

		$php_fields   = [
			[
				"key"   => "php_status",
				"title" => "Ativar / Desativar",
				"label" => "Ativar API PHP",
			],[
				"key"   => "php_env",
				"title" => "Homologação",
				"label" => "Habilitar API de homologação",
				"value" => true,
			],[
				"key"   => "php_hml_key",
				"title" => "Key (homol)",
			],[
				"key"   => "php_hml_tkn",
				"title" => "Token (homol)",
			],[
				"key"   => "php_prd_key",
				"title" => "key",
			],[
				"key"   => "php_prd_tkn",
				"title" => "Token",
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
	 * @param 	string $page
	 * @param 	array $data
	 * @return 	void
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
	 * @param 	string $section
	 * @param 	string $index
	 * @param 	array $field
	 * @return 	void
	 */
	public function add_settings_field( $section, $index, $field )
	{
		$id      = $this->prefix . $field[ "key" ];
		$title   = $field[ "title" ];
		$section = $this->prefix . $section . $this->sufix;

		add_settings_field(
            $id, $title, [ $this, "render_settings_field" ], $this->page, $section,
			[
				"type"             => "input",
				"subtype"          => ( in_array( $index, [ 0, 1 ] ) ) ? "checkbox"        : "text",
				"label"            => ( in_array( $index, [ 0, 1 ] ) ) ? $field[ "label" ] : "",
				"value"            => ( $index == 1 ) ? $field[ "value" ] : "",
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
	 * @param 	array $field
	 * @return 	void
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
	 * @param 	array $args
	 * @return 	void
	 */
	public function message_section( $args )
	{
		if ( strpos( $args[ "id" ], "gateway" ) !== false )
		{
			echo "<p>Configurações da API Gateway</p>";
			echo "<p>Plugins que usam essa API:</p>";
			do_action( "triibo_api_service_list_plugin_gate" );
		}

		if ( strpos( $args[ "id" ], "node"    ) !== false )
		{
			echo "<p>Configurações da API Node</p>";
			echo "<p>Plugins que usam essa API:</p>";
			do_action( "triibo_api_service_list_plugin_node" );
		}

		if ( strpos( $args[ "id" ], "php"     ) !== false )
		{
			echo "<p>Configurações da API PHP</p>";
			echo "<p>Plugins que usam essa API:</p>";
			do_action( "triibo_api_service_list_plugin_php" );
		}
	}

	/**
	 * Renders each field according to the configured args
	 *
	 * @since 	1.0.0
	 * @param 	array $args
	 * @return 	void
	 */
	public function render_settings_field( $args )
	{
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

				if ( $args[ "subtype" ] != "checkbox" )
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

	/**
	 *
	 *
	 * @since 	1.0.0
	 * @param 	array $links
	 * @return 	array
	 */
	public function plugin_action_links( $links )
	{
        $url            = esc_url( admin_url( "admin.php?page=" . $this->plugin_name . "-settings" ) );
		$plugin_links   = [ "<a href='{$url}' >Configurações</a>" ];

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Add link to changelog modal
	 *
	 * @since 	1.2.0
	 * @param 	array 	$plugin_meta
	 * @param 	string 	$plugin_file
	 * @param 	array 	$plugin_data
	 * @return 	array
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data )
	{
		$path = path_join( WP_PLUGIN_DIR, $plugin_file );

		if ( DIRECTORY_SEPARATOR == "\\" )
			$path = str_replace( "/", "\\", $path );

        if ( TRIIBO_API_SERVICES_FILE === $path )
		{
            $url = plugins_url( "readme.txt", TRIIBO_API_SERVICES_FILE );

            $plugin_meta[] = sprintf(
                "<a href='%s' class='thickbox open-plugin-details-modal' aria-label='%s' data-title='%s'>%s</a>",
                add_query_arg( "TB_iframe", "true", $url ),
                esc_attr( sprintf( __( "More information about %s" ), $plugin_data[ "Name" ] ) ),
                esc_attr( $plugin_data[ "Name" ] ),
                __( "Histórico de alterações" )
            );
        }

        return $plugin_meta;
	}

	/**
	 * Registering triibo `meta_keys` as not protected.
	 *
	 * @since 	1.12.0
	 * @param 	bool 	$protected
	 * @param 	string 	$meta_key
	 * @param 	string 	$meta_type
	 * @return 	bool
	 */
	public function triibo_protected_metas( $protected, $meta_key, $meta_type )
	{
		$triibo_metas = [
			"triiboId_phone",

			"_triibo_id",
			"_triibo_phone",
			"_triibo_auth_token",
			"_triibo_auth_validity",
			"_triibo_payments_code",
			"_triibo_security_code",
			"_triibo_assinatura_payment_id",
			"_triibo_assinatura_payment_token",

			"_fs_product_id",
			"_is_integration",
			"_fastshop_order_approved"
		];

		if ( in_array( $meta_key, $triibo_metas ) )
			return false;

		return $protected;
	}

	/**
	 * Updates the post_parent of images associated with a product.
	 *
	 * @since 	1.13.0
	 * @param 	int 	$product_id
	 * @param 	object 	$product
	 * @return 	void
	 */
	public function attach_product_images( $product_id, $product )
	{
		$thumbnail_id = get_post_meta( $product_id, "_thumbnail_id", true );
		$gallery      = get_post_meta( $product_id, "_product_image_gallery", true );

		if ( $thumbnail_id )
			wp_update_post( [ "ID" => $thumbnail_id, "post_parent" => $product_id, ] );

		if ( !empty( $gallery ) )
		{
			$gallery_ids = explode( ",", $gallery );

			foreach ( $gallery_ids as $gallery_id )
				wp_update_post( [ "ID" => $gallery_id, "post_parent" => $product_id, ] );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function run()
	{
		$this->loader->run();
	}
}
