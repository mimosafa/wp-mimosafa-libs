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
	 * Default Arguments
	 *
	 * @var array
	 */
	protected static $defaults = [
		'labels'               => [],
		'description'          => '',
		'public'               => false,
		'hierarchical'         => false,
		'exclude_from_search'  => null,
		'publicly_queryable'   => null,
		'show_ui'              => null,
		'show_in_menu'         => null,
		'show_in_nav_menus'    => null,
		'show_in_admin_bar'    => null,
		'menu_position'        => null,
		'menu_icon'            => null,
		'capability_type'      => 'post',
		'capabilities'         => [],
		'map_meta_cap'         => null,
		'supports'             => [],
		'register_meta_box_cb' => null,
		'taxonomies'           => [],
		'has_archive'          => false,
		'rewrite'              => true,
		'query_var'            => true,
		'can_export'           => true,
		'delete_with_user'     => null
	];

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
	public static function regulation( &$name, Array &$args ) {
		$_name = $name;
		parent::regulation( $name, $args );
		if ( self::$prefix ) {
			$queryable = false;
			if ( isset( $args['publicly_queryable'] ) ) {
				$queryable = filter_var( $args['publicly_queryable'], \FILTER_VALIDATE_BOOLEAN );
			}
			else if ( isset( $args['public'] ) ) {
				$queryable = filter_var( $args['public'], \FILTER_VALIDATE_BOOLEAN );
			}
			if ( $queryable ) {
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
