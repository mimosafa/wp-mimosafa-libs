<?php
namespace mimosafa\WP\Repository;
/**
 * Repository Factory Class
 *
 * @access public
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
class Factory {

	/**
	 * @var string
	 */
	private $prefix = '';

	/**
	 * @var array
	 */
	private static $creatable = [
		'post_type' => 'PostType',
		'taxonomy'  => 'Taxonomy',
		'role'      => 'Role'
	];

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * @param  string $prefix
	 * @return void
	 */
	public function __construct( $prefix = null ) {
		if ( $prefix = self::validatePrefix( $prefix ) ) {
			$this->prefix = $prefix;
		}
	}

	/**
	 * Create Repository
	 *
	 * @access public
	 *
	 * @param  string $what post_type|taxonomy|role
	 * @param  string $name
	 * @return mimosafa\WP\RepositoryInterface|false
	 */
	public function create( $what, $name ) {
		if ( isset( self::$creatable[$what] ) ) {
			$class = __NAMESPACE__ . '\\' . self::$creatable[$what];
			if ( class_exists( $class ) ) {
				return $class::init( $name, $this );
			}
		}
		return false;
	}

	/**
	 * @access public
	 *
	 * @return string
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * @access private
	 *
	 * @param  string $string
	 * @return string|false
	 */
	private static function validatePrefix( $string ) {
		if ( $string = filter_var( $string ) ) {
			if ( $string === sanitize_key( $string ) && strlen( $string ) < 17 ) {
				return $string;
			}
		}
		return false;
	}

}
