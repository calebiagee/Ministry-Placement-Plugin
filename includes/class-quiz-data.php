<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPQ_Quiz_Data {

	/* =========================================================================
	   Spiritual Gifts Questions (3 per gift × 17 gifts = 51 questions)
	   Rated on a 1–5 scale: 1 = Not at all like me → 5 = Very much like me
	   ====================================================================== */

	public static function get_spiritual_gifts_questions() {
		return [
			// Teaching (2)
			[ 'id' => 'T1',  'category' => 'teaching',      'text' => 'I enjoy explaining Bible passages in ways that help others understand.',                      'type' => 'scale' ],
			[ 'id' => 'T3',  'category' => 'teaching',      'text' => 'I love preparing lessons or studies that help others grow in their faith.',                    'type' => 'scale' ],

			// Encouragement (2)
			[ 'id' => 'EN1', 'category' => 'encouragement', 'text' => 'I naturally find ways to lift others up when they\'re struggling.',                            'type' => 'scale' ],
			[ 'id' => 'EN3', 'category' => 'encouragement', 'text' => 'I enjoy helping others recognize their potential and cheering them on.',                       'type' => 'scale' ],

			// Giving (2)
			[ 'id' => 'GV1', 'category' => 'giving',        'text' => 'I feel deep satisfaction when I give generously to God\'s work.',                              'type' => 'scale' ],
			[ 'id' => 'GV3', 'category' => 'giving',        'text' => 'I give sacrificially and trust God to meet my own needs.',                                     'type' => 'scale' ],

			// Leadership (2)
			[ 'id' => 'LD1', 'category' => 'leadership',    'text' => 'People naturally look to me to lead when direction is needed.',                                'type' => 'scale' ],
			[ 'id' => 'LD2', 'category' => 'leadership',    'text' => 'I am able to cast vision and motivate others to work toward a goal.',                          'type' => 'scale' ],

			// Mercy (2)
			[ 'id' => 'MC1', 'category' => 'mercy',         'text' => 'I am deeply moved by the pain and struggles of others.',                                       'type' => 'scale' ],
			[ 'id' => 'MC2', 'category' => 'mercy',         'text' => 'I am drawn to minister to people who are hurting or forgotten.',                                'type' => 'scale' ],

			// Service / Helps (2)
			[ 'id' => 'SV1', 'category' => 'service',       'text' => 'I feel most fulfilled when I\'m helping others complete their work.',                           'type' => 'scale' ],
			[ 'id' => 'SV3', 'category' => 'service',       'text' => 'I notice practical needs and want to meet them without being asked.',                          'type' => 'scale' ],

			// Administration (2)
			[ 'id' => 'AD1', 'category' => 'administration','text' => 'I enjoy creating systems and processes that help things run smoothly.',                        'type' => 'scale' ],
			[ 'id' => 'AD2', 'category' => 'administration','text' => 'I thrive when organizing people, tasks, and resources toward a common goal.',                  'type' => 'scale' ],

			// Evangelism (2)
			[ 'id' => 'EV1', 'category' => 'evangelism',    'text' => 'I feel a strong urgency to share the Gospel with those who don\'t know Jesus.',                'type' => 'scale' ],
			[ 'id' => 'EV2', 'category' => 'evangelism',    'text' => 'I find it natural and comfortable to tell others about my faith.',                             'type' => 'scale' ],

			// Pastor / Shepherd (2)
			[ 'id' => 'PS1', 'category' => 'pastor',        'text' => 'I feel responsible for the spiritual growth and wellbeing of those around me.',                'type' => 'scale' ],
			[ 'id' => 'PS2', 'category' => 'pastor',        'text' => 'I naturally invest in long-term relationships with people I disciple.',                        'type' => 'scale' ],

			// Wisdom (2)
			[ 'id' => 'WS1', 'category' => 'wisdom',        'text' => 'People frequently seek my counsel for important decisions.',                                   'type' => 'scale' ],
			[ 'id' => 'WS2', 'category' => 'wisdom',        'text' => 'I have a sense of how biblical truth applies to complex, real-life situations.',               'type' => 'scale' ],

			// Knowledge (2)
			[ 'id' => 'KN1', 'category' => 'knowledge',     'text' => 'I love digging deep into Scripture and researching biblical truth.',                           'type' => 'scale' ],
			[ 'id' => 'KN2', 'category' => 'knowledge',     'text' => 'I often discover insights in the Bible that others may have overlooked.',                     'type' => 'scale' ],

			// Faith (2)
			[ 'id' => 'FT1', 'category' => 'faith',         'text' => 'I often sense God calling me to take steps others consider risky or unreasonable.',            'type' => 'scale' ],
			[ 'id' => 'FT3', 'category' => 'faith',         'text' => 'I have a strong confidence that God will act, even when outcomes seem impossible.',            'type' => 'scale' ],

			// Discernment (2)
			[ 'id' => 'DS1', 'category' => 'discernment',   'text' => 'I can often sense when someone\'s motives don\'t match what they\'re saying.',                 'type' => 'scale' ],
			[ 'id' => 'DS2', 'category' => 'discernment',   'text' => 'I quickly recognize when teaching or doctrine doesn\'t align with Scripture.',                 'type' => 'scale' ],

			// Hospitality (2)
			[ 'id' => 'HP1', 'category' => 'hospitality',   'text' => 'I love making others feel welcomed and completely at home in my presence.',                    'type' => 'scale' ],
			[ 'id' => 'HP3', 'category' => 'hospitality',   'text' => 'I notice when people feel like outsiders and go out of my way to include them.',               'type' => 'scale' ],

			// Prayer / Intercession (2)
			[ 'id' => 'PI1', 'category' => 'intercession',  'text' => 'I feel a deep burden to pray for specific people or needs for extended periods.',              'type' => 'scale' ],
			[ 'id' => 'PI3', 'category' => 'intercession',  'text' => 'Prayer is a primary way I connect with God, and I find it deeply fulfilling.',                 'type' => 'scale' ],

			// Creative Communication (2)
			[ 'id' => 'CC1', 'category' => 'creative',      'text' => 'I express spiritual truths through creative means — music, art, writing, drama, or design.',  'type' => 'scale' ],
			[ 'id' => 'CC2', 'category' => 'creative',      'text' => 'I feel most alive when using creative gifts to communicate God\'s heart.',                     'type' => 'scale' ],

			// Prophecy (2)
			[ 'id' => 'PY1', 'category' => 'prophecy',      'text' => 'I feel compelled to speak God\'s truth even when it\'s difficult or uncomfortable.',           'type' => 'scale' ],
			[ 'id' => 'PY2', 'category' => 'prophecy',      'text' => 'I have a strong sense of what God wants to say to a group or individual.',                     'type' => 'scale' ],
		];
	}

	/* =========================================================================
	   Interest / Skills / Experience Questions (12 questions)
	   These are interspersed throughout the spiritual gifts questions
	   ====================================================================== */

	public static function get_interest_questions() {
		return [
			[
				'id' => 'I1', 'category' => 'interest_age', 'type' => 'choice',
				'text' => 'Which age group do you most enjoy spending time with?',
				'options' => [
					[ 'value' => 'children', 'label' => 'Children (infant – 12)' ],
					[ 'value' => 'youth',    'label' => 'Teenagers & Young Adults' ],
					[ 'value' => 'adults',   'label' => 'Adults' ],
					[ 'value' => 'seniors',  'label' => 'Seniors' ],
					[ 'value' => 'all',      'label' => 'All ages — I love them equally' ],
				],
			],
			[
				'id' => 'I2', 'category' => 'interest_setting', 'type' => 'choice',
				'text' => 'Where do you feel most comfortable serving?',
				'options' => [
					[ 'value' => 'front',       'label' => 'Up front and visible (stage, door, leading)' ],
					[ 'value' => 'behind',      'label' => 'Behind the scenes — I keep things running' ],
					[ 'value' => 'one_on_one',  'label' => 'One-on-one conversations and relationships' ],
					[ 'value' => 'small_group', 'label' => 'Small groups and team settings' ],
				],
			],
			[
				'id' => 'I3', 'category' => 'interest_skill', 'type' => 'multi',
				'text' => 'Which of these describe skills or experiences you have?',
				'options' => [
					[ 'value' => 'music',         'label' => 'I play an instrument or sing' ],
					[ 'value' => 'tech',          'label' => 'I work well with technology, audio/video, or computers' ],
					[ 'value' => 'physical',      'label' => 'I\'m good with my hands — building, maintenance, or physical work' ],
					[ 'value' => 'people',        'label' => 'I\'m a natural communicator and connector with people' ],
				],
			],
			[
				'id' => 'I4', 'category' => 'interest_children_exp', 'type' => 'choice',
				'text' => 'Have you worked with children as a teacher, coach, parent, or volunteer?',
				'options' => [
					[ 'value' => 'yes',   'label' => 'Yes — regularly and I love it' ],
					[ 'value' => 'some',  'label' => 'Some experience, and I\'d like more' ],
					[ 'value' => 'no',    'label' => 'Not much experience yet' ],
				],
			],
			[
				'id' => 'I5', 'category' => 'interest_social', 'type' => 'choice',
				'text' => 'When you walk into a room full of people you don\'t know well, you tend to:',
				'options' => [
					[ 'value' => 'initiate',  'label' => 'Start conversations and introduce yourself' ],
					[ 'value' => 'deepen',    'label' => 'Seek out one person and go deep' ],
					[ 'value' => 'observe',   'label' => 'Watch, listen, and ease in gradually' ],
					[ 'value' => 'help',      'label' => 'Look for something useful to do' ],
				],
			],
			[
				'id' => 'I6', 'category' => 'interest_outreach', 'type' => 'choice',
				'text' => 'How do you feel about reaching people who aren\'t part of a church yet?',
				'options' => [
					[ 'value' => 'yes',      'label' => 'I\'m passionate about it — it drives me' ],
					[ 'value' => 'somewhat', 'label' => 'I care, but prefer working within the church' ],
					[ 'value' => 'no',       'label' => 'I\'m more drawn to serving those already here' ],
				],
			],
			[
				'id' => 'I7', 'category' => 'interest_teaching_exp', 'type' => 'choice',
				'text' => 'Have you ever taught a class, led a group discussion, or mentored someone?',
				'options' => [
					[ 'value' => 'yes',    'label' => 'Yes — it\'s something I do regularly' ],
					[ 'value' => 'some',   'label' => 'A few times — I enjoyed it' ],
					[ 'value' => 'no',     'label' => 'Not much, but I\'m open to growing in it' ],
				],
			],
			[
				'id' => 'I8', 'category' => 'interest_hands_on', 'type' => 'choice',
				'text' => 'If a task needs to get done, you\'re most likely to:',
				'options' => [
					[ 'value' => 'jump_in',  'label' => 'Roll up my sleeves and just do it' ],
					[ 'value' => 'plan',     'label' => 'Make a plan and coordinate with others' ],
					[ 'value' => 'delegate', 'label' => 'Recruit the right people and lead the effort' ],
					[ 'value' => 'support',  'label' => 'Help wherever someone needs an extra hand' ],
				],
			],
			[
				'id' => 'I9', 'category' => 'interest_safety', 'type' => 'choice',
				'text' => 'How do you feel about a role that involves protecting and keeping people safe?',
				'options' => [
					[ 'value' => 'yes',   'label' => 'That\'s exactly my type of role' ],
					[ 'value' => 'maybe', 'label' => 'I\'m open to it' ],
					[ 'value' => 'no',    'label' => 'I\'d prefer a different kind of role' ],
				],
			],
			[
				'id' => 'I10', 'category' => 'interest_prayer_life', 'type' => 'choice',
				'text' => 'How would you describe your prayer life?',
				'options' => [
					[ 'value' => 'deep',    'label' => 'I pray for extended periods regularly — it\'s central to my life' ],
					[ 'value' => 'daily',   'label' => 'I pray briefly but consistently each day' ],
					[ 'value' => 'growing', 'label' => 'It\'s something I\'m growing in' ],
					[ 'value' => 'new',     'label' => 'Prayer is relatively new to me' ],
				],
			],
			[
				'id' => 'I11', 'category' => 'interest_creativity', 'type' => 'multi',
				'text' => 'Which creative areas feel natural to you?',
				'options' => [
					[ 'value' => 'music',    'label' => 'Music — singing, playing, writing songs' ],
					[ 'value' => 'visual',   'label' => 'Visual arts, graphic design, or video' ],
					[ 'value' => 'writing',  'label' => 'Writing, storytelling, or speaking' ],
					[ 'value' => 'none',     'label' => 'Creativity isn\'t really my thing' ],
				],
			],
			[
				'id' => 'I12', 'category' => 'interest_time', 'type' => 'choice',
				'text' => 'How much time could you realistically commit to serving each week?',
				'options' => [
					[ 'value' => 'minimal',    'label' => '1 – 2 hours (Sundays only)' ],
					[ 'value' => 'moderate',   'label' => '3 – 5 hours (Sundays + some midweek)' ],
					[ 'value' => 'significant','label' => '6+ hours (a major commitment)' ],
				],
			],
		];
	}

	/* =========================================================================
	   Interleaved question list
	   Spiritual gifts and interest questions mixed at regular intervals
	   ====================================================================== */

	public static function get_all_questions() {
		$sg  = self::get_spiritual_gifts_questions();  // 51
		$iq  = self::get_interest_questions();          // 12
		$all = [];

		$sg_count  = count( $sg );
		$iq_count  = count( $iq );
		$interval  = max( 1, (int) floor( $sg_count / $iq_count ) ); // ~4
		$iq_cursor = 0;

		foreach ( $sg as $i => $q ) {
			$all[] = $q;
			// Insert a general question after every $interval spiritual-gifts questions
			if ( ( ( $i + 1 ) % $interval === 0 ) && $iq_cursor < $iq_count ) {
				$all[] = $iq[ $iq_cursor++ ];
			}
		}

		// Append any remaining general questions
		while ( $iq_cursor < $iq_count ) {
			$all[] = $iq[ $iq_cursor++ ];
		}

		return $all;
	}

	/* =========================================================================
	   Gift descriptions
	   ====================================================================== */

	public static function get_gifts_info() {
		return [
			'teaching'       => [ 'name' => 'Teaching',               'description' => 'You have a God-given ability to explain Scripture clearly and help others grow in their understanding of God\'s Word.' ],
			'encouragement'  => [ 'name' => 'Encouragement',          'description' => 'You have a remarkable ability to come alongside others, speak life into them, and help them see God\'s purpose.' ],
			'giving'         => [ 'name' => 'Giving',                 'description' => 'You feel called to be generous with your resources and trust God to use your giving to advance His kingdom.' ],
			'leadership'     => [ 'name' => 'Leadership',             'description' => 'You have the ability to cast vision, motivate others, and guide people toward a God-honoring goal.' ],
			'mercy'          => [ 'name' => 'Mercy',                  'description' => 'You are deeply moved by the suffering of others and called to show God\'s compassion to those who are hurting.' ],
			'service'        => [ 'name' => 'Service & Helps',        'description' => 'You find deep fulfillment in supporting others and working behind the scenes so that ministry can happen.' ],
			'administration' => [ 'name' => 'Administration',         'description' => 'You have a gift for organizing people, resources, and systems to accomplish ministry goals effectively.' ],
			'evangelism'     => [ 'name' => 'Evangelism',             'description' => 'You feel a natural urgency and joy in sharing the Gospel and helping others encounter Jesus.' ],
			'pastor'         => [ 'name' => 'Shepherding',            'description' => 'You feel a deep responsibility for the spiritual health and growth of others and love investing in long-term discipleship.' ],
			'wisdom'         => [ 'name' => 'Wisdom',                 'description' => 'You have an unusual ability to apply God\'s truth to real-life situations and offer sound, godly counsel.' ],
			'knowledge'      => [ 'name' => 'Knowledge',              'description' => 'You have a passion for deep Bible study and often discover truths that help the body of Christ grow.' ],
			'faith'          => [ 'name' => 'Faith',                  'description' => 'You have an extraordinary ability to trust God in circumstances others might find impossible, and your faith inspires those around you.' ],
			'discernment'    => [ 'name' => 'Discernment',            'description' => 'You have a Spirit-given ability to recognize truth from error and sense what is spiritually happening in situations.' ],
			'hospitality'    => [ 'name' => 'Hospitality',            'description' => 'You have a gift for making people feel genuinely welcome and creating spaces where people feel loved and included.' ],
			'intercession'   => [ 'name' => 'Prayer & Intercession',  'description' => 'You feel a deep calling to pray fervently for others and believe strongly in the power of persistent prayer.' ],
			'creative'       => [ 'name' => 'Creative Communication', 'description' => 'You communicate spiritual truths through creative means — music, art, writing, drama, or design — in ways that move people\'s hearts.' ],
			'prophecy'       => [ 'name' => 'Prophecy',               'description' => 'You feel a bold calling to speak God\'s truth clearly and directly, even when it\'s challenging or uncomfortable.' ],
		];
	}

	/* =========================================================================
	   Hope Church Ministries
	   'gifts'   → spiritual gift category → match weight
	   'interest_*' → interest question category → answer value → bonus points
	   ====================================================================== */

	public static function get_ministries() {
		return [

			'hope_kids_preschool' => [
				'name'        => 'Hope Kids — Preschool',
				'description' => 'Create a warm, nurturing environment where babies, toddlers, and preschoolers take their very first steps of faith and experience God\'s love through play, stories, and gentle care.',
				'commitment'  => 'Sundays + occasional events',
				'gifts'       => [ 'service' => 3, 'mercy' => 3, 'encouragement' => 2, 'hospitality' => 2, 'pastor' => 1 ],
				'interest_age'          => [ 'children' => 5, 'all' => 1 ],
				'interest_children_exp' => [ 'yes' => 4, 'some' => 2 ],
				'interest_setting'      => [ 'small_group' => 1 ],
			],

			'hope_kids_elementary' => [
				'name'        => 'Hope Kids — Elementary',
				'description' => 'Help kids in Kindergarten through 5th grade discover who Jesus is through energetic worship, engaging Bible lessons, and meaningful friendships that build a lifelong faith.',
				'commitment'  => 'Sundays + occasional events',
				'gifts'       => [ 'teaching' => 4, 'encouragement' => 2, 'service' => 2, 'mercy' => 1, 'hospitality' => 1, 'pastor' => 1 ],
				'interest_age'          => [ 'children' => 5, 'all' => 1 ],
				'interest_children_exp' => [ 'yes' => 4, 'some' => 2 ],
				'interest_teaching_exp' => [ 'yes' => 3, 'some' => 1 ],
				'interest_setting'      => [ 'small_group' => 1 ],
			],

			'hope_students' => [
				'name'        => 'Hope Student Ministries',
				'description' => 'Come alongside middle and high school students through authentic relationships, Biblical teaching, and life-on-life discipleship during a critical season of their lives.',
				'commitment'  => 'Weekly meetings + events',
				'gifts'       => [ 'evangelism' => 2, 'encouragement' => 3, 'leadership' => 2, 'hospitality' => 2, 'pastor' => 3 ],
				'interest_age'     => [ 'youth' => 5, 'all' => 1 ],
				'interest_social'  => [ 'initiate' => 2, 'deepen' => 2 ],
				'interest_setting' => [ 'small_group' => 1, 'one_on_one' => 1 ],
				'interest_outreach'=> [ 'yes' => 1 ],
			],

			'hope_young_adults' => [
				'name'        => 'Hope Young Adults',
				'description' => 'Help build a community where people in their 20s and 30s find real belonging, lasting friendships, and a faith that anchors their lives.',
				'commitment'  => 'Weekly gatherings + community events',
				'gifts'       => [ 'encouragement' => 3, 'hospitality' => 3, 'evangelism' => 2, 'pastor' => 2, 'leadership' => 1 ],
				'interest_age'     => [ 'adults' => 2, 'all' => 1 ],
				'interest_social'  => [ 'initiate' => 2, 'deepen' => 1 ],
				'interest_setting' => [ 'small_group' => 2, 'front' => 1 ],
				'interest_outreach'=> [ 'yes' => 1 ],
			],

			'worship' => [
				'name'        => 'Worship',
				'description' => 'Use your musical gifting to lead our church family into God\'s presence each Sunday through Spirit-led worship that points people to Jesus.',
				'commitment'  => 'Weekly rehearsals + Sundays',
				'gifts'       => [ 'creative' => 5, 'faith' => 1, 'leadership' => 1 ],
				'interest_skill'      => [ 'music' => 6, 'people' => 1 ],
				'interest_creativity' => [ 'music' => 6, 'visual' => 1 ],
				'interest_setting'    => [ 'front' => 3 ],
			],

			'hospitality' => [
				'name'        => 'Hospitality',
				'description' => 'Create a warm, welcoming atmosphere where everyone — regulars and first-timers alike — feels genuinely seen, valued, and at home at Hope Church.',
				'commitment'  => 'Sunday mornings',
				'gifts'       => [ 'hospitality' => 5, 'service' => 2, 'mercy' => 1, 'encouragement' => 2 ],
				'interest_social'   => [ 'initiate' => 3, 'help' => 1 ],
				'interest_setting'  => [ 'front' => 2 ],
				'interest_skill'    => [ 'people' => 3 ],
			],

			'greeters' => [
				'name'        => 'Greeters',
				'description' => 'Be the first smile someone sees — warmly welcoming guests and members at the door and helping everyone feel like they\'ve come home.',
				'commitment'  => 'Sunday mornings (rotating schedule)',
				'gifts'       => [ 'hospitality' => 4, 'encouragement' => 2, 'service' => 1 ],
				'interest_social'  => [ 'initiate' => 4, 'help' => 1 ],
				'interest_setting' => [ 'front' => 3 ],
				'interest_skill'   => [ 'people' => 3 ],
			],

			'ushers' => [
				'name'        => 'Ushers',
				'description' => 'Serve in the aisles to help services run smoothly, assist guests, manage seating, and support our weekend experience from start to finish.',
				'commitment'  => 'Sunday mornings (rotating schedule)',
				'gifts'       => [ 'service' => 3, 'administration' => 2, 'hospitality' => 2 ],
				'interest_setting'   => [ 'front' => 1, 'behind' => 2 ],
				'interest_hands_on'  => [ 'jump_in' => 2, 'support' => 2 ],
				'interest_skill'     => [ 'people' => 1 ],
			],

			'security' => [
				'name'        => 'Security',
				'description' => 'Protect and serve our church family by maintaining a safe environment during services and events so everyone can worship without distraction.',
				'commitment'  => 'Sundays + special events',
				'gifts'       => [ 'service' => 3, 'administration' => 2, 'leadership' => 2, 'discernment' => 3, 'faith' => 1 ],
				'interest_safety'    => [ 'yes' => 7, 'maybe' => 2 ],
				'interest_setting'   => [ 'front' => 1, 'behind' => 1 ],
				'interest_hands_on'  => [ 'jump_in' => 1, 'plan' => 1 ],
			],

			'facilities' => [
				'name'        => 'Facilities',
				'description' => 'Keep our campus clean, safe, and fully set up so that every ministry and service can happen without a hitch. If you love practical hands-on work, this is your place.',
				'commitment'  => 'Weekdays + weekends as needed',
				'gifts'       => [ 'service' => 5, 'administration' => 2 ],
				'interest_setting'   => [ 'behind' => 4 ],
				'interest_hands_on'  => [ 'jump_in' => 4, 'support' => 2 ],
				'interest_skill'     => [ 'physical' => 6, 'tech' => 1 ],
			],

			'hope_family' => [
				'name'        => 'Lead a Hope Family',
				'description' => 'Facilitate a small group of people who do life together — growing in God\'s Word, carrying one another\'s burdens, and becoming true church family.',
				'commitment'  => 'Weekly Hope Family meeting + prep time',
				'gifts'       => [ 'teaching' => 2, 'pastor' => 4, 'encouragement' => 3, 'wisdom' => 2, 'hospitality' => 2 ],
				'interest_age'          => [ 'adults' => 2, 'all' => 1 ],
				'interest_setting'      => [ 'small_group' => 5, 'one_on_one' => 1 ],
				'interest_teaching_exp' => [ 'yes' => 2, 'some' => 1 ],
				'interest_social'       => [ 'deepen' => 2, 'initiate' => 1 ],
			],

			'media' => [
				'name'        => 'Media Team',
				'description' => 'Use your technical and creative skills to support live worship, livestreaming, social media, graphic design, and the digital presence of Hope Church.',
				'commitment'  => 'Sundays + special productions',
				'gifts'       => [ 'administration' => 1, 'creative' => 3, 'service' => 3 ],
				'interest_skill'      => [ 'tech' => 6, 'music' => 1 ],
				'interest_creativity' => [ 'visual' => 5, 'music' => 2 ],
				'interest_setting'    => [ 'behind' => 4 ],
				'interest_hands_on'   => [ 'plan' => 2, 'jump_in' => 1 ],
			],

			'outreach' => [
				'name'        => 'Outreach',
				'description' => 'Bring the love of Jesus to our city through servant evangelism, community service, and Gospel conversations that reach people who don\'t yet know Hope Church.',
				'commitment'  => 'Monthly events + ongoing opportunities',
				'gifts'       => [ 'evangelism' => 4, 'giving' => 1, 'service' => 2, 'faith' => 2, 'mercy' => 2 ],
				'interest_outreach'   => [ 'yes' => 6, 'somewhat' => 2 ],
				'interest_social'     => [ 'initiate' => 3 ],
				'interest_hands_on'   => [ 'jump_in' => 2 ],
			],

			'prayer_team' => [
				'name'        => 'Prayer Ministry Team',
				'description' => 'Come alongside others to pray with them during services, at prayer gatherings, and at key moments in people\'s lives. If intercession is central to your calling, this is your place.',
				'commitment'  => 'Sundays + prayer gatherings',
				'gifts'       => [ 'intercession' => 6, 'mercy' => 2, 'encouragement' => 2, 'faith' => 3, 'wisdom' => 1 ],
				'interest_prayer_life' => [ 'deep' => 7, 'daily' => 3, 'growing' => 1 ],
				'interest_social'      => [ 'one_on_one' => 3, 'deepen' => 2 ],
				'interest_setting'     => [ 'one_on_one' => 2, 'front' => 1 ],
			],

		];
	}

	/* Maps interest question IDs to category keys used in ministry scoring */
	public static function get_interest_category_map() {
		return [
			'I1'  => 'interest_age',
			'I2'  => 'interest_setting',
			'I3'  => 'interest_skill',
			'I4'  => 'interest_children_exp',
			'I5'  => 'interest_social',
			'I6'  => 'interest_outreach',
			'I7'  => 'interest_teaching_exp',
			'I8'  => 'interest_hands_on',
			'I9'  => 'interest_safety',
			'I10' => 'interest_prayer_life',
			'I11' => 'interest_creativity',
			'I12' => 'interest_time',
		];
	}
}
