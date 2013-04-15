<?php

/**
 * My Log Classes
 *
 * @package BuddyPress
 * @subpackage ActivityClasses
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

Class Voter_Entry {
	var $id;
	var $item_id;
	var $secondary_item_id;
	var $user_id;
	var $component;
	var $type;
	var $action;
	var $date_recorded;
	const VOTER_TABLE = 'ask_votes';

	function Voter_Entry( $id = false ) {
		$this->__construct( $id );
	}

	function __construct( $id = false ) {
		global $bp;

		if ( !empty( $id ) ) {
			$this->id = $id;
			$this->populate();
		}
	}

	function populate() {
		global $wpdb, $bp;

		if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . Voter_Entry::VOTER_TABLE . " WHERE id = %d", $this->id ) ) ) {
			$this->id                = $row->id;
			$this->item_id           = $row->item_id;
			$this->secondary_item_id = $row->secondary_item_id;
			$this->user_id           = $row->user_id;
			$this->component         = $row->component;
			$this->type              = $row->type;
			$this->action            = $row->action;
			$this->date_recorded     = $row->date_recorded;
		}

	}

	function save() {
		global $wpdb, $bp, $current_user;

		if ( !$this->component || !$this->type )
			return false;

		// If we have an existing ID, update the activity item, otherwise insert it.
		if ( $this->id )
			$q = $wpdb->prepare( "UPDATE " . Voter_Entry::VOTER_TABLE . " SET user_id = %d, component = %s, type = %s, action = %s, date_recorded = %s, item_id = %s, secondary_item_id = %s WHERE id = %d", $this->user_id, $this->component, $this->type, $this->action, $this->date_recorded, $this->item_id, $this->secondary_item_id, $this->id );
		else
			$q = $wpdb->prepare( "INSERT INTO " . Voter_Entry::VOTER_TABLE . " ( user_id, component, type, action, date_recorded, item_id, secondary_item_id ) VALUES ( %d, %s, %s, %s, %s, %d, %d )", $this->user_id, $this->component, $this->type, $this->action, $this->date_recorded, $this->item_id, $this->secondary_item_id );

		if ( !$wpdb->query( $q ) )
			return false;

		if ( empty( $this->id ) )
			$this->id = $wpdb->insert_id;

		return true;
	}

	// Static Functions

	function get( $component_id, $filter = false, $in = false ) {
		global $wpdb, $bp;

		// Select conditions
		$select_sql = "SELECT a.secondary_item_id as post, a.action, count(a.action) as votes";

		$from_sql = "FROM " . Voter_Entry::VOTER_TABLE . " a";

		// Where conditions
		$where_conditions = array();

		// Filtering
		if ( $filter && $filter_sql = Voter_Entry::get_filter_sql( $filter ) )
			$where_conditions['filter_sql'] = $filter_sql;

		// The specific ids to which you want to limit the query
		if ( !empty( $in ) ) {
			if ( is_array( $in ) ) {
				$in = implode ( ',', array_map( 'absint', $in ) );
			} else {
				$in = implode ( ',', array_map( 'absint', explode ( ',', $in ) ) );
			}

			$where_conditions['in'] = "a.id IN ({$in})";
		}
			$where_sql = 'WHERE a.component = "forums" AND a.type = "topic" AND a.item_id = ' . $component_id;
			$group_by_sql = 'GROUP BY a.secondary_item_id, a.action';

			$activities = $wpdb->get_results( $wpdb->prepare( "{$select_sql} {$from_sql} {$where_sql} {$group_by_sql}" ) );

		// Get the fullnames of users so we don't have to query in the loop
		if ( bp_is_active( 'xprofile' ) && $activities ) {
			foreach ( (array)$activities as $activity ) {
				if ( (int)$activity->user_id )
					$activity_user_ids[] = $activity->user_id;
			}

			$activity_user_ids = implode( ',', array_unique( (array)$activity_user_ids ) );
			if ( !empty( $activity_user_ids ) ) {
				if ( $names = $wpdb->get_results( $wpdb->prepare( "SELECT user_id, value AS user_fullname FROM {$bp->profile->table_name_data} WHERE field_id = 1 AND user_id IN ({$activity_user_ids})" ) ) ) {
					foreach ( (array)$names as $name )
						$tmp_names[$name->user_id] = $name->user_fullname;

					foreach ( (array)$activities as $i => $activity ) {
						if ( !empty( $tmp_names[$activity->user_id] ) )
							$activities[$i]->user_fullname = $tmp_names[$activity->user_id];
					}

					unset( $names );
					unset( $tmp_names );
				}
			}
		}

		$votes_by_post = array();

		foreach( (array)$activities as $activity )
		{
			if( empty( $votes_by_post[$activity->post] ) )
				$votes_by_post[$activity->post] = array();

				$votes_by_post[$activity->post][$activity->action] = $activity->votes;
		}

		return $votes_by_post;
		
	}

	function get_specific_user( $user_id, $topic_id ) {
		global $wpdb;
		$current_user = $wpdb->get_results( "SELECT secondary_item_id, id, action FROM " . Voter_Entry::VOTER_TABLE . " WHERE user_id = $user_id AND item_id = $topic_id AND component = 'forums' AND type = 'topic' ", OBJECT_K );
		return $current_user;
	}

	function get_id( $user_id, $component, $type, $item_id, $secondary_item_id, $action, $content, $date_recorded ) {
		global $bp, $wpdb;

		$where_args = false;

		if ( !empty( $user_id ) )
			$where_args[] = $wpdb->prepare( "user_id = %d", $user_id );

		if ( !empty( $component ) )
			$where_args[] = $wpdb->prepare( "component = %s", $component );

		if ( !empty( $type ) )
			$where_args[] = $wpdb->prepare( "type = %s", $type );

		if ( !empty( $item_id ) )
			$where_args[] = $wpdb->prepare( "item_id = %s", $item_id );

		if ( !empty( $secondary_item_id ) )
			$where_args[] = $wpdb->prepare( "secondary_item_id = %s", $secondary_item_id );

		if ( !empty( $action ) )
			$where_args[] = $wpdb->prepare( "action = %s", $action );

		if ( !empty( $content ) )
			$where_args[] = $wpdb->prepare( "content = %s", $content );

		if ( !empty( $date_recorded ) )
			$where_args[] = $wpdb->prepare( "date_recorded = %s", $date_recorded );

		if ( !empty( $where_args ) )
			$where_sql = 'WHERE ' . join( ' AND ', $where_args );
		else
			return false;

		return $wpdb->get_var( "SELECT id FROM " . Voter_Entry::VOTER_TABLE . " {$where_sql}" );
	}

	function delete( $args ) {
		global $wpdb, $bp;

		$defaults = array(
			'id'                => false,
			'action'            => false,
			'content'           => false,
			'component'         => false,
			'type'              => false,
			'primary_link'      => false,
			'user_id'           => false,
			'item_id'           => false,
			'secondary_item_id' => false,
			'date_recorded'     => false,
			'hide_sitewide'     => false
		);
		$params = wp_parse_args( $args, $defaults );
		extract( $params );

		$where_args = false;

		if ( !empty( $id ) )
			$where_args[] = $wpdb->prepare( "id = %d", $id );

		if ( !empty( $user_id ) )
			$where_args[] = $wpdb->prepare( "user_id = %d", $user_id );

		if ( !empty( $action ) )
			$where_args[] = $wpdb->prepare( "action = %s", $action );

		if ( !empty( $content ) )
			$where_args[] = $wpdb->prepare( "content = %s", $content );

		if ( !empty( $component ) )
			$where_args[] = $wpdb->prepare( "component = %s", $component );

		if ( !empty( $type ) )
			$where_args[] = $wpdb->prepare( "type = %s", $type );

		if ( !empty( $primary_link ) )
			$where_args[] = $wpdb->prepare( "primary_link = %s", $primary_link );

		if ( !empty( $item_id ) )
			$where_args[] = $wpdb->prepare( "item_id = %s", $item_id );

		if ( !empty( $secondary_item_id ) )
			$where_args[] = $wpdb->prepare( "secondary_item_id = %s", $secondary_item_id );

		if ( !empty( $date_recorded ) )
			$where_args[] = $wpdb->prepare( "date_recorded = %s", $date_recorded );

		if ( !empty( $hide_sitewide ) )
			$where_args[] = $wpdb->prepare( "hide_sitewide = %d", $hide_sitewide );

		if ( !empty( $where_args ) )
			$where_sql = 'WHERE ' . join( ' AND ', $where_args );
		else
			return false;

		// Fetch the activity IDs so we can delete any comments for this activity item
		$activity_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM " . Voter_Entry::VOTER_TABLE . " {$where_sql}" ) );

		if ( !$wpdb->query( $wpdb->prepare( "DELETE FROM " . Voter_Entry::VOTER_TABLE . " {$where_sql}" ) ) )
			return false;

		if ( $activity_ids ) {
			Voter_Entry::delete_activity_item_comments( $activity_ids );
			Voter_Entry::delete_activity_meta_entries( $activity_ids );

			return $activity_ids;
		}

		return $activity_ids;
	}

	function get_in_operator_sql( $field, $items ) {
		global $wpdb;

		// split items at the comma
		$items_dirty = explode( ',', $items );

		// array of prepared integers or quoted strings
		$items_prepared = array();

		// clean up and format each item
		foreach ( $items_dirty as $item ) {
			// clean up the string
			$item = trim( $item );
			// pass everything through prepare for security and to safely quote strings
			$items_prepared[] = ( is_numeric( $item ) ) ? $wpdb->prepare( '%d', $item ) : $wpdb->prepare( '%s', $item );
		}

		// build IN operator sql syntax
		if ( count( $items_prepared ) )
			return sprintf( '%s IN ( %s )', trim( $field ), implode( ',', $items_prepared ) );
		else
			return false;
	}

	function get_filter_sql( $filter_array ) {
		global $wpdb;

		if ( !empty( $filter_array['user_id'] ) ) {
			$user_sql = Voter_Entry::get_in_operator_sql( 'a.user_id', $filter_array['user_id'] );
			if ( !empty( $user_sql ) )
				$filter_sql[] = $user_sql;
		}

		if ( !empty( $filter_array['object'] ) ) {
			$object_sql = Voter_Entry::get_in_operator_sql( 'a.component', $filter_array['object'] );
			if ( !empty( $object_sql ) )
				$filter_sql[] = $object_sql;
		}

		if ( !empty( $filter_array['action'] ) ) {
			$action_sql = Voter_Entry::get_in_operator_sql( 'a.type', $filter_array['action'] );
			if ( !empty( $action_sql ) )
				$filter_sql[] = $action_sql;
		}

		if ( !empty( $filter_array['primary_id'] ) ) {
			$pid_sql = Voter_Entry::get_in_operator_sql( 'a.item_id', $filter_array['primary_id'] );
			if ( !empty( $pid_sql ) )
				$filter_sql[] = $pid_sql;
		}

		if ( !empty( $filter_array['secondary_id'] ) ) {
			$sid_sql = Voter_Entry::get_in_operator_sql( 'a.secondary_item_id', $filter_array['secondary_id'] );
			if ( !empty( $sid_sql ) )
				$filter_sql[] = $sid_sql;
		}

		if ( empty($filter_sql) )
			return false;

		return join( ' AND ', $filter_sql );
	}

	function get_last_updated() {
		global $bp, $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT date_recorded FROM " . Voter_Entry::VOTER_TABLE . " ORDER BY date_recorded DESC LIMIT 1" ) );
	}

}

function voter_add( $args = '' ) {
	global $bp;

	$defaults = array(
		'id'                => false, // Pass an existing activity ID to update an existing entry.

		'action'            => '',    // The activity action - e.g. "Jon Doe posted an update"

		'component'         => false, // The name/ID of the component e.g. groups, profile, mycomponent
		'type'              => false, // The activity type e.g. activity_update, profile_updated

		'user_id'           => $bp->loggedin_user->id, // Optional: The user to record the activity for, can be false if this activity is not for a user.
		'item_id'           => false, // Optional: The ID of the specific item being recorded, e.g. a blog_id
		'secondary_item_id' => false, // Optional: A second ID used to further filter e.g. a comment_id
		'recorded_time'     => bp_core_current_time(), // The GMT time that this activity was recorded
	);
	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	if( empty( $id ) )
	{
		$current_user_votes = Voter_Entry::get_specific_user( bp_loggedin_user_id(), $item_id  );
		if( !empty( $current_user_votes[$secondary_item_id] ) )
		{
			if( $current_user_votes[$secondary_item_id]->action == $action )
				return false;
			else $id = $current_user_votes[$secondary_item_id]->id;
			
		}
	}
	// Setup activity to be added
	$entry		                 = new Voter_Entry( $id );

	if( !empty( $id ) && !empty( $entry->action ) && $entry->action != 'neutral' )
	{
		if( $entry->action == $action )
			return false;
		else $action = 'neutral';
	}
	$entry->user_id           = $user_id;
	$entry->component         = $component;
	$entry->type              = $type;
	$entry->action            = $action;
	$entry->item_id           = $item_id;
	$entry->secondary_item_id = $secondary_item_id;
	$entry->date_recorded     = $recorded_time;

	if ( !$entry->save() )
		return false;

	return $entry->id;
}

function voter_get( $args = '' ) {
	$defaults = array(
		'in'               => false,  // Comma-separated list or array of activity IDs to which you want to limit the query

		/**
		 * Pass filters as an array -- all filter items can be multiple values comma separated:
		 * array(
		 * 	'user_id'      => false, // user_id to filter on
		 *	'object'       => false, // object to filter on e.g. groups, profile, status, friends
		 *	'action'       => false, // action to filter on e.g. activity_update, profile_updated
		 *	'primary_id'   => false, // object ID to filter on e.g. a group_id or forum_id or blog_id etc.
		 *	'secondary_id' => false, // secondary object ID to filter on e.g. a post_id
		 * );
		 */
		'filter' => array()
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$activity = Voter_Entry::get( $id, $filter, $in );

	return $activity;
}

function voter_get_post_votes( $post_id, $votes_by_post )
{
	return $votes_by_post[$post_id]['vote_up'] - $votes_by_post[$post_id]['vote_down'];
}
function voter_get_current_user_votes()
{
	if( !is_user_logged_in() )
		return false;

	return Voter_Entry::get_specific_user( bp_loggedin_user_id(), bp_get_the_topic_id()  );
}

?>