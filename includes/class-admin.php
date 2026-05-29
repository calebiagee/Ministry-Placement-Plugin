<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPQ_Admin {

	public function __construct() {
		add_action( 'admin_menu',            [ $this, 'register_menus' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_init',            [ $this, 'handle_settings_save' ] );
		add_action( 'admin_post_mpq_delete_submission', [ $this, 'handle_delete' ] );
	}

	/* -------------------------------------------------------------------------
	 * Menus
	 * ---------------------------------------------------------------------- */

	public function register_menus() {
		add_menu_page(
			'Ministry Quiz',
			'Ministry Quiz',
			'manage_options',
			'ministry-quiz',
			[ $this, 'page_submissions' ],
			'dashicons-groups',
			30
		);

		add_submenu_page( 'ministry-quiz', 'Submissions',     'Submissions',     'manage_options', 'ministry-quiz',             [ $this, 'page_submissions' ] );
		add_submenu_page( 'ministry-quiz', 'Ministry Images', 'Ministry Images', 'manage_options', 'ministry-quiz-images',      [ $this, 'page_ministry_images' ] );
		add_submenu_page( 'ministry-quiz', 'Settings',        'Settings',        'manage_options', 'ministry-quiz-settings',    [ $this, 'page_settings' ] );
		add_submenu_page( 'ministry-quiz', 'Shortcode',       'Shortcode / Help','manage_options', 'ministry-quiz-help',        [ $this, 'page_help' ] );
	}

	/* -------------------------------------------------------------------------
	 * Assets
	 * ---------------------------------------------------------------------- */

	public function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'ministry-quiz' ) === false ) return;

		wp_enqueue_media();
		wp_enqueue_style(  'mpq-admin', MPQ_URL . 'assets/css/admin-style.css', [], MPQ_VERSION );
		wp_enqueue_script( 'mpq-admin', MPQ_URL . 'assets/js/admin.js', [ 'jquery' ], MPQ_VERSION, true );
		wp_localize_script( 'mpq-admin', 'mpqAdmin', [
			'mediaTitle'  => 'Select Ministry Image',
			'mediaButton' => 'Use this image',
		] );
	}

	/* -------------------------------------------------------------------------
	 * Submissions page
	 * ---------------------------------------------------------------------- */

	public function page_submissions() {
		$submissions = MPQ_Database::get_submissions( [ 'limit' => 100 ] );
		$total       = MPQ_Database::count_submissions();
		?>
		<div class="wrap mpq-admin">
			<h1>Ministry Quiz — Submissions <span class="mpq-count"><?php echo $total; ?></span></h1>

			<?php if ( empty( $submissions ) ) : ?>
				<div class="mpq-empty">
					<p>No submissions yet. Add <code>[ministry_quiz]</code> to a page and share it with your congregation!</p>
				</div>
			<?php else : ?>
			<table class="wp-list-table widefat fixed striped mpq-table">
				<thead>
					<tr>
						<th width="30">#</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Top Gifts</th>
						<th>Recommended</th>
						<th>Interested In</th>
						<th>Date</th>
						<th width="80">Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $submissions as $row ) :
					$gifts     = json_decode( $row->top_gifts, true ) ?: [];
					$gift_names = array_map( function( $g ) { return $g['name']; }, $gifts );
					$rec_ids    = $row->rec_ministries ? explode( ',', $row->rec_ministries ) : [];
					$sel_ids    = $row->sel_ministries ? explode( ',', $row->sel_ministries ) : [];
					$all_m      = MPQ_Quiz_Data::get_ministries();
					$rec_names  = array_map( function( $id ) use ( $all_m ) { return isset( $all_m[ $id ] ) ? $all_m[ $id ]['name'] : $id; }, $rec_ids );
					$sel_names  = array_map( function( $id ) use ( $all_m ) { return isset( $all_m[ $id ] ) ? $all_m[ $id ]['name'] : $id; }, $sel_ids );
					$delete_url = wp_nonce_url( admin_url( 'admin-post.php?action=mpq_delete_submission&id=' . $row->id ), 'mpq_delete_' . $row->id );
				?>
					<tr>
						<td><?php echo intval( $row->id ); ?></td>
						<td><strong><?php echo esc_html( $row->name ?: '—' ); ?></strong></td>
						<td><a href="mailto:<?php echo esc_attr( $row->email ); ?>"><?php echo esc_html( $row->email ?: '—' ); ?></a></td>
						<td><?php echo $row->phone ? '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $row->phone ) ) . '">' . esc_html( $row->phone ) . '</a>' : '—'; ?></td>
						<td><?php echo esc_html( implode( ', ', $gift_names ) ?: '—' ); ?></td>
						<td><?php echo esc_html( implode( ', ', $rec_names ) ?: '—' ); ?></td>
						<td class="mpq-interested"><?php echo $sel_names ? esc_html( implode( ', ', $sel_names ) ) : '<span class="mpq-none">None selected</span>'; ?></td>
						<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $row->submitted_at ) ) ); ?></td>
						<td>
							<a href="<?php echo esc_url( $delete_url ); ?>" class="mpq-delete-link" onclick="return confirm('Delete this submission?');">Delete</a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/* -------------------------------------------------------------------------
	 * Ministry Images page
	 * ---------------------------------------------------------------------- */

	public function page_ministry_images() {
		$settings   = get_option( 'mpq_ministry_settings', [] );
		$ministries = MPQ_Quiz_Data::get_ministries();
		$saved      = false;

		if ( isset( $_POST['mpq_save_images'] ) && check_admin_referer( 'mpq_save_images' ) ) {
			$new_settings = [];
			foreach ( $ministries as $id => $m ) {
				$new_settings[ $id ] = [
					'image_id'   => intval( $_POST['image_id'][ $id ]   ?? 0 ),
					'image_url'  => esc_url_raw( $_POST['image_url'][ $id ]  ?? '' ),
					'logo_id'    => intval( $_POST['logo_id'][ $id ]    ?? 0 ),
					'logo_url'   => esc_url_raw( $_POST['logo_url'][ $id ]   ?? '' ),
				];
			}
			update_option( 'mpq_ministry_settings', $new_settings );
			$settings = $new_settings;
			$saved    = true;
		}
		?>
		<div class="wrap mpq-admin">
			<h1>Ministry Images & Logos</h1>
			<p>Upload a banner image and/or logo for each ministry. These appear on the quiz results page.</p>

			<?php if ( $saved ) : ?>
				<div class="notice notice-success is-dismissible"><p>Images saved.</p></div>
			<?php endif; ?>

			<form method="post" action="">
				<?php wp_nonce_field( 'mpq_save_images' ); ?>
				<input type="hidden" name="mpq_save_images" value="1">

				<div class="mpq-ministry-grid">
				<?php foreach ( $ministries as $id => $m ) :
					$ms        = $settings[ $id ] ?? [];
					$image_id  = intval( $ms['image_id']  ?? 0 );
					$image_url = esc_url( $ms['image_url']  ?? '' );
					$logo_id   = intval( $ms['logo_id']   ?? 0 );
					$logo_url  = esc_url( $ms['logo_url']   ?? '' );
				?>
					<div class="mpq-ministry-row">
						<div class="mpq-ministry-row__title">
							<strong><?php echo esc_html( $m['name'] ); ?></strong>
							<span class="mpq-ministry-row__id"><?php echo esc_html( $id ); ?></span>
						</div>

						<div class="mpq-media-fields">
							<!-- Banner image -->
							<div class="mpq-media-field">
								<label>Banner Image</label>
								<div class="mpq-media-preview <?php echo $image_url ? 'has-image' : ''; ?>" id="preview-image-<?php echo esc_attr( $id ); ?>">
									<?php if ( $image_url ) : ?>
										<img src="<?php echo $image_url; ?>" alt="">
									<?php endif; ?>
								</div>
								<input type="hidden" name="image_id[<?php echo esc_attr( $id ); ?>]" id="image_id_<?php echo esc_attr( $id ); ?>" value="<?php echo $image_id; ?>">
								<input type="hidden" name="image_url[<?php echo esc_attr( $id ); ?>]" id="image_url_<?php echo esc_attr( $id ); ?>" value="<?php echo $image_url; ?>">
								<button type="button" class="button mpq-media-btn" data-target-id="image_id_<?php echo esc_attr( $id ); ?>" data-target-url="image_url_<?php echo esc_attr( $id ); ?>" data-preview="preview-image-<?php echo esc_attr( $id ); ?>">
									<?php echo $image_url ? 'Change Image' : 'Upload Image'; ?>
								</button>
								<?php if ( $image_url ) : ?>
									<button type="button" class="button mpq-media-clear" data-target-id="image_id_<?php echo esc_attr( $id ); ?>" data-target-url="image_url_<?php echo esc_attr( $id ); ?>" data-preview="preview-image-<?php echo esc_attr( $id ); ?>">Remove</button>
								<?php endif; ?>
							</div>

							<!-- Logo -->
							<div class="mpq-media-field">
								<label>Logo <span class="mpq-optional">(optional)</span></label>
								<div class="mpq-media-preview mpq-media-preview--logo <?php echo $logo_url ? 'has-image' : ''; ?>" id="preview-logo-<?php echo esc_attr( $id ); ?>">
									<?php if ( $logo_url ) : ?>
										<img src="<?php echo $logo_url; ?>" alt="">
									<?php endif; ?>
								</div>
								<input type="hidden" name="logo_id[<?php echo esc_attr( $id ); ?>]" id="logo_id_<?php echo esc_attr( $id ); ?>" value="<?php echo $logo_id; ?>">
								<input type="hidden" name="logo_url[<?php echo esc_attr( $id ); ?>]" id="logo_url_<?php echo esc_attr( $id ); ?>" value="<?php echo $logo_url; ?>">
								<button type="button" class="button mpq-media-btn" data-target-id="logo_id_<?php echo esc_attr( $id ); ?>" data-target-url="logo_url_<?php echo esc_attr( $id ); ?>" data-preview="preview-logo-<?php echo esc_attr( $id ); ?>">
									<?php echo $logo_url ? 'Change Logo' : 'Upload Logo'; ?>
								</button>
								<?php if ( $logo_url ) : ?>
									<button type="button" class="button mpq-media-clear" data-target-id="logo_id_<?php echo esc_attr( $id ); ?>" data-target-url="logo_url_<?php echo esc_attr( $id ); ?>" data-preview="preview-logo-<?php echo esc_attr( $id ); ?>">Remove</button>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
				</div>

				<p class="submit"><input type="submit" class="button button-primary" value="Save Images"></p>
			</form>
		</div>
		<?php
	}

	/* -------------------------------------------------------------------------
	 * Settings page
	 * ---------------------------------------------------------------------- */

	public function handle_settings_save() {
		if ( ! isset( $_POST['mpq_save_settings'] ) ) return;
		if ( ! check_admin_referer( 'mpq_settings' ) ) return;
		if ( ! current_user_can( 'manage_options' ) ) return;

		update_option( 'mpq_settings', [
			'team_email' => sanitize_email( $_POST['team_email'] ?? '' ),
			'site_name'  => sanitize_text_field( $_POST['site_name']  ?? '' ),
			'connect_url'=> esc_url_raw( $_POST['connect_url'] ?? '' ),
		] );
	}

	public function page_settings() {
		$settings    = get_option( 'mpq_settings', [] );
		$team_email  = esc_attr( $settings['team_email']  ?? get_option( 'admin_email' ) );
		$site_name   = esc_attr( $settings['site_name']   ?? get_bloginfo( 'name' ) );
		$connect_url = esc_attr( $settings['connect_url'] ?? '' );
		$saved       = isset( $_GET['settings-updated'] );
		?>
		<div class="wrap mpq-admin">
			<h1>Quiz Settings</h1>
			<?php if ( $saved ) : ?><div class="notice notice-success is-dismissible"><p>Settings saved.</p></div><?php endif; ?>
			<form method="post" action="">
				<?php wp_nonce_field( 'mpq_settings' ); ?>
				<input type="hidden" name="mpq_save_settings" value="1">
				<table class="form-table">
					<tr>
						<th scope="row"><label for="team_email">Team Email Address</label></th>
						<td>
							<input type="email" id="team_email" name="team_email" class="regular-text" value="<?php echo $team_email; ?>">
							<p class="description">Quiz results are emailed to this address when someone selects their ministry interests.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="site_name">Church Name</label></th>
						<td>
							<input type="text" id="site_name" name="site_name" class="regular-text" value="<?php echo $site_name; ?>">
							<p class="description">Used in email subject lines and the quiz interface.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="connect_url">Connect / Next Steps URL</label></th>
						<td>
							<input type="url" id="connect_url" name="connect_url" class="regular-text" value="<?php echo $connect_url; ?>" placeholder="https://hopechurch.net/connect">
							<p class="description">Where to send people after they submit — e.g. your connections or volunteer page.</p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button button-primary" value="Save Settings"></p>
			</form>
		</div>
		<?php
	}

	/* -------------------------------------------------------------------------
	 * Help / Shortcode page
	 * ---------------------------------------------------------------------- */

	public function page_help() {
		?>
		<div class="wrap mpq-admin">
			<h1>Using the Ministry Quiz</h1>
			<div class="mpq-help-card">
				<h2>Shortcode</h2>
				<p>Place this shortcode on any page to display the quiz:</p>
				<div class="mpq-code"><code>[ministry_quiz]</code></div>
			</div>
			<div class="mpq-help-card">
				<h2>Recommended Setup</h2>
				<ol>
					<li>Create a new page (e.g. <em>"Find Your Ministry"</em>) and add <code>[ministry_quiz]</code>.</li>
					<li>Set the page template to full-width so the quiz has room to breathe.</li>
					<li>Go to <strong>Ministry Images</strong> to upload a banner photo and optional logo for each ministry.</li>
					<li>Go to <strong>Settings</strong> and enter the team email where submissions should be sent.</li>
					<li>Share the link with your congregation!</li>
				</ol>
			</div>
		</div>
		<?php
	}

	/* -------------------------------------------------------------------------
	 * Delete handler
	 * ---------------------------------------------------------------------- */

	public function handle_delete() {
		$id = intval( $_GET['id'] ?? 0 );
		if ( ! $id || ! current_user_can( 'manage_options' ) ) wp_die( 'Not allowed.' );
		check_admin_referer( 'mpq_delete_' . $id );
		MPQ_Database::delete_submission( $id );
		wp_safe_redirect( admin_url( 'admin.php?page=ministry-quiz' ) );
		exit;
	}
}
