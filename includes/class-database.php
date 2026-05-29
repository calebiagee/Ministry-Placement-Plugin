<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPQ_Database {

	const TABLE = 'mpq_submissions';
	const DB_VERSION = '1.1';
	const VERSION_OPTION = 'mpq_db_version';

	public static function create_table() {
		global $wpdb;
		$table   = $wpdb->prefix . self::TABLE;
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name        VARCHAR(150)        NOT NULL DEFAULT '',
			email       VARCHAR(200)        NOT NULL DEFAULT '',
			phone       VARCHAR(50)         NOT NULL DEFAULT '',
			top_gifts   TEXT                         DEFAULT NULL,
			rec_ministries TEXT                      DEFAULT NULL,
			sel_ministries TEXT                      DEFAULT NULL,
			answers_json LONGTEXT                    DEFAULT NULL,
			submitted_at DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY email (email),
			KEY submitted_at (submitted_at)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( self::VERSION_OPTION, self::DB_VERSION );
	}

	public static function maybe_upgrade() {
		if ( get_option( self::VERSION_OPTION ) !== self::DB_VERSION ) {
			self::create_table();
		}
	}

	public static function save_submission( array $data ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE;

		$inserted = $wpdb->insert(
			$table,
			[
				'name'           => sanitize_text_field( $data['name'] ?? '' ),
				'email'          => sanitize_email( $data['email'] ?? '' ),
				'phone'          => sanitize_text_field( $data['phone'] ?? '' ),
				'top_gifts'      => wp_json_encode( $data['top_gifts'] ?? [] ),
				'rec_ministries' => sanitize_text_field( $data['rec_ministries'] ?? '' ),
				'sel_ministries' => sanitize_text_field( $data['sel_ministries'] ?? '' ),
				'answers_json'   => wp_json_encode( $data['answers'] ?? [] ),
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
		);

		return $inserted ? $wpdb->insert_id : false;
	}

	public static function get_submissions( $args = [] ) {
		global $wpdb;
		$table  = $wpdb->prefix . self::TABLE;
		$limit  = intval( $args['limit']  ?? 50 );
		$offset = intval( $args['offset'] ?? 0 );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, name, email, phone, top_gifts, rec_ministries, sel_ministries, submitted_at
				 FROM {$table}
				 ORDER BY submitted_at DESC
				 LIMIT %d OFFSET %d",
				$limit,
				$offset
			)
		);
	}

	public static function count_submissions() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
	}

	public static function get_submission( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ) );
	}

	public static function delete_submission( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( $table, [ 'id' => $id ], [ '%d' ] );
	}
}
