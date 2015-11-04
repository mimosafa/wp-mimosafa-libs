<?php

class RepositoryRegisterTest extends WP_UnitTestCase {

	function test_repository_register() {
		mimosafa\WP\Repository\Register::post_type( 'aaa' );
		$args = [ 'post_type' => 'aaa' ];
		$id = $this->factory->post->create( $args );
		$post = get_post( $id );
		$this->assertEquals( $post->post_type, 'aaa' );
	}

}
