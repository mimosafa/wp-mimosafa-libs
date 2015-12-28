<?php
namespace mimosafa\WP\Component;

/**
 * Post type component builder.
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 *
 * @uses mimosafa\WP\Component\RewritableComponent
 */
class PostType extends RewritableComponent {

	/**
	 * WordPress built-in post types
	 *
	 * @var array
	 */
	protected static $builtins = [
		'post'       => 'post',
		'page'       => 'page',
		'attachment' => 'attachment',
	];

	/**
	 * Post type default arguments.
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
		'capability_type'      => null,
		'capabilities'         => [],
		'map_meta_cap'         => null,
		'supports'             => [ 'title', 'editor' ],
		'register_meta_box_cb' => null,
		'taxonomies'           => [],
		'has_archive'          => false,
		'rewrite'              => true,
		'query_var'            => true,
		'can_export'           => true,
		'delete_with_user'     => null
	];

	/**
	 * Rewrites default arguments.
	 *
	 * @var array
	 */
	private static $rewrite_defaults = [
		'slug'       => '',
		'with_front' => true,
		'pages'      => true,
		'feeds'      => null,
		'ep_mask'    => null
	];

	/**
	 * Validate $id string for post type.
	 *
	 * @access protected
	 *
	 * @param  string $id
	 * @return string|null
	 */
	protected static function validateID( $id ) {
		/**
		 * Post type name regulation.
		 *
		 * @see http://codex.wordpress.org/Function_Reference/register_post_type#Parameters
		 */
		return parent::validateID( $id ) && strlen( $id ) < 21 ? $id : null;
	}

	/**
	 * Regulate arguments for registration.
	 *
	 * @access public
	 */
	public function regulation() {
		if ( post_type_exists( $this->id ) ) {
			return;
		}
		//
	}

}
