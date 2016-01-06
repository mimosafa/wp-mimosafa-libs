<?php
namespace mimosafa\WP\Model;

class Model {
	/**
	 *
	 */
	protected static $models = [];
	protected function has( $property, $arg = null ) {
		if ( ! property_exists( get_called_class(), $property ) || ! isset( $this->{$property} ) ) {
			return false;
		}
		if ( isset( $arg ) ) {
			switch ( $arg ) {
				case 'string' :
					return (bool) filter_var( $this->{$property} );
			}
			if ( is_callable( $arg ) ) {
				return $arg( $this->{$property} );
			}
		}
		return $this->{$property};
	}
}
