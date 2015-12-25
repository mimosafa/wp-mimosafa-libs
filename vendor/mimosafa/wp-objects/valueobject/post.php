<?php
namespace mimosafa\WP\Object\ValueObject;

abstract class Post extends ValueObject {

	protected static $map = [];
	protected static $_repository_class = '\\mimosafa\\WP\\Object\\Repository\\PostType';

	final public static function list_filter( $repository, Array $args, $operator = 'AND', $field = false ) {
		if ( ! filter_var( $repository ) || ! isset( self::$map[$repository] ) ) {
			return [];
		}
		return wp_filter_object_list( self::$map[$repository], $args, $operator, $field );
	}

}
