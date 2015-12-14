<?php
namespace mimosafa\WP\Admin\Page;

class PostType extends Page {

	protected static $instances = [];

	protected $post_type;

	protected function __construct( $post_type ) {
		if ( filter_var( $post_type ) && post_type_exists( $post_type ) ) {
			$this->post_type = $post_type;
		}
	}

	public static function load( $post_type ) {
		if ( $instance = static::getInstance( $post_type ) ) {
			$instance->load_load();
		}
	}

}
