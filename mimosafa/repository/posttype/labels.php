<?php
namespace mimosafa\WP\Repository\PostType;
use mimosafa\WP\Repository\Repository as Repository;
/**
 * Post Type Labels Generator & Extension
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
class Labels extends Repository\Labels {

	/**
	 * Common Labels
	 *
	 * @var array
	 */
	protected static $defaults = [
		'name'               => [ 'plural', [ '%s', 'post type general name' ] ],
		'singular_name'      => [ 'singular', [ '%s', 'post type singular name' ] ],
		# 'add_new'            => 'Add New',
		'add_new_item'       => [ 'singular', 'Add New %s' ],
		'edit_item'          => [ 'singular', 'Edit %s' ],
		'new_item'           => [ 'singular', 'New %s' ],
		'view_item'          => [ 'singular', 'View %s' ],
		'search_items'       => [ 'plural', 'Search %s' ],
		'not_found'          => [ 'plural', 'No %s found.' ],
		'not_found_in_trash' => [ 'plural', 'No %s found in Trash.' ],
		'all_items'          => [ 'plural', 'All %s' ],
	];

	/**
	 * Hierarchical Post Type Label
	 *
	 * @var array
	 */
	protected static $hier = [
		'parent_item_colon' => [ 'singular', 'Parent %s:' ]
	];

	/**
	 * Featured Image Labels
	 *
	 * @var array
	 */
	protected static $featured_image = [
		# 'featured_image'        => '%s',
		'set_featured_image'    => 'Set %s',
		'remove_featured_image' => 'Remove %s',
		'use_featured_image'    => 'Use as %s'
	];

	/**
	 * Initialize Post Type Registration Arguments for Labels
	 *
	 * @access public
	 *
	 * @param  string $post_type
	 * @param  array  &$args     # Registration Arguments for Post Type
	 */
	public static function init( $post_type, Array &$args ) {
		if ( ! isset( $args['labels'] ) ) {
			$args['labels'] = [];
		}
		if ( ! isset( $args['labels']['name'] ) || ! filter_var( $args['labels']['name'] ) ) {
			if ( ! isset( $args['label'] ) ) {
				$args['label'] = self::labelize( $post_type );
			}
			$args['labels']['name'] = $args['label'];
		}
		$plural = $args['labels']['name'];
		if ( ! isset( $args['labels']['singular_name'] ) || ! filter_var( $args['labels']['singular_name'] ) ) {
			$args['labels']['singular_name'] = $plural;
		}
		$singular = $args['labels']['singular_name'];
		$defaults = self::$defaults;
		if ( isset( $args['hierarchical'] ) && $args['hierarchical'] ) {
			$defaults = $defaults + self::$hier;
		}
		$featured_image = '';
		if ( isset( $args['supports'] ) && in_array( 'thumbnail', (array) $args['supports'], true ) ) {
			if ( isset( $args['labels']['featured_image'] ) && filter_var( $args['labels']['featured_image'] ) ) {
				$defaults = $defaults + self::$featured_image;
				$featured_image = $args['labels']['featured_image'];
			}
		}
		$args['labels'] = array_merge(
			self::generate_labels( $defaults, $plural, $singular, $featured_image ),
			$args['labels']
		);
		/**
		 * Extentions
		 */
		$self = self::getInstance();
		$self->expand_labels( $args['labels'] );
	}

	/**
	 * Generate Custom Labels
	 *
	 * @access protected
	 *
	 * @param  array  $formats
	 * @param  string $plural
	 * @param  string $singular
	 * @param  string $featured_image
	 * @return array
	 */
	protected static function generate_labels( Array $formats, $plural, $singular, $featured_image = '' ) {
		$labels = [];
		foreach ( $formats as $key => $f ) {
			if ( is_array( $f ) ) {
				$string = $f[0] === 'singular' ? $singular : $plural;
				if ( is_array( $f[1] ) ) {
					$format = _x( $f[1][0], $f[1][1], 'wp-mimosafa-libs' );
				} else {
					$format = __( $f[1], 'wp-mimosafa-libs' );
				}
				$labels[$key] = sprintf( $format, $string );
			}
			else if ( $featured_image ) {
				$labels[$key] = sprintf( __( $f, 'wp-mimosafa-libs' ), $featured_image );
			}
		}
		return $labels;
	}

	/**
	 * Post Type Labels Extensions
	 *
	 * @access private
	 */
	protected function expand_labels( Array $labels ) {
		/**
		 * "Enter Title Here"
		 */
		static $enter_title_here;
		if ( ! $enter_title_here && isset( $labels['enter_title_here'] ) ) {
			add_filter( 'enter_title_here', [ $this, 'enter_title_here' ], 10, 2 );
		}
	}

	/**
	 * Customize "Enter Title Here"
	 *
	 * @access public
	 *
	 * @see    http://www.warna.info/archives/2929/
	 *
	 * @param  string  $text
	 * @param  WP_Post $post
	 * @return string
	 */
	public function enter_title_here( $text, \WP_Post $post ) {
		$post_type = get_post_type_object( $post->post_type );
		if ( isset( $post_type->labels->enter_title_here ) && $enter_title_here = filter_var( $post_type->labels->enter_title_here ) ) {
			$text = esc_html( $post_type->labels->enter_title_here );
		}
		return $text;
	}

}
