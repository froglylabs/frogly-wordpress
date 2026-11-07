<?php
/**
 * Runs when the plugin is deleted from the Plugins screen.
 * Removes the only thing Frogly stores: the site ID.
 *
 * @package Frogly
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'frogly_site_id' );
