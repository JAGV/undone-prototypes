<?php
/*--------------------------------------------------------------------------------------------------------------

	Plugin Name: Wolf jPlayer
	Plugin URI: http://wpwolf.com/wolf-jplayer
	Description: A WordPress plugin based on the jPlayer jQuery plugin. Allows multiple playlists and supports multiple uploads.
	Version: 2.0.5
	Author: Constantin Saguin
	Author URI: http://wpwolf.com/about/

----------------------------------------------------------------------------------------------------------------*/

class Wolf_Jplayer {

	var $update_url = 'http://plugins.wpwolf.com/update';

	function __construct() {

		define( 'WOLF_JPLAYER_PLUGIN_URL', plugins_url() . '/' . basename( dirname( __FILE__) ) );
		define( 'WOLF_JPLAYER_PLUGIN_DIR', dirname( __FILE__ ) );
		
		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		
		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );
		
		add_action( 'after_setup_theme', array( &$this, 'options_init' ) );
		add_action( 'after_setup_theme',  array( &$this, 'jplayer_show' ) );
		add_action( 'wp_head', array( $this, 'jplayer_custom_styles' ) );
		
		add_action( 'admin_menu', array( &$this, 'add_menu' ) );

		add_action( 'admin_init', array( &$this, 'jplayer_options' ) );
		add_action( 'admin_print_styles', array( &$this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_script' ) );

		add_action( 'admin_init', array( &$this, 'plugin_update' ) );

	}

	// --------------------------------------------------------------------------

	public function activate( $network_wide ) {
		
		$this->create_jplayer_tables();

	} // end activate

	// --------------------------------------------------------------------------

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {

		$domain = 'wolf';
		$locale = apply_filters( 'wolf', get_locale(), $domain );
		load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	} // end plugin_textdomain

	// --------------------------------------------------------------------------

	function create_jplayer_tables() {
		global $wpdb;

		$jplayer_playlists_table = "CREATE  TABLE IF NOT EXISTS `{$wpdb->prefix}wolf_jplayer_playlists` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`name` VARCHAR(255) NOT NULL ,
			`logo` VARCHAR(255) NULL ,
			`autoplay` VARCHAR(20) NULL ,
			PRIMARY KEY (`id`) );";

		$jplayer_table = "CREATE  TABLE IF NOT EXISTS `{$wpdb->prefix}wolf_jplayer` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`mp3` VARCHAR(255) NULL ,
			`ogg` VARCHAR(255) NULL ,
			`name` VARCHAR(255) NULL ,
			`artist` VARCHAR(255) NULL ,
			`poster` VARCHAR(255) NULL ,
			`free` VARCHAR(45) NULL ,
			`external` INT NOT NULL DEFAULT 0 ,
			`itunes` VARCHAR(255) NULL ,
			`amazon` VARCHAR(255) NULL ,
			`buy` VARCHAR(255) NULL ,
			`position` INT NOT NULL DEFAULT 0 ,
			`playlist_id` INT NULL ,
			PRIMARY KEY (`id`) );";

		$wpdb->query($jplayer_playlists_table);
		$wpdb->query($jplayer_table);

	}

	// --------------------------------------------------------------------------

	function delete_tables() {
		global $wpdb;
		$jplayer_playlists_table = "DROP TABLE `{$wpdb->prefix}wolf_jplayer_playlists`;";
		$jplayer_table =  "DROP TABLE `{$wpdb->prefix}wolf_jplayer`;";
		$wpdb->query($jplayer_playlists_table);
		$wpdb->query($jplayer_table);
	}

	// --------------------------------------------------------------------------


	function admin_styles() {
		
		if ( isset( $_GET['page'] ) ) {
			
			if ( $_GET['page'] == 'wolf-jplayer-panel' || $_GET['page'] == 'wolf-jplayer.php' )
				wp_enqueue_style( 'wolf-jplayer-admin', WOLF_JPLAYER_PLUGIN_URL . '/css/jplayer-admin.css', array(), '0.1', 'all');
			
			if ( $_GET['page'] == 'wolf-jplayer-options' )
				wp_enqueue_style( 'wp-color-picker' );
		}
	}

	// --------------------------------------------------------------------------

	function admin_script() {
		if ( isset( $_GET['page'] ) ) {
		
			if ( $_GET['page'] == 'wolf-jplayer-panel' || $_GET['page'] == 'wolf-jplayer.php' ) {
				wp_enqueue_media();
				wp_enqueue_script( 'wolf-jplayer-admin', WOLF_JPLAYER_PLUGIN_URL. ' /js/wolf-jplayer-admin.js', 'jquery', '1.0', true);
				wp_enqueue_script( 'wolf-jplayer-admin-tipsy', WOLF_JPLAYER_PLUGIN_URL. ' /js/min/tipsy.min.js', 'jquery', '1.0', true);
			}
		
			if ( $_GET['page'] == 'wolf-jplayer-panel' )
				wp_enqueue_script( 'jquery-ui-sortable' );
		
   			if ( $_GET['page'] == 'wolf-jplayer-options' )
   				wp_enqueue_script( 'wolf-jplayer-colorpicker', WOLF_JPLAYER_PLUGIN_URL . '/js/min/jplayer-colorpicker.min.js' , array( 'wp-color-picker' ), false, true );
		}
		
	}


	// --------------------------------------------------------------------------


	function add_menu() {
		add_menu_page( 'jPlayer', __( 'Wolf jPlayer', 'wolf'), 'activate_plugins', basename( __FILE__ ), array( &$this, 'jplayer_panel' ) , WOLF_JPLAYER_PLUGIN_URL . '/img/admin/menu.png');
		add_submenu_page( basename( __FILE__ ), '', '', 'activate_plugins',basename( __FILE__ ), array( &$this,'jplayer_panel' ) );
		add_submenu_page( basename( __FILE__ ),  __( 'Manage playlists', 'wolf'), __( 'Manage playlists', 'wolf'), 'activate_plugins', 'wolf-jplayer-panel', array( &$this, 'jplayer_panel' ) );
		add_submenu_page( basename( __FILE__ ),  __( 'Options', 'wolf'), __( 'Options', 'wolf'), 'activate_plugins', 'wolf-jplayer-options', array( &$this, 'wolf_jplayer_settings' ) );
	}

	// --------------------------------------------------------------------------


	function get_option( $value ) {
		global $options;
		$settings = get_option( 'wolf_jplayer_settings');
		
		if ( isset($settings[$value]) )
			return $settings[$value];

	}

	// --------------------------------------------------------------------------

	function jplayer_custom_styles() {
		?>
		<style type="text/css">
		.wolf-jplayer-playlist, .wolf-jplayer-playlist a{
			color: <?php echo $this->get_option( 'font_color') ? $this->get_option( 'font_color') : '#ffffff'; ?>!important;
		}
		.wolf-jplayer-playlist .jp-play-bar, .wolf-jplayer-playlist .jp-volume-bar-value{
			background-color: <?php echo $this->get_option( 'font_color') ? $this->get_option( 'font_color') : '#ffffff'; ?>;
		}
		.wolf-jplayer-playlist div.jp-type-playlist{
			background-color: <?php echo $this->get_option( 'bg_color') ? $this->get_option( 'bg_color') : '#353535'; ?>
		}
		</style>
		<?php
	}

	// --------------------------------------------------------------------------
	
	/**
	* Set default settings
	*/
	function options_init() {
		global $options;

		if ( false ===  get_option( 'wolf_jplayer_settings' )  ) {

			$default = array(
				'bg_color' => '#353535',
				'font_color' => '#ffffff',
				'force_flash' => 'true'
			);

			add_option( 'wolf_jplayer_settings', $default );
		}
	}

	// --------------------------------------------------------------------------

	function jplayer_options() {
		register_setting( 'wolf-jplayer-options', 'wolf_jplayer_settings', array( &$this, 'settings_validate' ) );
		add_settings_section( 'wolf-jplayer-options', '', array( &$this, 'section_intro' ), 'wolf-jplayer-options' );
		add_settings_field( 'color', __( 'Main Color Tone', 'wolf' ), array( &$this, 'section_color' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
		add_settings_field( 'font_color', __( 'Text and Icons Color', 'wolf' ), array( &$this, 'section_font_color' ), 'wolf-jplayer-options', 'wolf-jplayer-options' );
	}

	// --------------------------------------------------------------------------

	function settings_validate($input) {
		return $input;
	}

	// --------------------------------------------------------------------------

	function section_intro() {
		//global $options;
		//echo "<pre>";
		//print_r(get_option( 'wolf_jplayer_settings'));
		//echo "</pre>";
	}

	// --------------------------------------------------------------------------

	function section_color() {
		?>
		<input type="text" value="<?php echo $this->get_option( 'bg_color' ) ?>" class="wolf-jplayer-color" name="wolf_jplayer_settings[bg_color]" />
		<?php
	}

	// --------------------------------------------------------------------------

	function section_font_color() {
		?>
		<input type="text" value="<?php echo $this->get_option( 'font_color' ) ?>" class="wolf-jplayer-color" name="wolf_jplayer_settings[font_color]" />
		<?php
	}

	// --------------------------------------------------------------------------

	/**
	* Form
	*/
	function wolf_jplayer_settings() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php _e( 'Player Color Settings', 'wolf'); ?></h2>
			<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
			<div id="setting-error-settings_updated" class="updated settings-error"> 
				<p><strong><?php _e( 'Settings saved.', 'wolf' ); ?></strong></p>
			</div>
			<?php } ?>
			<form action="options.php" method="post">
				<?php settings_fields( 'wolf-jplayer-options' ); ?>
				<?php do_settings_sections( 'wolf-jplayer-options' ); ?>
				<p class="submit">
					<input name="save" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wolf' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	// --------------------------------------------------------------------------

	function jplayer_panel() {
		require_once( WOLF_JPLAYER_PLUGIN_DIR . '/wolf-jplayer-panel.php' );
	}

	// --------------------------------------------------------------------------

	function jplayer_show() {
		require_once( WOLF_JPLAYER_PLUGIN_DIR . '/wolf-jplayer-show.php' );
	}

	// --------------------------------------------------------------------------

	/**
	 * Plugin update
	 */
	function plugin_update() {
		
		$plugin_data = get_plugin_data( __FILE__ );

		$current_version = $plugin_data['Version'];
		$plugin_slug = plugin_basename( dirname( __FILE__ ) );
		$plugin_path = plugin_basename( __FILE__ );
		$remote_path = $this->update_url . '/' . $plugin_slug;
		
		if ( ! class_exists( 'Wolf_WP_Update' ) )
			include_once('class/class-wp-update.php');
		
		new Wolf_WP_Update( $current_version, $remote_path, $plugin_path );
	}


} // end class
new Wolf_Jplayer;
?>