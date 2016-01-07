<?php
namespace mimosafa;

require_once 'classloader.php';
ClassLoader::register( 'mimosafa\WP\Http',       __DIR__ . '/vendor/mimosafa/wp-http'       );
ClassLoader::register( 'mimosafa\WP\Repository', __DIR__ . '/vendor/mimosafa/wp-repository' );
ClassLoader::register( 'mimosafa\WP\Model',      __DIR__ . '/vendor/mimosafa/wp-model'      );
ClassLoader::register( 'mimosafa\WP\UI',         __DIR__ . '/vendor/mimosafa/wp-ui'         );
ClassLoader::register( 'mimosafa\WP',            __DIR__ . '/vendor/mimosafa'               );

WP\Http\Request::init();
