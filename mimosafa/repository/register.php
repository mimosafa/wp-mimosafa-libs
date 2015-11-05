<?php
namespace mimosafa\WP\Repository;
/**
 * WordPress Repositories Register Class
 *
 * @access private
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Register {

	/**
	 * @var array
	 */
	private $taxonomies = [];
	private $post_types = [];

	/**
	 * Constructor
	 *
	 * @access private
	 */
	private function __construct() {
		add_action( 'init', [ $this, 'register_taxonomies' ], 1 );
		add_action( 'init', [ $this, 'register_post_types' ], 1 );
	}

	/**
	 * Instance Getter (Singleton Pattern)
	 *
	 * @access private
	 *
	 * @return mimosafa\WP\Repository\Register
	 */
	private static function instance() {
		static $instance;
		return $instance ?: $instance = new self();
	}

	/**
	 * Taxonomy Registration
	 *
	 * @access public
	 *
	 * @param  string        $post_type
	 * @param  array|string  $$object_type
	 * @param  array         $args
	 * @return void
	 */
	public static function taxonomy( $taxonomy, $object_type = [], $args = [] ) {
		if ( $taxonomy = filter_var( $taxonomy ) ) {
			$self = self::instance();
			$self->taxonomies[] = [
				'taxonomy'    => $taxonomy,
				'object_type' => (array) $object_type,
				'args'        => (array) $args
			];
		}
	}

	/**
	 * Post Type Registration
	 *
	 * @access public
	 *
	 * @param  string $post_type
	 * @param  array  $args
	 * @return void
	 */
	public static function post_type( $post_type, $args = [] ) {
		if ( $post_type = filter_var( $post_type ) ) {
			$self = self::instance();
			$self->post_types[] = [
				'post_type' => $post_type,
				'args'      => (array) $args
			];
		}
	}

	/**
	 * Register Taxonomies
	 *
	 * @access private
	 *
	 * @uses   mimosafa\WP\Repository\Taxonomy\Labels::init()
	 */
	public function register_taxonomies() {
		if ( ! empty( $this->taxonomies ) ) {
			foreach ( $this->taxonomies as $tx ) {
				/**
				 * @var string $taxonomy
				 * @var array  $object_type
				 * @var array  $args
				 */
				extract( $tx, EXTR_OVERWRITE );
				Taxonomy\Labels::init( $taxonomy, $args );
				register_taxonomy( $taxonomy, $object_type, $args );
				if ( $object_type ) {
					foreach ( $object_type as $obj ) {
						if ( post_type_exists( $obj ) ) {
							/**
							 * Built-in Post Types
							 */
							register_taxonomy_for_object_type( $taxonomy, $obj );
						}
					}
				}
			}
		}
	}

	/**
	 * Register Post Types
	 *
	 * @access private
	 *
	 * @uses   mimosafa\WP\Repository\PostType\Labels::init()
	 */
	public function register_post_types() {
		if ( ! empty( $this->post_types ) ) {
			/**
			 * Theme Support: Thumbnail
			 *
			 * @var boolean
			 */
			static $thumbnail_supported;
			if ( ! isset( $thumbnail_supported ) ) {
				$thumbnail_supported = current_theme_supports( 'post-thumbnails' );
			}
			foreach ( $this->post_types as $pt ) {
				/**
				 * @var string $post_type
				 * @var array  $args
				 */
				extract( $pt, EXTR_OVERWRITE );
				PostType\Labels::init( $post_type, $args );

				/**
				 * Registered Object Type
				 */
				if ( $this->taxonomies ) {
					$taxonomies = [];
					foreach ( $this->taxonomies as $taxonomy ) {
						if ( in_array( $post_type, $taxonomy['object_type'], true ) ) {
							$taxonomies[] = $taxonomy['taxonomy'];
						}
					}
					if ( $taxonomies ) {
						if ( ! isset( $args['taxonomies'] ) ) {
							$args['taxonomies'] = [];
						}
						$args['taxonomies'] = array_unique( array_merge( $args['taxonomies'], $taxonomies ) );
					}
				}
				if ( ! $thumbnail_supported && isset( $args['supports'] ) && in_array( 'thumbnail', (array) $args['supports'], true ) ) {
					add_theme_support( 'post-thumbnails' );
					$thumbnail_supported = true;
					// Triger Error ?
				}
				register_post_type( $post_type, $args );
			}
		}
	}

}
