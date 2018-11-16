<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Boo_Ajax_Helper
 *
 * This is utility helper class
 *
 * @version 1.0
 *
 * @author RaoAbid | BooSpot
 * @link https://github.com/boospot/boo-ajax-helper
 */
if ( ! class_exists( 'Boo_Ajax_Helper' ) ):

	class Boo_Ajax_Helper {

		public $action_name;

		public $script_handle;

		public $script_url;

		public $config = array();

		public function __construct( $config ) {

			$this->set_properties( $config );
			$this->setup_hooks();

		}


		public function set_properties( $config ) {

			$this->config = wp_parse_args( $config, $this->default_config() );

			$this->action_name   = $this->config['action_name'];
			$this->script_handle = $this->config['script_handle'];
			$this->script_url    = $this->config['script_url'];


		}

		public function default_config() {

			return array(
				'action_name'   => 'plugin_name',
				'script_handle' => 'plugin-name',
				'script_url'    => '',
				'callback'      => ''
			);

		}


		public function setup_hooks() {

			add_action( 'wp_enqueue_scripts', array( $this, 'ajax_enqueue_scripts' ) );

			// ajax hook for logged-in users: wp_ajax_{action}
			add_action( 'wp_ajax_public_hook', array( $this, 'ajax_public_handler' ) );

			// ajax hook for non-logged-in users: wp_ajax_nopriv_{action}
			add_action( 'wp_ajax_nopriv_public_hook', array( $this, 'ajax_public_handler' ) );

		}


		function ajax_public_handler() {

			// check nonce
			check_ajax_referer( $this->action_name, 'nonce' );
			// Do Your Magic

			if ( isset( $this->config['callback'] ) && is_callable( $this->config['callback'] ) ) {
				call_user_func( $this->config['callback'] );
			};

			// End your magic
			wp_die();
		}


		public function ajax_enqueue_scripts( $hook ) {

			// enqueue script
			wp_enqueue_script( $this->script_handle, $this->script_url, array( 'jquery' ) );

			// create nonce for action name
			$nonce = wp_create_nonce( $this->action_name );

			// define ajax url
			$ajax_url = admin_url( 'admin-ajax.php' );


			// define script
			$js_vars = array( 'nonce' => $nonce, 'ajaxurl' => $ajax_url );

			// localize script
			wp_localize_script( $this->script_handle, $this->action_name, $js_vars );

		}


	}

endif;
