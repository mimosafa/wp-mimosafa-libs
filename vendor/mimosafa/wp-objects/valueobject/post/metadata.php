<?php
namespace mimosafa\WP\Object\ValueObject\Post;
use mimosafa\WP\Object\ValueObject;

class Metadata extends ValueObject\Post {

	protected static $meta_type = 'post';

	protected static $defaults = [
		'labels'            => [],
		'description'       => '',
		'public'            => true,
		'show_ui'           => null,
		'show_admin_column' => false,
		'multiple'          => false,
		'sanitize'          => null,
	];

	public function regulate() {
		if ( ! post_type_exists( $this->repository_id ) ) {
			return;
		}
		/**
		 * @var array $labels
		 * @var boolean $public
		 * @var boolean $show_ui
		 * @var boolean $show_admin_column
		 */
		extract( $this->args );

		$public   = filter_var( $public,   \FILTER_VALIDATE_BOOLEAN );
		$multiple = filter_var( $multiple, \FILTER_VALIDATE_BOOLEAN );
		if ( isset( $show_ui ) ) {
			$show_ui = filter_var( $show_ui, \FILTER_VALIDATE_BOOLEAN );
		} else {
			$show_ui = $public;
		}
		$show_admin_column = filter_var( $show_admin_column, \FILTER_VALIDATE_BOOLEAN ); 

		if ( is_array( $description ) || is_object( $description ) ) {
			$description = '';
		}
		if ( isset( $sanitize ) && ! is_callable( $sanitize ) ) {
			$sanitize = null;
		}

		if ( ! is_array( $labels ) ) {
			$labels = [];
		}
		if ( ! isset( $labels['name'] ) || ! filter_var( $labels['name'] ) ) {
			$labels['name'] = isset( $label ) && filter_var( $label ) ? $label : self::labelize( $this->name );
		}
		if ( ! isset( $labels['singular_name'] ) || ! filter_var( $labels['singular_name'] ) ) {
			$labels['singular_name'] = $labels['name'];
		}

		/**
		 * Compact the regulated arguments.
		 */
		$this->args = compact( array_keys( $this->args ) );
	}

}
