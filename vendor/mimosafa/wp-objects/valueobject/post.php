<?php
namespace mimosafa\WP\Object\ValueObject;

abstract class Post extends ValueObject {

	protected static $map = [];
	protected static $_repository_class = '\\mimosafa\\WP\\Object\\Repository\\PostType';

}
