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
	public static function arguments( &$name, Array &$args ) {
		$_name = $name;
		parent::arguments( $name, $args );
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
