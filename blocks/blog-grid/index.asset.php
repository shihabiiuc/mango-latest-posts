<?php
/**
 * Editor script dependencies (the script is plain JavaScript, no build step).
 *
 * @package ShihabiiucPostGrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'dependencies' => array(
		'wp-block-editor',
		'wp-blocks',
		'wp-components',
		'wp-core-data',
		'wp-data',
		'wp-element',
		'wp-i18n',
		'wp-server-side-render',
	),
	'version'      => SHIHABIIUC_PG_VERSION,
);
