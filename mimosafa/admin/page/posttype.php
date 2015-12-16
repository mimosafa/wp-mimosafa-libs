<?php
namespace mimosafa\WP\Admin\Page;

class PostType extends Page {

	protected static $map = [];
	protected static $groups = [];

	public static function init( Array $value_objects ) {
		foreach ( $value_objects as $value_object ) {
			$args = $value_object->to_array();
			if ( isset( $args['group'] ) && filter_var( $args['group'] ) ) {
				static::$group[] = $args['group'];
			}
			static::$map[] = $args;
		}
		add_meta_box( 'a', '$title', function() { var_dump( static::$map ); } );
	}

}
