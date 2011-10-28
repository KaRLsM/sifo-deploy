<?php

/**
  Syntax of the domains:

  $config['domain.com'] = array(
  'devel' => true,
  'instance' => 'default',
  'auth' => 'user,password',	// User and password requested by the browser, comment to disable
  'lang_in_subdomain' => true, // The language is set in the subdomain. E.g: fr.subdomain.com
  'www_as_subdomain' => true, // Are you using "www" as a "default" subdomain or not.
  'language' => 'es', // Language by default, e.g. domain.es is always in Spanish
  'language_domain' =>'messages' // Name of the file containing the translations,
  'static_host' => 'http://static.seoframework.local', // host containing the images and other static content
  'media_host' => 'http://media.seoframework.local', // Avatars and other media not fitting under static. Comment or remove to disable.
  'database' => array(
  'db_driver' => 'mysql', // mysqli also available
  'db_host' => '127.0.0.1',
  'db_user' => 'root',
  'db_password' => 'root',
  'db_name' => 'mydatabase',
  'db_init_commands' => array( 'SET NAMES utf8' ),
  // 'profile' => 'PRODUCTION' // This option overrides all the previous keys and uses the profile set in db_profiles.config.php
  ),
  // REDIS syntax:
  // 'redis' => array(
  //	'database' => array(
  //	'host'     => '127.0.0.1',
  //	'port'     => 6379,
  //	'database' => 0
  ),

  'php_ini_sets' => array(  // Changes in php.ini conf. You'd better make changes in your php.ini and leave this array empty.
  'log_errors' => 'On',
  'error_log' => ROOT_PATH . '/logs/errors.log',
  'short_open_tag' => '1'
  ).
  'libraries_profile' => 'default', // This profile defines the versions of libraries your project will use.
  // By default (if you don't add this variable in your domains.config) will use "default" profile.
  );
  Redirections use the EXACT term in the host, and needs the format
  $config['redirections'] = array( array( 'from' => 'domain.old', 'to' => 'http://domain.new' ), array( 'from' => 'domain2.old', 'to' => 'http://domain.new' ),... );
  FROM: is only the host while TO contains the protocol.

  Use $config['core_inheritance'] for active new versions of core.
  $config['core_inheritance'] = array( 'SEOframework', 'SEOframework5.3' );  // For work with SIFO for php5.3
 */;
$config['instance_type'] = 'instantiable';
$config['core_inheritance'] = array( 'SEOframework' );
// Define the inheritance of this instance (which instances are their parents:
$config['instance_inheritance'] = array( 'common' );

$config['redirections'] = array(
);

$config['deploy.development.vm'] = array(
	'devel' => true,
	'instance' => 'deploy',
	'language' => 'en_US',
	'language_domain' => 'messages',
//	'lang_in_subdomain' => false,
//	'www_as_subdomain' => false,
	'static_host' => 'http://deploy.development.vm',
	'media_host' => 'http://deploy.development.vm', // Alternative static content (media). Comment to disable.
	'database' => array(
		// 'profile' => 'PRODUCTION' // Use this option for MASTER/SLAVE configurations and fill db_profiles.config.php with credentials.
		'db_driver' => 'mysql', // For use transactions you must use mysqli driver.
		'db_host' => '127.0.0.1',
		'db_user' => 'root',
		'db_password' => 'root',
		'db_name' => 'splitweet',
		'db_init_commands' => array( 'SET NAMES utf8' )
	),
	/* REDIS syntax:
	  'database' => array(
	  'database' => array(
	  'host'     => '127.0.0.1',
	  'port'     => 6379,
	  'database' => 0
	  ),
	 */
	'php_ini_sets' => array( // Empty array if don't want changes.
		// Log errors to 'logs' folder:
		'log_errors' => 'On',
		'error_log' => ROOT_PATH . '/logs/errors.log',
		// Allow short tags <? (instead of <?php) for more flexible view scripts.
		'short_open_tag' => '1'
	),
		//'libraries_profile' => 'bleeding_edge'
);

$config['deploy.production.com'] = $config['deploy.development.vm'];
$config['deploy.production.com']['devel'] = false;
$config['deploy.production.com']['static_host'] = 'http://deploy.production.com';
$config['deploy.production.com']['media_host'] = 'http://deploy.production.com';
$config['deploy.production.com']['auth'] = 'user,password';

$config['unit.test'] = $config['deploy.development.vm'];
$config['unit.test']['instance'] = 'utilities';
