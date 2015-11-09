<?php
namespace mimosafa\WP\Repository\Taxonomy;
use mimosafa\WP\Repository\Repository as Repository;
/**
 * Taxonomy Regulation & Extension
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
		'labels'             => [],
		'description'        => '',
		'public'             => true,
		'hierarchical'       => false,
		'show_ui'            => null,
		'show_in_menu'       => null,
		'show_in_nav_menus'  => null,
		'show_tagcloud'      => null,
		'show_in_quick_edit' => null,
		'show_admin_column'  => false,
		'meta_box_cb'        => null,
		'capabilities'       => [],
		'rewrite'            => true,
		'query_var'          => ''
	];

	/**
	 * Prototypes for Taxonomy
	 *
	 * @var array
	 */
	protected static $prototypes = [
		/**
		 * Category
		 */
		'category' => [
			'hierarchical' => true,
			'public'       => true,
			'show_ui'      => true,
			'show_admin_column' => true
		],

		/**
		 * Post Tag
		 */
		'post_tag' => [
		 	'hierarchical' => false,
			'public'       => true,
			'show_ui'      => true,
			'show_admin_column' => true
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
		if ( $name = parent::validateName( $name ) ) {
			/**
			 * Taxonomy Name Regulation
			 *
			 * @see http://codex.wordpress.org/Function_Reference/register_taxonomy#Parameters
			 */
			if ( strlen( self::$prefix . $name ) > 32 || @preg_match( '/[0-9]\-/', $name ) ) {
				$name = null;
			}
		}
		return $name;
	}

	/**
	 * Regulate Arguments for Registration
	 *
	 * @access public
	 *
	 * @param  string &$name # Taxonomy Name
	 * @param  array  &$args # Registration Arguments for Taxonomy
	 */
	public static function regulation( &$name, Array &$args ) {
		$_name = $name;
		parent::regulation( $name, $args );
		if ( self::$prefix ) {
			if ( strpos( $name, '-' ) ) {
				/**
				 * Taxonomy Name Regulation
				 *
				 * @see http://codex.wordpress.org/Function_Reference/register_taxonomy#Parameters
				 */
				$name = str_replace( '-', '_', $name );
			}
			if ( isset( $args['public'] ) && $args['public'] ) {
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
