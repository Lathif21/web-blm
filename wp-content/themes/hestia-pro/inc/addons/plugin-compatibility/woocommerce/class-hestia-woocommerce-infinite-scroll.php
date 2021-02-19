<?php
/**
 * Class for infinite scroll in on shop page
 *
 * @package Hestia
 */

/**
 * Class Hestia_WooCommerce_Infinite_Scroll
 */
class Hestia_WooCommerce_Infinite_Scroll {

	/**
	 * Determine if infinite scroll script should be loaded.
	 *
	 * @return bool
	 */
	private function should_enqueue_infinite_scroll() {
		if ( is_shop() === false ) {
			return false;
		}
		$hestia_pagination_type = get_theme_mod( 'hestia_shop_pagination_type', 'number' );
		return $hestia_pagination_type === 'infinite';
	}

	/**
	 * Register actions and filters
	 */
	public function init() {
		add_action( 'template_redirect', array( $this, 'action_template_redirect' ) );
		add_action( 'template_redirect', array( $this, 'ajax_response' ) );
		add_action( 'custom_ajax_infinite_scroll', array( $this, 'query' ) );
		add_action( 'infinite_scroll_render', array( $this, 'render' ) );
	}

	/**
	 * Does the legwork to determine whether the feature is enabled.
	 *
	 * @return bool
	 */
	public function action_template_redirect() {

		if ( ! $this->should_enqueue_infinite_scroll() ) {
			return false;
		}

		wp_register_script(
			'hestia-woo-infinite-scroll',
			get_template_directory_uri() . '/inc/addons/plugin-compatibility/woocommerce/script.js',
			array(
				'jquery',
			),
			HESTIA_VERSION,
			true
		);

		if ( self::is_last_batch() ) {
			return false;
		}

		wp_enqueue_script( 'hestia-woo-infinite-scroll' );

		add_action( 'wp_footer', array( $this, 'action_wp_footer_settings' ), 2 );

		return true;
	}

	/**
	 * Prints the relevant infinite scroll settings in JS.
	 */
	public function action_wp_footer_settings() {

		$js_settings = array(
			'id'            => 'woo-products-wrap',
			'base_url'      => esc_url_raw( self::get_base_url() ),
			'url_args'      => self::get_url_args(),
			'wrapper_class' => 'products',
		);

		?>
		<script type="text/javascript">
			//<![CDATA[
			var infiniteScroll = JSON.parse( decodeURIComponent( ' <?php echo rawurlencode( json_encode( array( 'settings' => $js_settings ) ) ); ?> ' ) );
			//]]>
		</script>
		<?php
	}

	/**
	 * Our own Ajax response, avoiding calling admin-ajax
	 */
	public function ajax_response() {
		if ( ! self::is_infinite_request() ) {
			return false;
		}

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		send_nosniff_header();
		do_action( 'custom_ajax_infinite_scroll' );
		die( '0' );
	}

	/**
	 * Runs the query and returns the results via JSON.
	 * Triggered by an AJAX request.
	 *
	 * @return string | bool
	 */
	function query() {
		$results = array();

		if ( have_posts() ) {
			ob_start();
			wp_head();
			while ( ob_get_length() ) {
				ob_end_clean();
			}

			$results['type'] = 'success';

			rewind_posts();
			ob_start();

			do_action( 'infinite_scroll_render' );

			$results['html'] = ob_get_clean();

			ob_start();
			wp_footer();
			while ( ob_get_length() ) {
				ob_end_clean();
			}

			if ( 'success' === $results['type'] ) {
				$results['lastbatch'] = self::is_last_batch();
			}
		}
		wp_send_json( $results );
		return true;
	}

	/**
	 * Add a default renderer for WooCommerce products within infinite scroll.
	 */
	public function render() {
		if ( ! is_shop() ) {
			return false;
		}

		woocommerce_product_loop_start();

		while ( have_posts() ) {
			the_post();
			wc_get_template_part( 'content', 'product' );
		}

		woocommerce_product_loop_end();

		return true;
	}

	/**
	 * Has infinite scroll been triggered?
	 */
	static function is_infinite_request() {
		return isset( $_GET['hestia-infinite-scroll'] );
	}

	/**
	 * Check whether or not this is the last batch for a request
	 */
	static function is_last_batch() {

		global $wp_the_query;

		$entries        = (int) $wp_the_query->found_posts;
		$posts_per_page = $wp_the_query->get( 'posts_per_page' );
		$paged          = max( 1, $wp_the_query->get( 'paged' ) );

		if ( $entries <= $posts_per_page ) {
			return true;
		}

		if ( $paged && $paged > 1 ) {
			$entries -= $posts_per_page * $paged;
		}

		return $entries <= 0;
	}

	/**
	 * Get shop url.
	 *
	 * @return bool|string
	 */
	function get_base_url() {
		$shop_page_id  = wc_get_page_id( 'shop' );
		$shop_page_url = $shop_page_id ? get_permalink( $shop_page_id ) : '';
		if ( empty( $shop_page_id ) ) {
			return false;
		}
		return $shop_page_url;
	}

	/**
	 * Get args form url.
	 *
	 * @return array
	 */
	function get_url_args() {
		$args = array( 'hestia-infinite-scroll' => true );
		if ( isset( $_GET ) ) {
			foreach ( $_GET as $key => $value ) {
				$args[ $key ] = $value;
			}
		}
		return json_encode( $args );
	}
};

/**
 * Early accommodation of the Infinite Scroll AJAX request
 */
if ( Hestia_WooCommerce_Infinite_Scroll::is_infinite_request() ) {
	/**
	 * If we're sure this is an AJAX request (i.e. the HTTP_X_REQUESTED_WITH header says so),
	 * indicate it as early as possible for actions like init
	 */
	if ( ! defined( 'DOING_AJAX' ) && isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtoupper( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'XMLHTTPREQUEST' ) {
		define( 'DOING_AJAX', true );
	}

	// Don't load the admin bar when doing the AJAX response.
	show_admin_bar( false );
}
