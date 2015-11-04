<?php
namespace mimosafa\WP\Repository\PostType;
use mimosafa\WP\Repository\Repository as Repository;
/**
 * Post Type Regulation & Extension
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
class Registry extends Repository\Registry {

	/**
	 * Prototypes for Post Type
	 *
	 * @var array
	 */
	protected static $prototypes = [
		/**
		 * Post
		 */
		'post' => [
			'public'          => true,
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'hierarchical'    => false,
			'supports'        => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'post-formats' ],
		],

		/**
		 * Page
		 */
		'page' => [
			'public'          => true,
			'capability_type' => 'page',
			'map_meta_cap'    => true,
			'hierarchical'    => true,
			'supports'        => [ 'title', 'editor', 'author', 'thumbnail', 'page-attributes', 'custom-fields', 'comments', 'revisions' ],
		]
	];

	/**
	 * Extended: Validate Repository Name
	 *
	 * @access public
	 *
	 * @param  string $var
	 * @return string|null
	 */
	public static function validateName( $name ) {
		/**
		 * Post Type Name Regulation
		 *
		 * @see http://codex.wordpress.org/Function_Reference/register_post_type#Parameters
		 */
		return ( $name = parent::validateName( $name ) ) && strlen( self::$prefix . $name ) < 21 ? $name : null;
	}

	/**
	 * Regulate Arguments for Registration
	 *
	 * @access public
	 *
	 * @param  string &$name # Post Type Name
	 * @param  array  &$args # Registration Arguments for Post Type
	 */
	public static function arguments( &$name, Array &$args ) {
		$_name = $name;
		parent::arguments( $name, $args );
		if ( self::$prefix ) {
			if ( ( isset( $args['publicly_queryable'] ) && $args['publicly_queryable'] )
				|| ( isset( $args['public'] ) && $args['public'] ) )
			{
				/**
				 * Regulate Rewrite Slug
				 */
				if ( ! isset( $args['rewrite'] ) || $args['rewrite'] !== false ) {
					if ( ! isset( $args['rewrite'] ) || ! is_array( $args['rewrite'] ) ) {
						$args['rewrite'] = [];
					}
					$args['rewrite']['slug'] = $_name;
				}
			}
		}
	}

}
