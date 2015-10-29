<?php
namespace mimosafa\WP\Repository;
/**
 * Post Type Definition Class
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
class PostType extends Repository {

	/**
	 * Object Instances (Singleton Pattern)
	 *
	 * @var array
	 */
	protected static $instances = [];

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
	 * Register Post Type
	 *
	 * @access public
	 */
	public function register() {
		Register::post_type( $this->name, $this->args );
	}

	/**
	 * Extended: Validate Repository Name
	 *
	 * @access protected
	 *
	 * @param  string $var
	 * @return string|null
	 */
	protected static function validateName( $name ) {
		return ( $name = parent::validateName( $name ) ) && strlen( $name ) < 21 ? $name : null;
	}

}
