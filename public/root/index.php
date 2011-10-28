<?php
/**
 * Point of entry for this instance. Apache points to this file.
 *
 * All PHP requests should be directed to this file via Apache using mod_rewrite or mod_alias or .htaccess (not really efficient).
 */

// Define the root path:
define( 'ROOT_PATH', realpath( __DIR__ . '/../../../..' ) );

require ROOT_PATH . '/instances/Bootstrap.php';

// Execute your instance:
\SeoFramework\Bootstrap::execute( 'deploy' );