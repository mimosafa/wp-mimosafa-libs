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
			if ( strlen( self::$prefix . $name ) > 32 || @preg_match( '/[0-9]/', $name ) ) {
				$name = null;
			}
		}
		return $name;
	}

}
