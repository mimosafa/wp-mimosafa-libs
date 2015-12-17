<?php
namespace mimosafa\WP\Device\PostType;

abstract class Label {

	const TEXT_DOMAIN = 'wp-mimosafa-libs';

	/**
	 * Label formats
	 *
	 * @var array
	 */
	protected static $formats = [
		'name'                  => null,
		'singular_name'         => null,
		'add_new'               => null,
		'add_new_item'          => 'Add New %s',
		'edit_item'             => 'Edit %s',
		'new_item'              => 'New %s',
		'view_item'             => 'View %s',
		'search_items'          => 'Search %s',
		'not_found'             => 'No %s found.',
		'not_found_in_trash'    => 'No %s found in Trash.',
		'all_items'             => 'All %s',
		'parent_item_colon'     => 'Parent %s:',
		'uploaded_to_this_item' => 'Uploaded to this %s',
		'featured_image'        => null,
		'set_featured_image'    => 'Set %s',
		'remove_featured_image' => 'Remove %s',
		'use_featured_image'    => 'Use as %s',
		'archives'              => '%s Archives',
		'insert_into_item'      => 'Insert into %s',
		'filter_items_list'     => 'Filter %s list',
		'items_list_navigation' => '%s list navigation',
		'items_list'            => '%s list',
	];

	protected static $custom_labels = [
		'enter_title_here' => false,
	];

	public static function generate( $key, $string, $text_domain = null ) {
		if ( filter_var( $key ) && filter_var( $string ) ) {
			if ( isset( static::$formats[$key] ) ) {
				$text_domain = filter_var( $text_domain ) ? $text_domain : self::TEXT_DOMAIN;
				return esc_html( sprintf( __( static::$formats[$key], $text_domain ), $string ) );
			}
			self::expand( $key );
		}
		return false;
	}

	public static function expand( $key ) {
		if ( isset( static::$custom_labels[$key] ) && ! static::$custom_labels[$key] ) {
			switch ( $key ) {
				case 'enter_title_here' :
					add_filter( 'enter_title_here', __CLASS__ . '::enter_title_here', 10, 2 );
					break;
			}
			static::$custom_labels[$key] = true;
		}
	}

	/**
	 * "Enter Title Here"
	 *
	 * @access public
	 *
	 * @see    http://www.warna.info/archives/2929/
	 *
	 * @param  string  $text
	 * @param  WP_Post $post
	 * @return string
	 */
	public static function enter_title_here( $text, \WP_Post $post ) {
		$post_type = get_post_type_object( $post->post_type );
		if ( isset( $post_type->labels->enter_title_here ) && $enter_title_here = filter_var( $post_type->labels->enter_title_here ) ) {
			$text = esc_html( $post_type->labels->enter_title_here );
		}
		return $text;
	}

}
