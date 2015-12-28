<?php
namespace mimosafa;

require_once 'compat.php';
require_once 'functions.php';
require_once 'classloader.php';
ClassLoader::register( 'mimosafa\WP\Component', __DIR__ . '/vendor/mimosafa/wp-components' );
ClassLoader::register( 'mimosafa\WP\Object', __DIR__ . '/vendor/mimosafa/wp-objects' );
ClassLoader::register( 'mimosafa\WP', __DIR__ . '/vendor/mimosafa/wp-libraries' );
