<?php
// Smoke test of the plugin inside a real WordPress (wp-env): `wp eval-file wp-content/cvc-tests/smoke.php`.
// Renders the shortcode, the block and the classic widget, checks the checker's address and the opt-in credit link,
// and creates two published posts for the front-end check in the workflow. Exits 1 on the first failure.

$fail = function ( $msg ) {
	fwrite( STDERR, "FAIL: $msg\n" );
	exit( 1 );
};
$has = function ( $html, $needle, $what ) use ( $fail ) {
	if ( false === strpos( $html, $needle ) ) {
		$fail( "$what: expected «" . $needle . '» in ' . $html );
	}
	echo "ok   $what\n";
};
$hasnt = function ( $html, $needle, $what ) use ( $fail ) {
	if ( false !== strpos( $html, $needle ) ) {
		$fail( "$what: did not expect «" . $needle . '» in ' . $html );
	}
	echo "ok   $what\n";
};

if ( ! function_exists( 'chinavisacheck_render' ) ) {
	$fail( 'plugin not active' );
}

$sc = do_shortcode( '[china_visa_check passport="jp" lang="ja" theme="light" credit="yes"]' );
$has( $sc, 'src="https://chinavisacheck.com/ja/china-visa/embed#jp-light"', 'shortcode: language, passport and colours in the address' );
$has( $sc, 'class="chinavisacheck-credit"', 'shortcode: credit link when asked for' );

$plain = do_shortcode( '[china_visa_check]' );
$has( $plain, 'src="https://chinavisacheck.com/china-visa/embed"', 'shortcode: English, no settings' );
$hasnt( $plain, 'chinavisacheck-credit', 'shortcode: no credit link by default' );

$bad = do_shortcode( '[china_visa_check passport="<x>" lang="xx" theme="neon"]' );
$has( $bad, 'src="https://chinavisacheck.com/china-visa/embed"', 'shortcode: unknown values fall back' );

$block = do_blocks( '<!-- wp:chinavisacheck/checker {"passport":"gb","theme":"dark","lang":"de"} /-->' );
$has( $block, 'src="https://chinavisacheck.com/de/china-visa/embed#gb-dark"', 'block: settings in the address' );
$hasnt( $block, 'chinavisacheck-credit', 'block: no credit link by default' );

ob_start();
the_widget( 'ChinaVisaCheck_Widget', array( 'title' => 'Visa?', 'passport' => 'fr', 'lang' => 'fr', 'theme' => 'auto', 'credit' => false ), array( 'before_widget' => '<section>', 'after_widget' => '</section>', 'before_title' => '<h2>', 'after_title' => '</h2>' ) );
$widget = ob_get_clean();
$has( $widget, 'src="https://chinavisacheck.com/fr/china-visa/embed#fr"', 'widget: settings in the address' );
$has( $widget, '<h2>Visa?</h2>', 'widget: title' );

// The site's own language decides when none is chosen
$filter = function () {
	return 'zh_TW';
};
add_filter( 'locale', $filter );
$has( do_shortcode( '[china_visa_check]' ), 'src="https://chinavisacheck.com/tc/china-visa/embed"', 'shortcode: Traditional Chinese site gets the tc checker' );
remove_filter( 'locale', $filter );

// Posts for the front-end check (height script printed in the footer)
foreach ( array( 'cvc-shortcode' => '[china_visa_check passport="jp"]', 'cvc-block' => '<!-- wp:chinavisacheck/checker {"passport":"kr"} /-->' ) as $name => $content ) {
	if ( ! get_page_by_path( $name, OBJECT, 'post' ) ) {
		wp_insert_post( array( 'post_title' => $name, 'post_name' => $name, 'post_content' => $content, 'post_status' => 'publish' ) );
	}
}
echo 'WordPress ' . get_bloginfo( 'version' ) . ", PHP " . PHP_VERSION . ": all checks passed\n";
