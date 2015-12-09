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
class PostType extends Rewritable {

	protected static $builtins = [
		'post' => 'post',
		'page' => 'page',
		'attachment' => 'attachment',
	];

	private static $defaults = [
		'label'                => '',
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

	protected static function validateID( $id ) {
		return parent::validateID( $id ) && strlen( $id ) < 21 ? $id : null;
	}

	public function regulate() {
		extract( wp_parse_args( $this->args, static::$defaults ) );
		if ( ! $label || ! is_string( $label ) ) {
			$label = self::labelize( $this->name );
		}
		$public       = filter_var( $public,       \FILTER_VALIDATE_BOOLEAN );
		$hierarchical = filter_var( $hierarchical, \FILTER_VALIDATE_BOOLEAN );
		$has_archive  = filter_var( $has_archive,  \FILTER_VALIDATE_BOOLEAN );
		$can_export   = filter_var( $can_export,   \FILTER_VALIDATE_BOOLEAN );
		if ( isset( $exclude_from_search ) && ! is_bool( $exclude_from_search ) ) {
			$exclude_from_search = ! $public;
		}
		$publicly_queryable = isset( $publicly_queryable ) && ! is_bool( $publicly_queryable ) ? $public : $publicly_queryable;
		$show_ui            = isset( $show_ui )            && ! is_bool( $show_ui )            ? $public : $show_ui;
		$show_in_nav_menus  = isset( $show_in_nav_menus )  && ! is_bool( $show_in_nav_menus )  ? $public : $show_in_nav_menus;

		$this->args = compact( array_keys( self::$defaults ) );
		var_dump( $this->args );
	}

	public function register() {
		if ( ! $this->_builtin ) {
			$args = wp_parse_args( $this->args, static::$defaults );
			$queryable = false;
			if ( isset( $args['publicly_queryable'] ) ) {
				$queryable = filter_var( $args['publicly_queryable'], \FILTER_VALIDATE_BOOLEAN );
			}
			else if ( isset( $args['public'] ) ) {
				$queryable = filter_var( $args['public'], \FILTER_VALIDATE_BOOLEAN );
			}
			if ( $queryable && $this->name !== $this->id ) {
				if ( ! isset( $args['rewrite'] ) || $args['rewrite'] !== false ) {
					if ( ! isset( $args['rewrite'] ) || ! is_array( $args['rewrite'] ) ) {
						$args['rewrite'] = [];
					}
					$args['rewrite']['slug'] = urlencode( $this->name );
				}
			}
			PostType\Labels::init( $this->name, $args );
			Register::post_type( $this->id, $args );
		}
	}

	public function registered() {
		//
	}

}
