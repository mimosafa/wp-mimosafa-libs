<?php
use mimosafa\WP\Repository as Repos;

class mimosafaRepositoryTest extends WP_UnitTestCase {

	/**
	 * setUp
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * tearDown
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @group repository_register
	 */
	public function test_repository_register() {
		Repos\Register::post_type( 'aaa' );
		$args = [ 'post_type' => 'aaa' ];
		$id = $this->factory->post->create( $args );
		$post = get_post( $id );
		$this->assertEquals( $post->post_type, 'aaa' );
	}

}
