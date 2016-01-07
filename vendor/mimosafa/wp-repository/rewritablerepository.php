<?php
namespace mimosafa\WP\Repository;

/**
 * Abstract rewritable repository class.
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 *
 * @uses mimosafa\WP\Repository\Repository
 */
abstract class RewritableRepository extends Repository {

	/**
	 * Instances, whole post types & taxonomies created by this class.
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Post types & Taxonomies `alias` <=> `name` map.
	 *
	 * @var array
	 */
	protected static $names = [];

	/**
	 * Post types arguments
	 *
	 * @var array
	 */
	protected static $post_types = [];

	/**
	 * Taxonomies arguments
	 *
	 * @var array
	 */
	protected static $taxonomies = [];

	/**
	 * Abstract method: Regulate arguments for registration.
	 *
	 * @access public
	 */
	abstract public function regulation();

	/**
	 * Constructor.
	 *
	 * @access protected
	 *
	 * @uses  mimosafa\WP\Repository\Repository::__construct()
	 *
	 * @param  string $name
	 * @param  string $alias
	 * @param  array  $args
	 * @return void
	 */
	protected function __construct( $name, $alias, Array $args, $builtin ) {
		parent::__construct( $name, $alias, $args, $builtin );
		/**
		 * Regulate arguments.
		 */
		add_action( 'init', [ &$this, 'regulation' ], 0 );
		/**
		 * Flag for action at only once.
		 *
		 * @var boolean
		 */
		static $done = false;
		if ( ! $done ) {
			add_action( 'init', [ &$this, 'register_taxonomies' ], 1 );
			add_action( 'init', [ &$this, 'register_post_types' ], 1 );
			$done = true;
		}
	}

	/**
	 * Register taxonomy components.
	 *
	 * @access public
	 */
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

	/**
	 * Register post type components.
	 *
	 * @access public
	 */
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
