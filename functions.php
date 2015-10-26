<?php
/**
 * Wrapper Functions
 *
 * @package WordPress
 * @subpackage WordPress Libraries by mimosafa
 *
 * @license GPLv2
 *
 * @author Toshimichi Mimoto <mimosafa@gmail.com>
 */
if ( ! function_exists( 'mimosafa_settings_page_instance' ) ) {
	/**
	 * Get Settings Page Instance
	 *
	 * @access public
	 *
	 * @param  mimosafa\WP\Settings\Options
	 * @param  array $array
	 * @return mimosafa\WP\Settings\Page
	 */
	function mimosafa_settings_page_instance( $option = null, $array = null ) {
		if ( class_exists( 'mimosafa\\WP\\Settings\\Page' ) ) {
			return new mimosafa\WP\Settings\Page( $option, $array );
		}
	}
}
