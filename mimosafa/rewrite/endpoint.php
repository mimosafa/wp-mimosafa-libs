<?php
namespace mimosafa\WP\Rewrite;
/**
 * Custom Endpoints
 *
 * @access public
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Endpoint {

	/**
	 * @var array
	 */
	private $endpoints = [];

	/**
	 * @var array
	 */
	private $query_vars = [];

	/**
	 * Default Arguments
	 *
	 * @var array
	 */
	private static $defaults = [
		'places'    => 0, // EP_NONE
		'query_var' => true,
		'rewrite'   => '',
	];

	/**
	 * Constructor
	 *
	 * @access private
	 */
	private function __construct() {
		add_action( 'init', [ $this, 'add_rewrite_endpoints' ], 1 );
	}

	/**
	 * Instance Getter (Singleton Pattern)
	 *
	 * @access private
	 *
	 * @return mimosafa\WP\Rewrite\Endpoints
	 */
	private static function instance() {
		static $instance;
		return $instance ?: $instance = new self();
	}

	/**
	 * Registration
	 *
	 * @access public
	 *
	 * @param  string $endpoint
	 * @param  array  $args
	 * @return void
	 */
	public static function register( $endpoint, $args = [] ) {
		$args = wp_parse_args( $args, self::$defaults );
		$args = (object) $args;
		$endpoint = sanitize_key( $endpoint );
		if ( empty( $endpoint ) ) {
			return; // WP_Error
		}
		if ( ! is_int( $args->places ) || ! $args->places ) {
			$args->places = \EP_ROOT;
		}
		if ( $args->query_var !== false ) {
			$args->query_var = $endpoint;
		}
		if ( ! $args->rewrite ) {
			$args->rewrite = $endpoint;
		}
		$self = self::instance();
		$self->endpoints[$endpoint] = $args;
	}

	/**
	 * Add Rewrite Endpoints
	 *
	 * @access private
	 */
	public function add_rewrite_endpoints() {
		if ( ! empty( $this->endpoints ) ) {
			foreach ( $this->endpoints as $endpoint => $args ) {
				add_rewrite_endpoint( $args->rewrite, $args->places, $args->query_var );
				if ( $args->query_var ) {
					$this->query_vars[$args->query_var] = $args;
				}
			}
			if ( $this->query_vars ) {
				add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
				add_action( 'template_redirect', [ $this, 'template_redirect' ] );
			}
		}
	}

	/**
	 * Add Query Vars for Endpoints
	 *
	 * @access public
	 *
	 * @param  array $public_query_vars
	 * @return array
	 */
	public function add_query_vars( $public_query_vars ) {
		foreach ( array_keys( $this->query_vars ) as $var ) {
			$public_query_vars[] = $var;
		}
		return $public_query_vars;
	}

	/**
	 * Do 'template_redirect' Actions for Endpoints
	 *
	 * @access public
	 */
	public function template_redirect() {
		global $wp_query;
		foreach ( $this->query_vars as $var => $args ) {
			if ( isset( $wp_query->query[$var] ) ) {
				do_action( 'template_redirect_' . $var, $wp_query->query[$var], $args );
				break;
			}
		}
	}

}
