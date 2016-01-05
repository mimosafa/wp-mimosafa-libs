<?php
namespace mimosafa;

require_once 'classloader.php';
ClassLoader::register( 'mimosafa\WP\Repository', __DIR__ . '/vendor/mimosafa/wp-repository' );
ClassLoader::register( 'mimosafa\WP\Admin',      __DIR__ . '/vendor/mimosafa/wp-admin'      );
