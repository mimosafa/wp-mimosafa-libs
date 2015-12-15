<?php
namespace mimosafa\WP\ValueObject;
use mimosafa\WP\Repository;

abstract class Post extends ValueObject {

	public static function create( $repository, $name, $args = [] ) {
		if ( is_object( $repository ) && $repository instanceof Repository\RepositoryRepository ) {
			return new static( $name, $repository->id, $args );
		}
		if ( is_string( $repository ) && $repository ) {
			if ( $instance = Repository\PostType::create( $repository ) ) {
				$instance->add_value_object( $name, $args );
			}
			else if ( $instance = Repository\PostType::getRepository( $repository ) ) {
				$instance->add_value_object( $name, $args );
			}
		}
	}

}
