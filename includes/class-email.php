<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPQ_Email {

	public static function send_team_notification( array $submission ) {
		$settings   = get_option( 'mpq_settings', [] );
		$team_email = sanitize_email( $settings['team_email'] ?? get_option( 'admin_email' ) );
		$site_name  = esc_html( $settings['site_name'] ?? get_bloginfo( 'name' ) );

		if ( ! is_email( $team_email ) ) return false;

		$name            = esc_html( $submission['name'] );
		$email           = esc_html( $submission['email'] );
		$phone           = esc_html( $submission['phone'] ?? '' );
		$top_gifts       = $submission['top_gifts']       ?? [];
		$rec_ministries  = $submission['rec_ministries']  ?? '';
		$sel_ministries  = $submission['sel_ministries']  ?? '';
		$date            = current_time( 'F j, Y \a\t g:i a' );

		$subject = "{$site_name} — New Ministry Quiz: {$name}";

		$body = self::build_html_email( [
			'site_name'       => $site_name,
			'name'            => $name,
			'email'           => $email,
			'phone'           => $phone,
			'top_gifts'       => $top_gifts,
			'rec_ministries'  => $rec_ministries,
			'sel_ministries'  => $sel_ministries,
			'date'            => $date,
		] );

		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			"From: {$site_name} <{$team_email}>",
			"Reply-To: {$name} <{$email}>",
		];

		return wp_mail( $team_email, $subject, $body, $headers );
	}

	private static function build_html_email( array $d ) {
		$gifts_html = '';
		foreach ( $d['top_gifts'] as $i => $g ) {
			$rank         = [ 'Primary', 'Secondary', 'Third' ][ $i ] ?? '';
			$gifts_html  .= '<tr>
				<td style="padding:6px 12px;font-size:13px;color:#555;border-bottom:1px solid #eee;">' . esc_html( $rank ) . ' Gift</td>
				<td style="padding:6px 12px;font-size:13px;font-weight:600;color:#111;border-bottom:1px solid #eee;">' . esc_html( $g['name'] ) . '</td>
				<td style="padding:6px 12px;font-size:13px;color:#C8A43A;font-weight:700;border-bottom:1px solid #eee;">' . intval( $g['percentage'] ) . '%</td>
			</tr>';
		}

		$rec_list  = self::ministry_list_html( $d['rec_ministries'] );
		$sel_list  = self::ministry_list_html( $d['sel_ministries'] );

		$admin_url = admin_url( 'admin.php?page=ministry-quiz-submissions' );

		return '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Ministry Quiz Submission</title></head>
<body style="margin:0;padding:0;background:#f2f2f2;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f2f2f2;padding:32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">

      <!-- Header -->
      <tr>
        <td style="background:#111;padding:28px 36px;">
          <p style="margin:0;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#C8A43A;">' . esc_html( $d['site_name'] ) . '</p>
          <h1 style="margin:8px 0 0;font-size:22px;font-weight:800;color:#fff;letter-spacing:-0.02em;">New Ministry Quiz Submission</h1>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:32px 36px;">
          <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#aaa;">Submitted</p>
          <p style="margin:0 0 24px;font-size:14px;color:#555;">' . esc_html( $d['date'] ) . '</p>

          <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#aaa;">Name</p>
          <p style="margin:0 0 4px;font-size:18px;font-weight:700;color:#111;">' . esc_html( $d['name'] ) . '</p>
          <p style="margin:0 0 6px;"><a href="mailto:' . esc_attr( $d['email'] ) . '" style="color:#C8A43A;text-decoration:none;font-size:14px;">' . esc_html( $d['email'] ) . '</a></p>
          ' . ( $d['phone'] ? '<p style="margin:0 0 28px;"><a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $d['phone'] ) ) . '" style="color:#C8A43A;text-decoration:none;font-size:14px;">' . esc_html( $d['phone'] ) . '</a></p>' : '<p style="margin:0 0 28px;"></p>' ) . '

          <hr style="border:none;border-top:1px solid #eee;margin:0 0 24px;">

          <p style="margin:0 0 12px;font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#aaa;">Top Spiritual Gifts</p>
          <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eee;border-radius:8px;overflow:hidden;margin-bottom:24px;">
            ' . $gifts_html . '
          </table>

          <p style="margin:0 0 8px;font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#aaa;">Recommended Ministries</p>
          ' . $rec_list . '

          <p style="margin:16px 0 8px;font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#C8A43A;">Ministries They Expressed Interest In</p>
          ' . $sel_list . '

        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#f9f9f9;padding:20px 36px;border-top:1px solid #eee;">
          <p style="margin:0;font-size:12px;color:#aaa;">View all submissions in the <a href="' . esc_url( $admin_url ) . '" style="color:#C8A43A;">WordPress admin</a>.</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>';
	}

	private static function ministry_list_html( $ministry_str ) {
		if ( ! $ministry_str ) return '<p style="font-size:14px;color:#aaa;margin:0 0 8px;">None selected</p>';
		$ids       = explode( ',', $ministry_str );
		$ministries = MPQ_Quiz_Data::get_ministries();
		$items      = '';
		foreach ( $ids as $id ) {
			$id    = trim( $id );
			$name  = isset( $ministries[ $id ] ) ? $ministries[ $id ]['name'] : $id;
			$items .= '<span style="display:inline-block;margin:3px 4px 3px 0;padding:4px 12px;background:#f2f2f2;border-radius:99px;font-size:13px;font-weight:600;color:#111;">' . esc_html( $name ) . '</span>';
		}
		return '<p style="margin:0 0 4px;">' . $items . '</p>';
	}
}
