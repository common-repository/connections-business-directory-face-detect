<?php
/**
 * An extension for the Connections plugin attempts to do face detection when cropping an image to prevent the face from being cropped out of the image.
 *
 * @package   Connections Face Detect
 * @category  Extension
 * @author    Steven A. Zahm
 * @license   GPL-2.0+
 * @link      https://connections-pro.com
 * @copyright 2021 Steven A. Zahm
 *
 * @wordpress-plugin
 * Plugin Name:       Connections Face Detect
 * Plugin URI:        https://connections-pro.com
 * Description:       An extension for the Connections plugin attempts to do face detection when cropping an image to prevent the face from being cropped out of the image.
 * Version:           1.1
 * Author:            Steven A. Zahm
 * Author URI:        https://connections-pro.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       connections_face_crop
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists('Connections_Face_Detect') ) {

	class Connections_Face_Detect {

		public function __construct() {

			self::defineConstants();
			self::includes();

			// Init the async task.
			new Connections_Face_Detect_Async_Task;

			add_filter( 'cn_get_image_atts', array( __CLASS__, 'crop_focus' ), 10, 3 );

			add_action( 'wp_async_cn_image_get', array( __CLASS__, 'detect' ), 10, 3 );

		}

		/**
		 * Define the constants.
		 *
		 * @access  private
		 * @static
		 * @since   1.0
		 *
		 * @return  void
		 */
		private static function defineConstants() {

			define( 'CNFD_CURRENT_VERSION', '1.0' );
			define( 'CNFD_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'CNFD_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'CNFD_PATH', plugin_dir_path( __FILE__ ) );
			define( 'CNFD_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Include the plugin dependencies.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return void
		 */
		public static function includes() {

			// Include WP Asynchronous Tasks
			require_once CNFD_PATH . 'vendor/wp-async-task/wp-async-task.php';

			// Include the Face Crop Async Task
			require_once CNFD_PATH . 'includes/async-task.php';
		}

		public static function activate() {


		}

		/**
		 * Callback for the cn_get_image_atts filter.
		 *
		 * This will check the face detect option cache to see if the
		 * $source image has been processed through face detect and use the
		 * stored crop_focus if it has.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 * @param  array  $atts   The associative array that is passed to an instance of cnImage::get() when processing an image.
		 * @param  string $source The image URL or absolute path. NOTE: The onl valid pathhs will be: WP_CONTENT/UPLOADS or STYLESHEETPATH
		 * @param  string $return What to return, @see cnImage::get()
		 *
		 * @return array          An associative array of options for cnImage::get().
		 */
		public static function crop_focus( $atts, $source, $return ) {

			$cache = cnCache::get( 'face_detect', 'option-cache', 'cnfc' );

			$image = $cache != FALSE ? json_decode( $cache, TRUE ) : FALSE;

			$name = basename( $source );

			if ( $image != FALSE && isset( $image[ $name ] ) ) {

				$atts['crop_focus'] = $image[ $name ]['crop_focus'];
			}

			return $atts;
		}

		/**
		 * The callback ran in the async request to do the face detection.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 * @param  array  $atts   The associative array that is passed to an instance of cnImage::get() when processing an image.
		 * @param  string $source The image URL or absolute path. NOTE: The onl valid pathhs will be: WP_CONTENT/UPLOADS or STYLESHEETPATH
		 * @param  string $return What to return, @see cnImage::get()
		 *
		 * @return void
		 */
		public static function detect( $atts, $source, $return ) {

			if ( ! is_wp_error( $info = cnImage::info( $source ) ) ) {

				$name  = $info['basename'];

				$cache = cnCache::get( 'face_detect', 'option-cache', 'cnfc' );

				$image = $cache != FALSE ? json_decode( $cache, TRUE ) : array();

				if ( $image == FALSE || ( ! isset( $image[ $name ] ) || $image[ $name ]['modified'] != $info['modified'] ) ) {

					if ( ! class_exists( 'svay\FaceDetector' ) ) {

						include_once CNFD_PATH . 'vendor/facedetector/FaceDetector.php';
					}

					$detect = new svay\FaceDetector();
					$detect->faceDetect( $info['path'] );

					$coord = $detect->getFace();

					if ( ! is_null( $coord ) ) {

						$atts['crop_focus'] = array(
							( $coord['x'] + ( $coord['w'] / 2 ) ) / $info[0],
							$coord['y'] / $info[1]
							);
					}

					$image[ $name ]['path']       = $info['path'];
					$image[ $name ]['modified']   = $info['modified'];
					$image[ $name ]['mime']       = $info['mime'];
					$image[ $name ]['width']      = $info[0];
					$image[ $name ]['height']     = $info[1];
					$image[ $name ]['face']       = $coord;
					$image[ $name ]['crop_focus'] = $atts['crop_focus'];

					if ( ! is_wp_error( $result = cnImage::get( $source, $atts ) ) ) {

						cnCache::set( 'face_detect', json_encode( $image ), YEAR_IN_SECONDS, 'option-cache', 'cnfc' );
					}

				}

			}

		}

	}

	/**
	 * Start up the extension.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return Connections_Face_Detect|false
	 */
	function Connections_Face_Detect() {

		if ( class_exists( 'connectionsLoad' ) ) {

			return new Connections_Face_Detect();

		} else {

			add_action(
				'admin_notices',
				function() {
					echo '<div id="message" class="error"><p><strong>ERROR:</strong> Connections must be installed and active in order use Connections Face Detect.</p></div>';
				}
			);

			return false;
		}
	}

	/**
	 * Since Connections loads at default priority 10, and this extension is dependent on Connections,
	 * we'll load with priority 11, so we know Connections will be loaded and ready first.
	 */
	add_action( 'plugins_loaded', 'Connections_Face_Detect', 11 );

}
