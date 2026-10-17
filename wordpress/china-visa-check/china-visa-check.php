<?php
/**
 * Plugin Name:       China Visa Check
 * Plugin URI:        https://chinavisacheck.com/china-visa/embed-widget
 * Description:       A free China visa checker for your posts, pages and sidebar: visitors pick their passport and see whether they need a visa for mainland China. 194 passports, 10 languages, kept up to date by chinavisacheck.com.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            China Visa Check
 * Author URI:        https://chinavisacheck.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       china-visa-check
 *
 * The checker itself is the page https://chinavisacheck.com/china-visa/embed, shown in an iframe: the rules are checked
 * against official Chinese sources every day and change there, so the plugin never needs an update for a rule change.
 * Three ways to place it: the block "China Visa Check", the shortcode [china_visa_check], and a classic widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CHINAVISACHECK_VERSION', '1.0.0' );
define( 'CHINAVISACHECK_ORIGIN', 'https://chinavisacheck.com' );

/** Languages of the checker, by the code chinavisacheck.com uses in its addresses (English has none). */
function chinavisacheck_languages() {
	return array(
		'en' => 'English',
		'zh' => '简体中文',
		'tc' => '繁體中文',
		'ko' => '한국어',
		'ja' => '日本語',
		'ru' => 'Русский',
		'es' => 'Español',
		'de' => 'Deutsch',
		'fr' => 'Français',
		'ar' => 'العربية',
	);
}

/** The checker's language for this site when none is chosen: from the site's language, English otherwise. */
function chinavisacheck_site_language() {
	$locale = strtolower( str_replace( '-', '_', get_locale() ) );
	if ( in_array( $locale, array( 'zh_tw', 'zh_hk', 'zh_mo' ), true ) ) {
		return 'tc';
	}
	$lang = substr( $locale, 0, 2 );
	return array_key_exists( $lang, chinavisacheck_languages() ) ? $lang : 'en';
}

/**
 * Address of the checker. $passport: two-letter country code shown first (empty = guessed from the visitor's browser);
 * $theme: auto, light or dark.
 */
function chinavisacheck_url( $lang, $passport, $theme ) {
	$lang     = array_key_exists( $lang, chinavisacheck_languages() ) ? $lang : chinavisacheck_site_language();
	$passport = preg_match( '/^[a-z]{2}$/', strtolower( (string) $passport ) ) ? strtolower( $passport ) : '';
	$theme    = in_array( $theme, array( 'light', 'dark' ), true ) ? $theme : '';
	$hash     = implode( '-', array_filter( array( $passport, $theme ) ) );
	return CHINAVISACHECK_ORIGIN . ( 'en' === $lang ? '' : '/' . $lang ) . '/china-visa/embed' . ( $hash ? '#' . $hash : '' );
}

/**
 * The checker's HTML, used by the block, the shortcode and the widget. The iframe reports its height, and a small
 * script (added once per page) sets it, so there is no scroll bar; without scripts it stays 320 px tall.
 * A credit link under it appears only when the site owner asks for it (credit = yes).
 */
function chinavisacheck_render( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'passport' => '',
			'lang'     => '',
			'theme'    => 'auto',
			'credit'   => false,
		)
	);
	$src  = chinavisacheck_url( $args['lang'], $args['passport'], $args['theme'] );
	$html = '<div class="chinavisacheck"><iframe src="' . esc_url( $src ) . '" title="' . esc_attr__( 'China visa checker', 'china-visa-check' ) . '" width="100%" height="320" style="border:0;max-width:520px;display:block" loading="lazy"></iframe>';
	if ( filter_var( $args['credit'], FILTER_VALIDATE_BOOLEAN ) ) {
		$html .= '<p class="chinavisacheck-credit"><a href="' . esc_url( CHINAVISACHECK_ORIGIN . '/china-visa/' ) . '">China Visa Check</a></p>';
	}
	$html .= '</div>';
	wp_enqueue_script( 'chinavisacheck-resize' );
	return $html;
}

/** The height script, printed in the footer of pages that show the checker. */
function chinavisacheck_register_assets() {
	wp_register_script( 'chinavisacheck-resize', false, array(), CHINAVISACHECK_VERSION, true );
	wp_add_inline_script(
		'chinavisacheck-resize',
		'addEventListener("message",function(e){if(e.origin==="' . CHINAVISACHECK_ORIGIN . '"&&e.data&&e.data.cvcH)document.querySelectorAll(".chinavisacheck iframe").forEach(function(f){if(f.contentWindow===e.source)f.style.height=e.data.cvcH+"px"})});'
	);
}
add_action( 'init', 'chinavisacheck_register_assets' );

/** [china_visa_check passport="jp" lang="ja" theme="light" credit="yes"] */
function chinavisacheck_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'passport' => '',
			'lang'     => '',
			'theme'    => 'auto',
			'credit'   => 'no',
		),
		$atts,
		'china_visa_check'
	);
	return chinavisacheck_render( $atts );
}
add_shortcode( 'china_visa_check', 'chinavisacheck_shortcode' );

/** The block: settings in the sidebar, the real checker as the preview. */
function chinavisacheck_register_block() {
	wp_register_script(
		'chinavisacheck-block',
		plugins_url( 'block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ),
		CHINAVISACHECK_VERSION,
		true
	);
	wp_localize_script(
		'chinavisacheck-block',
		'chinavisacheckBlock',
		array( 'languages' => chinavisacheck_languages() )
	);
	register_block_type(
		'chinavisacheck/checker',
		array(
			'editor_script'   => 'chinavisacheck-block',
			'render_callback' => 'chinavisacheck_render',
			'attributes'      => array(
				'passport' => array(
					'type'    => 'string',
					'default' => '',
				),
				'lang'     => array(
					'type'    => 'string',
					'default' => '',
				),
				'theme'    => array(
					'type'    => 'string',
					'default' => 'auto',
				),
				'credit'   => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
		)
	);
}
add_action( 'init', 'chinavisacheck_register_block' );

/** Classic widget, for themes whose sidebars do not take blocks. */
class ChinaVisaCheck_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'chinavisacheck',
			__( 'China Visa Check', 'china-visa-check' ),
			array( 'description' => __( 'Visitors pick their passport and see whether they need a visa for China.', 'china-visa-check' ) )
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme
		}
		echo chinavisacheck_render( $instance ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in chinavisacheck_render
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme
	}

	public function form( $instance ) {
		$instance = wp_parse_args(
			$instance,
			array(
				'title'    => __( 'Do you need a visa for China?', 'china-visa-check' ),
				'passport' => '',
				'lang'     => '',
				'theme'    => 'auto',
				'credit'   => false,
			)
		);
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'china-visa-check' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'lang' ) ); ?>"><?php esc_html_e( 'Language', 'china-visa-check' ); ?></label>
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'lang' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'lang' ) ); ?>">
			<option value=""><?php esc_html_e( 'Same as this site', 'china-visa-check' ); ?></option>
			<?php foreach ( chinavisacheck_languages() as $code => $name ) : ?>
				<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $instance['lang'], $code ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'passport' ) ); ?>"><?php esc_html_e( 'Passport shown first (two-letter code, e.g. JP; empty = the visitor\'s own)', 'china-visa-check' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'passport' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'passport' ) ); ?>" type="text" maxlength="2" value="<?php echo esc_attr( $instance['passport'] ); ?>"></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>"><?php esc_html_e( 'Colours', 'china-visa-check' ); ?></label>
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'theme' ) ); ?>">
			<option value="auto" <?php selected( $instance['theme'], 'auto' ); ?>><?php esc_html_e( 'Follow the visitor\'s device', 'china-visa-check' ); ?></option>
			<option value="light" <?php selected( $instance['theme'], 'light' ); ?>><?php esc_html_e( 'Light', 'china-visa-check' ); ?></option>
			<option value="dark" <?php selected( $instance['theme'], 'dark' ); ?>><?php esc_html_e( 'Dark', 'china-visa-check' ); ?></option>
		</select></p>
		<p><input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'credit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'credit' ) ); ?>" value="1" <?php checked( ! empty( $instance['credit'] ) ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'credit' ) ); ?>"><?php esc_html_e( 'Show a "China Visa Check" link under it', 'china-visa-check' ); ?></label></p>
		<?php
		return '';
	}

	public function update( $new_instance, $old_instance ) {
		$passport = strtolower( sanitize_text_field( isset( $new_instance['passport'] ) ? $new_instance['passport'] : '' ) );
		$lang     = sanitize_key( isset( $new_instance['lang'] ) ? $new_instance['lang'] : '' );
		$theme    = sanitize_key( isset( $new_instance['theme'] ) ? $new_instance['theme'] : 'auto' );
		return array(
			'title'    => sanitize_text_field( isset( $new_instance['title'] ) ? $new_instance['title'] : '' ),
			'passport' => preg_match( '/^[a-z]{2}$/', $passport ) ? $passport : '',
			'lang'     => array_key_exists( $lang, chinavisacheck_languages() ) ? $lang : '',
			'theme'    => in_array( $theme, array( 'light', 'dark' ), true ) ? $theme : 'auto',
			'credit'   => ! empty( $new_instance['credit'] ),
		);
	}
}

function chinavisacheck_register_widget() {
	register_widget( 'ChinaVisaCheck_Widget' );
}
add_action( 'widgets_init', 'chinavisacheck_register_widget' );
