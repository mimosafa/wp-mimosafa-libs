<?php
namespace mimosafa\WP\Entity;
/**
 * Entity Class
 *
 * @access private
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
abstract class Entity implements EntityInterface {

	/**
	 * Object Instances (Singleton Pattern)
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * @var int
	 */
	protected $id;

	//
}
