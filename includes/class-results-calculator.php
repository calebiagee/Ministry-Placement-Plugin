<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPQ_Results_Calculator {

	public static function calculate( array $answers ) {
		$sg_questions = MPQ_Quiz_Data::get_spiritual_gifts_questions();
		$gifts_info   = MPQ_Quiz_Data::get_gifts_info();
		$ministries   = MPQ_Quiz_Data::get_ministries();
		$interest_map = MPQ_Quiz_Data::get_interest_category_map();

		// --- Tally raw scores per gift category ---
		$scores = [];
		$counts = [];

		foreach ( $sg_questions as $q ) {
			$cat = $q['category'];
			if ( ! isset( $scores[ $cat ] ) ) {
				$scores[ $cat ] = 0;
				$counts[ $cat ] = 0;
			}
			$val = isset( $answers[ $q['id'] ] ) ? intval( $answers[ $q['id'] ] ) : 0;
			$val = max( 1, min( 5, $val ) );
			$scores[ $cat ] += $val;
			$counts[ $cat ]++;
		}

		// Normalise to 0–100
		$percentages = [];
		foreach ( $scores as $cat => $total ) {
			$max                = $counts[ $cat ] * 5;
			$percentages[ $cat ] = $max > 0 ? round( ( $total / $max ) * 100 ) : 0;
		}

		arsort( $percentages );
		$top_gifts = array_slice( $percentages, 0, 3, true );

		// Build labelled gift objects for the top 3
		$top_gifts_detail = [];
		foreach ( $top_gifts as $cat => $pct ) {
			$info               = $gifts_info[ $cat ] ?? [ 'name' => $cat, 'description' => '' ];
			$top_gifts_detail[] = [
				'category'    => $cat,
				'name'        => $info['name'],
				'description' => $info['description'],
				'percentage'  => $pct,
			];
		}

		// --- Score each ministry ---
		$ministry_scores = [];

		foreach ( $ministries as $mid => $ministry ) {
			$score = 0;

			// Gift alignment
			foreach ( $ministry['gifts'] as $gift => $weight ) {
				$score += ( ( $percentages[ $gift ] ?? 0 ) / 100 ) * $weight;
			}

			// Interest bonuses — iterate every interest question
			foreach ( $interest_map as $q_id => $int_cat ) {
				if ( ! isset( $ministry[ $int_cat ] ) ) continue;
				$raw = $answers[ $q_id ] ?? null;
				if ( $raw === null ) continue;
				// Handle both single values and arrays (multi-select questions)
				$vals = is_array( $raw ) ? $raw : [ $raw ];
				foreach ( $vals as $v ) {
					if ( isset( $ministry[ $int_cat ][ $v ] ) ) {
						$score += $ministry[ $int_cat ][ $v ];
					}
				}
			}

			$ministry_scores[ $mid ] = $score;
		}

		arsort( $ministry_scores );

		$top_ministry_ids = array_slice( array_keys( $ministry_scores ), 0, 3 );
		$top_ministries   = [];

		foreach ( $top_ministry_ids as $mid ) {
			$m                = $ministries[ $mid ];
			$top_ministries[] = [
				'id'          => $mid,
				'name'        => $m['name'],
				'description' => $m['description'],
				'commitment'  => $m['commitment'],
				'score'       => round( $ministry_scores[ $mid ], 2 ),
			];
		}

		// All gift percentages sorted for the bar chart
		$all_gifts_sorted = [];
		foreach ( $percentages as $cat => $pct ) {
			$info               = $gifts_info[ $cat ] ?? [ 'name' => $cat ];
			$all_gifts_sorted[] = [
				'category'   => $cat,
				'name'       => $info['name'],
				'percentage' => $pct,
			];
		}

		return [
			'top_gifts'       => $top_gifts_detail,
			'all_gifts'       => $all_gifts_sorted,
			'top_ministries'  => $top_ministries,
		];
	}
}
