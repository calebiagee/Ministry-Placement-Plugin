<?php
/**
 * Plugin Name: Ministry Placement Quiz
 * Plugin URI:  https://hopechurch.net
 * Description: A typeform-style spiritual gifts quiz that recommends Hope Church ministries. Use [ministry_quiz] on any page.
 * Version:     1.0.0
 * Author:      Hope Church
 * Text Domain: ministry-placement
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'MPQ_DIR',     plugin_dir_path( __FILE__ ) );
define( 'MPQ_URL',     plugin_dir_url( __FILE__ ) );
define( 'MPQ_VERSION', '1.0.0' );

require_once MPQ_DIR . 'includes/class-quiz-data.php';
require_once MPQ_DIR . 'includes/class-results-calculator.php';
require_once MPQ_DIR . 'includes/class-database.php';
require_once MPQ_DIR . 'includes/class-email.php';
require_once MPQ_DIR . 'includes/class-admin.php';

/* ============================================================================
   Plugin lifecycle
   ========================================================================= */

register_activation_hook( __FILE__, function () {
	MPQ_Database::create_table();
} );

add_action( 'plugins_loaded', function () {
	MPQ_Database::maybe_upgrade();
} );

/* ============================================================================
   Main plugin class
   ========================================================================= */

class Ministry_Placement_Quiz {

	public function __construct() {
		add_action( 'wp_enqueue_scripts',        [ $this, 'enqueue_assets' ] );
		add_shortcode( 'ministry_quiz',          [ $this, 'render_quiz' ] );
		add_action( 'wp_ajax_mpq_submit',        [ $this, 'handle_quiz_submit' ] );
		add_action( 'wp_ajax_nopriv_mpq_submit', [ $this, 'handle_quiz_submit' ] );
		add_action( 'wp_ajax_mpq_confirm',       [ $this, 'handle_confirm' ] );
		add_action( 'wp_ajax_nopriv_mpq_confirm',[ $this, 'handle_confirm' ] );

		new MPQ_Admin();
	}

	/* -------------------------------------------------------------------------
	 * Assets
	 * ---------------------------------------------------------------------- */

	public function enqueue_assets() {
		global $post;
		if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'ministry_quiz' ) ) {
			return;
		}

		wp_enqueue_style(
			'mpq-style',
			MPQ_URL . 'assets/css/quiz-style.css',
			[],
			MPQ_VERSION
		);

		wp_enqueue_script(
			'mpq-quiz',
			MPQ_URL . 'assets/js/quiz.js',
			[],
			MPQ_VERSION,
			true
		);

		$settings = get_option( 'mpq_settings', [] );

		wp_localize_script( 'mpq-quiz', 'mpqData', [
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'mpq_nonce' ),
			'questions'  => MPQ_Quiz_Data::get_all_questions(),
			'siteName'   => esc_js( $settings['site_name'] ?? get_bloginfo( 'name' ) ),
			'connectUrl' => esc_js( $settings['connect_url'] ?? '' ),
		] );
	}

	/* -------------------------------------------------------------------------
	 * Shortcode output
	 * ---------------------------------------------------------------------- */

	public function render_quiz() {
		ob_start();
		?>
		<div id="mpq-app" class="mpq-app" role="main" aria-label="Ministry Placement Quiz">

			<!-- Welcome -->
			<div id="mpq-welcome" class="mpq-screen mpq-screen--active">
				<div class="mpq-screen__inner">
					<p class="mpq-eyebrow">Hope Church</p>
					<h1 class="mpq-welcome__title">Find Your<br>Place to Serve</h1>
					<p class="mpq-welcome__subtitle">Answer a few questions about how God has wired you and we'll help match you with the ministries where you'll thrive.</p>
					<ul class="mpq-welcome__meta">
						<li><span class="mpq-meta-icon">&#9201;</span> About 7 minutes</li>
						<li><span class="mpq-meta-icon">&#128100;</span> No sign-up required</li>
					</ul>
					<button id="mpq-start-btn" class="mpq-btn mpq-btn--primary mpq-btn--lg">
						Let&rsquo;s Get Started <span class="mpq-btn__arrow">&#8594;</span>
					</button>
				</div>
			</div>

			<!-- Quiz -->
			<div id="mpq-quiz" class="mpq-screen" aria-live="polite">
				<div class="mpq-progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
					<div id="mpq-progress-fill" class="mpq-progress-bar__fill"></div>
				</div>
				<div class="mpq-quiz__header">
					<div id="mpq-q-counter" class="mpq-q-counter"></div>
				</div>
				<div class="mpq-quiz__body">
					<div id="mpq-question-wrap" class="mpq-question-wrap"></div>
				</div>
				<div class="mpq-quiz__footer">
					<button id="mpq-back-btn" class="mpq-back-btn" aria-label="Previous question">&#8592; Back</button>
					<button id="mpq-next-btn" class="mpq-btn mpq-btn--primary" disabled>
						Next <span class="mpq-btn__arrow">&#8594;</span>
					</button>
				</div>
			</div>

			<!-- Results -->
			<div id="mpq-results" class="mpq-screen">
				<div class="mpq-results__inner">
					<div id="mpq-results-content"></div>
				</div>
			</div>

			<!-- Confirmation -->
			<div id="mpq-confirmation" class="mpq-screen">
				<div class="mpq-screen__inner mpq-confirmation__inner">
					<div class="mpq-confirmation__icon">&#10003;</div>
					<p class="mpq-eyebrow">You&rsquo;re all set!</p>
					<h2 class="mpq-confirmation__title">Thanks for taking the next step.</h2>
					<p class="mpq-confirmation__body">We&rsquo;ve received your ministry interests and our team will be in touch soon. We can&rsquo;t wait to serve alongside you!</p>
					<div id="mpq-confirmation__cta"></div>
				</div>
			</div>

			<!-- Loading -->
			<div id="mpq-loading" class="mpq-loading mpq-loading--hidden" aria-hidden="true">
				<div class="mpq-loading__spinner"></div>
				<p>Discovering your gifts&hellip;</p>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}

	/* -------------------------------------------------------------------------
	 * AJAX: quiz submission → calculate results
	 * ---------------------------------------------------------------------- */

	public function handle_quiz_submit() {
		check_ajax_referer( 'mpq_nonce', 'nonce' );

		$raw = isset( $_POST['answers'] ) ? (array) $_POST['answers'] : [];

		$answers = [];
		foreach ( $raw as $key => $val ) {
			$k = sanitize_key( $key );
			$answers[ $k ] = is_numeric( $val ) ? intval( $val ) : sanitize_text_field( $val );
		}

		$results    = MPQ_Results_Calculator::calculate( $answers );
		$ms_settings = get_option( 'mpq_ministry_settings', [] );

		// Attach image data to top ministries
		foreach ( $results['top_ministries'] as &$m ) {
			$s             = $ms_settings[ $m['id'] ] ?? [];
			$m['image_url'] = esc_url( $s['image_url'] ?? '' );
			$m['logo_url']  = esc_url( $s['logo_url']  ?? '' );
		}
		unset( $m );

		// Also pass ALL ministry data for display on results (with images)
		$all_ministries   = MPQ_Quiz_Data::get_ministries();
		$ministries_client = [];
		foreach ( $all_ministries as $id => $ministry ) {
			$s = $ms_settings[ $id ] ?? [];
			$ministries_client[ $id ] = [
				'id'          => $id,
				'name'        => $ministry['name'],
				'description' => $ministry['description'],
				'commitment'  => $ministry['commitment'],
				'image_url'   => esc_url( $s['image_url'] ?? '' ),
				'logo_url'    => esc_url( $s['logo_url']  ?? '' ),
			];
		}

		$results['all_ministries_data'] = $ministries_client;

		// Store answers in session for potential confirmation step
		$_SESSION['mpq_last_answers']   = $answers;
		$_SESSION['mpq_last_results']   = $results;

		wp_send_json_success( $results );
	}

	/* -------------------------------------------------------------------------
	 * AJAX: confirm ministry interest → save + email
	 * ---------------------------------------------------------------------- */

	public function handle_confirm() {
		check_ajax_referer( 'mpq_nonce', 'nonce' );

		$name           = sanitize_text_field( $_POST['name']            ?? '' );
		$email          = sanitize_email(      $_POST['email']           ?? '' );
		$phone          = sanitize_text_field( $_POST['phone']           ?? '' );
		$sel_ids        = sanitize_text_field( $_POST['sel_ministries']  ?? '' );
		$rec_ids        = sanitize_text_field( $_POST['rec_ministries']  ?? '' );
		$top_gifts_json = stripslashes(        $_POST['top_gifts']       ?? '[]' );
		$answers_json   = stripslashes(        $_POST['answers_json']    ?? '{}' );

		if ( ! $name || ! is_email( $email ) ) {
			wp_send_json_error( [ 'message' => 'Please enter a valid name and email address.' ] );
		}

		$top_gifts = json_decode( $top_gifts_json, true ) ?: [];
		$answers   = json_decode( $answers_json,   true ) ?: [];

		$submission_id = MPQ_Database::save_submission( [
			'name'           => $name,
			'email'          => $email,
			'phone'          => $phone,
			'top_gifts'      => $top_gifts,
			'rec_ministries' => $rec_ids,
			'sel_ministries' => $sel_ids,
			'answers'        => $answers,
		] );

		MPQ_Email::send_team_notification( [
			'name'           => $name,
			'email'          => $email,
			'phone'          => $phone,
			'top_gifts'      => $top_gifts,
			'rec_ministries' => $rec_ids,
			'sel_ministries' => $sel_ids,
		] );

		wp_send_json_success( [ 'id' => $submission_id ] );
	}
}

new Ministry_Placement_Quiz();
