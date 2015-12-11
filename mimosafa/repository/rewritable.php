<?php
namespace mimosafa\WP\Repository;
/**
 * Rewritable Repository Abstract Class
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
abstract class Rewritable extends Repository {

	protected static $instances = [];
	protected static $ids = [];

	protected static $post_types = [];
	protected static $taxonomies = [];

	abstract public function regulate();

	protected function __construct( $name, $id, Array $args, $builtin ) {
		parent::__construct( $name, $id, $args, $builtin );
		add_action( 'init', [ $this, 'regulate' ], 0 );
		static $done;
		if ( ! $done ) {
			add_action( 'init', [ $this, 'register_taxonomies' ], 1 );
			add_action( 'init', [ $this, 'register_post_types' ], 1 );
			$done = true;
		}
	}

	public function register_taxonomies() {
		if ( self::$taxonomies ) {
			foreach ( self::$taxonomies as $tx ) {
				/**
				 * @var string $taxonomy
				 * @var array  $object_type
				 * @var array  $args
				 */
				extract( $tx, EXTR_OVERWRITE );

				register_taxonomy( $taxonomy, $object_type, $args );
				/**
				 * Built-in object types
				 */
				if ( $object_type ) {
					foreach ( (array) $object_type as $object ) {
						if ( post_type_exists( $object ) ) {
							register_taxonomy_for_object_type( $taxonomy, $object );
						}
					}
				}
			}
		}
	}

	public function register_post_types() {
		if ( self::$post_types ) {
			/**
			 * Theme support: post-thumbnails
			 *
			 * @var boolean
			 */
			static $thumbnail_supported;
			if ( ! isset( $thumbnail_supported ) ) {
				$thumbnail_supported = current_theme_supports( 'post-thumbnails' );
			}
			/**
			 * Theme support: post-formats
			 *
			 * @var boolean
			 */
			static $post_formats_supported;
			if ( ! isset( $post_formats_supported ) ) {
				$post_formats_supported = current_theme_supports( 'post-formats' );
			}
			foreach ( self::$post_types as $pt ) {
				/**
				 * @var string $post_type
				 * @var array  $args
				 */
				extract( $pt, EXTR_OVERWRITE );
				/**
				 * Taxonomies
				 */
				if ( self::$taxonomies ) {
					$taxonomies = [];
					foreach ( self::$taxonomies as $tx ) {
						if ( in_array( $post_type, $tx['object_type'], true ) ) {
							$taxonomies[] = $tx['taxonomy'];
						}
					}
					if ( $taxonomies ) {
						if ( ! isset( $args['taxonomies'] ) || ! is_array( $args['taxonomies'] ) ) {
							$args['taxonomies'] = array_unique( array_merge( $args['taxonomies'], $taxonomies ) );
						}
					}
				}
				/**
				 * Theme supports.
				 */
				if ( ! $thumbnail_supported && isset( $args['supports'] ) && in_array( 'thumbnail', (array) $args['supports'], true ) ) {
					add_theme_support( 'post-thumbnails' );
					$thumbnail_supported = true;
				}
				if ( ! $post_formats_supported && isset( $args['supports'] ) && in_array( 'post-formats', (array) $args['supports'], true ) ) {
					add_theme_support( 'post-formats' );
					$post_formats_supported = true;
				}
				register_post_type( $post_type, $args );
			}
		}
	}

}
