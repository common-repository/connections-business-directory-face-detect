<?php

class Connections_Face_Detect_Async_Task extends WP_Async_Task {

	private $image = array();

	protected $argument_count = 3;

	protected $action = 'cn_image_get';

	/**
	 * Prepare data for the asynchronous request.
	 * NOTE: The return array will be sent via a non-blocking wp_remote_post() request.
	 *
	 * @throws Exception If for any reason the request should not happen
	 *
	 * @param array $data An array of data sent to the hook
	 *
	 * @return array
	 */
	protected function prepare_data( $data ) {

		$this->image[] = array(
			'atts'   => $data[0],
			'source' => $data[1],
			'return' => $data[2],
			);

		return array( 'data' => json_encode( $this->image ) );
	}

	/**
	 * Run the async task action.
	 * NOTE: The data from $this->prepare_data will be available via $_POST.
	 */
	protected function run_action() {

		$data = json_decode( stripslashes( $_POST['data'] ), TRUE );

		foreach ( $data as $item ) {

			$atts   = $item['atts'];
			$source = $item['source'];
			$return = $item['return'];

			do_action( "wp_async_$this->action", $atts, $source, $return );

		}

	}

	/**
	 * Overriding this method so the use_curl_transport filter can be added and removed.
	 * This is because curl does not seem to support non-blocking requests and that is the
	 * default transport used by WP.
	 *
	 * @url https://core.trac.wordpress.org/ticket/18738
	 *
	 * @todo  Since this method is already being overridden... should add a filter on
	 * $this->_body_data to remove any image already in the face detect option cache.
	 * Or, maybe not, if it already being done in the async request there might not be
	 * any benefit. What should be done is add a processing queue and the face detect
	 * only processes that queue ... lets sleep on it.
	 */
	public function launch_on_shutdown() {
		if ( ! empty( $this->_body_data ) ) {
			$cookies = array();
			foreach ( $_COOKIE as $name => $value ) {
				$cookies[] = "$name=" . urlencode( is_array( $value ) ? serialize( $value ) : $value );
			}

			$request_args = array(
				'timeout'   => 0.01,
				'blocking'  => false,
				'sslverify' => apply_filters( 'https_local_ssl_verify', true ),
				'body'      => $this->_body_data,
				'headers'   => array(
					'cookie' => implode( '; ', $cookies ),
				),
			);

			$url = admin_url( 'admin-post.php' );

			// This line added.
			add_filter( 'use_curl_transport',  array( $this, '__return_false' ) );

			wp_remote_post( $url, $request_args );

			// This line added.
			remove_filter( 'use_curl_transport',  array( $this, '__return_false' ) );
		}
	}

	/**
	 * Using this rather than the WP core __return_false so it can be removed.
	 */
	public function __return_false() {

		return false;
	}

}
