<?php
namespace mimosafa\WP\ValueObject;
use mimosafa\WP\Repository;

abstract class Post extends ValueObject {

	protected static $map = [];
	protected static $_repository_class = '\\mimosafa\\WP\\Repository\\PostType';

}
