<?php
/**
 * Plugin Name:     Easy Digital Downloads - Conditional Emails
 * Plugin URI:      https://easydigitaldownloads.com/extensions/conditional-emails
 * Description:     Send notification emails based on conditional events
 * Version:         1.1.0
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     edd-conditional-emails
 *
 * @package         EDD\ConditionalEmails
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright       Copyright (c) 2014, Daniel J Griffiths
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( ! class_exists( 'EDD_Conditional_Emails' ) ) {


	/**
	 * Main EDD_Conditional_Emails class
	 *
	 * @since       1.0.0
	 */
	class EDD_Conditional_Emails {


		/**
		 * @var         EDD_Conditional_Emails $instance The one true EDD_Conditional_Emails
		 * @since       1.0.0
		 */
		private static $instance;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      self::$instance The one true EDD_Conditional_Emails
		 */
		public static function instance() {
			if( ! self::$instance ) {
				self::$instance = new EDD_Conditional_Emails();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function setup_constants() {
			// Plugin version
			define( 'EDD_CONDITIONAL_EMAILS_VER', '1.1.0' );

			// Plugin path
			define( 'EDD_CONDITIONAL_EMAILS_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'EDD_CONDITIONAL_EMAILS_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {
			require_once EDD_CONDITIONAL_EMAILS_DIR . 'includes/scripts.php';
			require_once EDD_CONDITIONAL_EMAILS_DIR . 'includes/functions.php';
			require_once EDD_CONDITIONAL_EMAILS_DIR . 'includes/actions.php';
			require_once EDD_CONDITIONAL_EMAILS_DIR . 'includes/post-types.php';

			if( is_admin() ) {
				require_once EDD_CONDITIONAL_EMAILS_DIR . 'includes/admin/settings/register.php';
				require_once EDD_CONDITIONAL_EMAILS_DIR . 'includes/admin/actions.php';
				require_once EDD_CONDITIONAL_EMAILS_DIR . 'includes/admin/pages.php';
			}
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function hooks() {
			// Handle licensing
			if( class_exists( 'EDD_License' ) ) {
				$license = new EDD_License( __FILE__, 'Conditional Emails', EDD_CONDITIONAL_EMAILS_VER, 'Daniel J Griffiths' );
			}
		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_conditional_emails_lang_dir', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), '' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-conditional-emails', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-conditional-emails/' . $mofile;

			if( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-conditional-emails/ folder
				load_textdomain( 'edd-conditional-emails', $mofile_global );
			} elseif( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-conditional-emails/languages/ folder
				load_textdomain( 'edd-conditional-emails', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-conditional-emails', false, $lang_dir );
			}
		}
	}
}


/**
 * The main function responsible for returning the one true EDD_Conditional_Emails
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      EDD_Conditional_Emails The one true EDD_Conditional_Emails
 */
function edd_conditional_emails_load() {
	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if( ! class_exists( 'EDD_Extension_Activation' ) ) {
			require_once 'includes/libraries/class.extension-activation.php';
		}

		$activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

		return EDD_Conditional_Emails::instance();
	} else {
		return EDD_Conditional_Emails::instance();
	}
}
add_action( 'plugins_loaded', 'edd_conditional_emails_load' );
