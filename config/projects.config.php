<?php
/**
 * Projects config file.
 *
 * The atomic key means that you have 2 different git clones with the folders
 * "www" and "www_2_update". These folders will switch between them when
 * "www_2_update" has been git pulled, so the user doesn't get caught with
 * errors or an incomplete update.
 *
 * @package    Deploy
 * @subpackage Config
 * @author     Carlos Soriano <carlos.soriano@musicjumble.com>
 */

$config = array(
	'deploy' => array(
		'deploy' => array(
			'name' => 'Frostpixel Deploy Control',
			'server_path' => '/home/sifo/deploy',
			'atomic' => false
		)
	),
	'sphinx' => array(
		array(
			'name' => 'Sphinx PROD',
			'server_path' => '/home/prod/sphinx',
			'atomic' => false
		)
	),
	'prod' => array(
		array(
			'name' => 'PROD Musicjumble Shared',
			'server_path' => '/home/prod/shared',
			'atomic' => false
		),
		'instance_name1' => array( // If key is specified the memcache of that instance will be flushed.
			'name' => 'PROD Musicjumble ES',
			'server_path' => '/home/prod/es',
			'atomic' => true
		),
		'instance_name2' => array(
			'name' => 'PROD Musicjumble EN',
			'server_path' => '/home/prod/en',
			'atomic' => true
		),
		'instance_name3' => array(
			'name' => 'PROD Backend',
			'server_path' => '/home/prod/backend',
			'atomic' => false
		)
	)
);