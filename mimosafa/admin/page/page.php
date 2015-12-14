<?php
namespace mimosafa\WP\Admin\Page;

abstract class Page implements PagePage {

	protected static $instances = [];

	public static function init( $context ) {
		if ( filter_var( $context ) && isset( static::$instances[$context] ) ) {
			/**
			 * If instance is already existing, Do nothing.
			 */
			return null;
		}
		return static::$instances[$context] = new static( $context );
	}

	public static function getInstance( $context ) {
		return isset( static::$instances[$context] ) ? static::$instances[$context] : null;
	}

}
