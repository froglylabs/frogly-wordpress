<?php
/**
 * Plugin Name:       Frogly
 * Plugin URI:        https://frogly.ai
 * Description:       Catches automated intruders on your site and leads them away before they reach anything real. Paste your Frogly install line once and you are done.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Frogly
 * Author URI:        https://frogly.ai
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       frogly
 *
 * @package Frogly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FROGLY_VERSION', '1.0.0' );
define( 'FROGLY_OPTION', 'frogly_site_id' );
define( 'FROGLY_WIDGET_URL', 'https://cdn.frogly.ai/w/core.v5.js' );
define( 'FROGLY_WIDGET_SRI', 'sha384-5PrNyFpjR+GGOLhFy+XWlvP9vJtqRDKWrSyGVuWYl907zw26TU0PSCod12iTAUdu' );
define( 'FROGLY_CONSOLE_URL', 'https://frogly.ai/app' );

/**
 * Return the saved site ID, or an empty string if Frogly is not set up yet.
 *
 * @return string
 */
function frogly_get_site_id() {
	$site_id = get_option( FROGLY_OPTION, '' );
	return frogly_is_site_id( $site_id ) ? $site_id : '';
}

/**
 * Check that a value looks like a Frogly site ID (a lowercase UUID).
 *
 * @param string $value Candidate value.
 * @return bool
 */
function frogly_is_site_id( $value ) {
	return is_string( $value ) && 1 === preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $value );
}

/**
 * Pull the site ID out of whatever the operator pasted.
 *
 * Accepts either the bare site ID or the full install line copied from the
 * Frogly console (the ID is the data-cfg attribute).
 *
 * @param string $input Raw pasted text.
 * @return string Site ID, or empty string if none was found.
 */
function frogly_extract_site_id( $input ) {
	$input = strtolower( trim( (string) $input ) );

	if ( frogly_is_site_id( $input ) ) {
		return $input;
	}

	if ( preg_match( '/data-cfg\s*=\s*["\']?([0-9a-f-]{36})["\']?/', $input, $m ) && frogly_is_site_id( $m[1] ) ) {
		return $m[1];
	}

	return '';
}

/**
 * Sanitize the settings field. Stores only the site ID, never the pasted markup.
 *
 * @param string $input Raw submitted value.
 * @return string
 */
function frogly_sanitize_site_id( $input ) {
	$input = (string) $input;

	if ( '' === trim( $input ) ) {
		return '';
	}

	$site_id = frogly_extract_site_id( $input );

	if ( '' === $site_id ) {
		if ( function_exists( 'add_settings_error' ) ) {
			add_settings_error(
				FROGLY_OPTION,
				'frogly_invalid',
				__( 'That does not look like a Frogly install line. Copy the whole line from the Install button in your Frogly console and paste it here.', 'frogly' ),
				'error'
			);
		}
		return frogly_get_site_id();
	}

	return $site_id;
}

/**
 * Register the single setting.
 */
function frogly_register_settings() {
	register_setting(
		'frogly',
		FROGLY_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'frogly_sanitize_site_id',
			'default'           => '',
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'frogly_register_settings' );

/**
 * Add Settings > Frogly.
 */
function frogly_add_settings_page() {
	add_options_page(
		__( 'Frogly', 'frogly' ),
		__( 'Frogly', 'frogly' ),
		'manage_options',
		'frogly',
		'frogly_render_settings_page'
	);
}
add_action( 'admin_menu', 'frogly_add_settings_page' );

/**
 * Settings link on the Plugins screen.
 *
 * @param string[] $links Existing action links.
 * @return string[]
 */
function frogly_action_links( $links ) {
	$settings = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php?page=frogly' ) ),
		esc_html__( 'Settings', 'frogly' )
	);
	array_unshift( $links, $settings );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'frogly_action_links' );

/**
 * Gentle reminder until the install line is pasted.
 */
function frogly_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) || '' !== frogly_get_site_id() ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'plugins' ), true ) ) {
		return;
	}

	printf(
		'<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'Frogly is installed but not on yet. Paste your install line to start watching this site.', 'frogly' ),
		esc_url( admin_url( 'options-general.php?page=frogly' ) ),
		esc_html__( 'Open Frogly settings', 'frogly' )
	);
}
add_action( 'admin_notices', 'frogly_setup_notice' );

/**
 * Render Settings > Frogly.
 */
function frogly_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$site_id = frogly_get_site_id();
	$host    = wp_parse_url( home_url(), PHP_URL_HOST );
	?>
	<div class="wrap frogly-wrap" style="max-width: 640px;">
		<h1><?php esc_html_e( 'Frogly', 'frogly' ); ?></h1>

		<?php if ( '' !== $site_id ) : ?>
			<div class="notice notice-success inline" style="margin: 16px 0;">
				<p>
					<strong>
					<?php
					/* translators: %s: this site's domain */
					printf( esc_html__( 'Frogly is watching %s.', 'frogly' ), esc_html( $host ) );
					?>
					</strong>
					<?php esc_html_e( 'Real visitors see nothing. Automated intruders that take a decoy get led away, and you are told.', 'frogly' ); ?>
					<a href="<?php echo esc_url( FROGLY_CONSOLE_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open your console', 'frogly' ); ?></a>
				</p>
			</div>
		<?php else : ?>
			<p style="font-size: 14px;">
				<?php esc_html_e( 'Three small steps and Frogly is on.', 'frogly' ); ?>
			</p>
			<ol style="font-size: 14px; line-height: 1.8;">
				<li>
					<?php
					printf(
						/* translators: 1: console link, 2: sign up link */
						esc_html__( 'Sign in at %1$s (or %2$s, it is free) and add this site.', 'frogly' ),
						'<a href="' . esc_url( FROGLY_CONSOLE_URL ) . '" target="_blank" rel="noopener">frogly.ai/app</a>',
						'<a href="https://frogly.ai/signup" target="_blank" rel="noopener">' . esc_html__( 'sign up', 'frogly' ) . '</a>'
					);
					?>
				</li>
				<li><?php esc_html_e( 'Click Install and copy the line it shows you.', 'frogly' ); ?></li>
				<li><?php esc_html_e( 'Paste it below and save.', 'frogly' ); ?></li>
			</ol>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'frogly' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="frogly_site_id"><?php esc_html_e( 'Your install line', 'frogly' ); ?></label>
					</th>
					<td>
						<textarea
							id="frogly_site_id"
							name="<?php echo esc_attr( FROGLY_OPTION ); ?>"
							class="large-text code"
							rows="3"
							placeholder="<?php esc_attr_e( 'Paste the whole line from the Install button, or just the site ID', 'frogly' ); ?>"
						><?php echo esc_textarea( $site_id ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Only the site ID is kept. Clear the box and save to turn Frogly off.', 'frogly' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button( '' === $site_id ? __( 'Turn Frogly on', 'frogly' ) : __( 'Save', 'frogly' ) ); ?>
		</form>

		<h2 style="margin-top: 32px;"><?php esc_html_e( 'How it works', 'frogly' ); ?></h2>
		<p style="font-size: 14px; max-width: 560px;">
			<?php esc_html_e( 'Frogly adds one small script to your pages. It places a few decoys that real visitors never see. When an automated intruder takes one, it is led off your site into a maze of dead ends and your console shows you the catch. Frogly never blocks anyone, never slows your pages, and if it ever cannot reach its servers your site simply carries on as normal.', 'frogly' ); ?>
		</p>
		<p style="font-size: 14px;">
			<a href="https://frogly.ai/faq" target="_blank" rel="noopener"><?php esc_html_e( 'Questions and answers', 'frogly' ); ?></a>
			&middot;
			<a href="https://frogly.ai/privacy" target="_blank" rel="noopener"><?php esc_html_e( 'Privacy', 'frogly' ); ?></a>
			&middot;
			<a href="mailto:support@frogly.ai">support@frogly.ai</a>
		</p>
	</div>
	<?php
}

/**
 * Load the Frogly widget on the public site.
 */
function frogly_enqueue_widget() {
	$site_id = frogly_get_site_id();

	if ( '' === $site_id || is_admin() || is_customize_preview() ) {
		return;
	}

	// Version null: no ?ver= query string, the pinned hash does the versioning.
	wp_enqueue_script( 'frogly', FROGLY_WIDGET_URL, array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
}
add_action( 'wp_enqueue_scripts', 'frogly_enqueue_widget' );

/**
 * Add the attributes the widget needs: site ID, pinned hash, CORS mode, async.
 *
 * @param string $tag    The script tag HTML.
 * @param string $handle Script handle.
 * @return string
 */
function frogly_script_tag( $tag, $handle ) {
	if ( 'frogly' !== $handle ) {
		return $tag;
	}

	$site_id = frogly_get_site_id();
	if ( '' === $site_id ) {
		return $tag;
	}

	$attrs = sprintf(
		' data-cfg="%s" integrity="%s" crossorigin="anonymous"',
		esc_attr( $site_id ),
		esc_attr( FROGLY_WIDGET_SRI )
	);

	if ( false === strpos( $tag, ' async' ) ) {
		$attrs .= ' async';
	}

	return preg_replace( '/<script\b/', '<script' . $attrs, $tag, 1 );
}
add_filter( 'script_loader_tag', 'frogly_script_tag', 10, 2 );
