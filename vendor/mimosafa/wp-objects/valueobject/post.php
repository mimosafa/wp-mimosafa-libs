<?php
namespace mimosafa\WP\Object\ValueObject;

abstract class Post extends ValueObject {

	protected static $map = [];
	protected static $_repository_class = '\\mimosafa\\WP\\Object\\Repository\\PostType';

	final public static function list_filter( $repository, Array $args, $operator = 'AND' ) {
		if ( ! filter_var( $repository ) || ! isset( self::$map[$repository] ) ) {
			return [];
		}
		return wp_list_filter( self::$map[$repository], $args, $operator );
	}

}
