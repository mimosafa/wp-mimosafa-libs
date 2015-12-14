<?php
namespace mimosafa\WP\ValueObject\Post;

class Meta {

	protected $name;
	protected $post_type;
	protected $sanitize;

	protected function __construct( $name, $post_type, $sanitize = null ) {
		$this->name = $name;
		$this->post_type = $post_type;
		if ( $sanitize && is_callable( $sanitize ) ) {
			$this->sanitize = $sanitize;
		}
	}

	public static function create( $name, $post_type, $sanitize = null ) {
		if ( filter_var( $post_type ) && filter_var( $name ) && $name === sanitize_key( $name ) ) {
			return new static( $name, $post_type, $sanitize );
		}
	}

}
