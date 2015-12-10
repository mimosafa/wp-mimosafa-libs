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
				//
				register_taxonomy( $taxonomy, $object_type, $args );
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

				register_post_type( $post_type, $args );
			}
		}
	}

}
