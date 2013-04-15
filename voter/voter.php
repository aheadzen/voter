<?php
/*
Plugin Name: Voter
Plugin URI: http://www.mattvanandel.com/
Description: A highly documented plugin that demonstrates how to create custom List Tables using official WordPress APIs.
Version: 0.1
Author: Arpit Tambi
Author URI: http://www.ask-oracle.com
License: GPL2
*/


 require_once( 'voter-classes.php' );
add_action( 'bp_setup_globals', 'voter_setup_globals' );

function voter_ajax_action() {
	global $wpdb, $bbdb;

	$post_id = (int)$_POST['post_id'];
	if( empty($post_id) )
		return;

	$post = bp_forums_get_post( $post_id );
	
	if( empty( $post ) )
		return;

	check_ajax_referer( 'toggle-vote_' . $post_id, '_ajax_nonce' );

	switch($_POST['vote'])
	{
		case 'up':
		case 'down':
			$action = 'vote_' . $_POST['vote'];
			break;
	}

	if( empty( $action ) )
		return;
	
	$vote_id = (int)$_POST['vote_id'];
	$topic_id = $post->topic_id;

	 $votes = array(
		 'id'				=> $vote_id,
		'action'            => $action,    // The activity action - e.g. "Jon Doe posted an update"

		'component'         => 'forums', // The name/ID of the component e.g. groups, profile, mycomponent
		'type'              => 'topic', // The activity type e.g. activity_update, profile_updated

		'item_id'           => $topic_id, // Optional: The ID of the specific item being recorded, e.g. a blog_id
		'secondary_item_id' => $post_id, // Optional: A second ID used to further filter e.g. a comment_id
	);

	if ( voter_add( $votes ) && $action == 'vote_up' )
	{
		$user_id = bp_loggedin_user_id();
		$userlink = bp_core_get_userlink( $user_id );
		$topic = bp_forums_get_topic_details( $topic_id );
		$poster_link = bp_core_get_userlink( $post->poster_id );
		$topic_link = '<a href="' . bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $topic->object_slug . '/' . 'forum/topic/' . $topic->topic_slug . '/">' . $topic->topic_title . '</a>';

		bp_activity_add( array(
			'user_id'   => $user_id,
			'action'    => sprintf( __( "%s likes %s's post in %s", 'buddypress' ), $userlink, $poster_link, $topic_link ),
			'component' => 'votes',
			'item_id'           => $topic_id, 
			'secondary_item_id' => $post_id,
			'type'      => 'forums'
		) );

		bp_core_add_notification( $user_id, $post->poster_id, 'votes', $action, $post_id );

		return true;
	} else return false;

	
}
add_action( 'wp_ajax_voter_action', 'voter_ajax_action' );

function voter_vote_link($post_id, $type, $user_votes = false)
	{
	
		if( bp_get_the_topic_post_is_mine() )
			return;

		$vote_id = 0;

		if( !empty( $user_votes[$post_id] ) )
			$vote_id = $user_votes[$post_id]->id;

		if( $type == 'up' )
		{
			$class = 'vote-up-off';

			if( !empty( $user_votes[$post_id] ) && $user_votes[$post_id]->action == 'vote_up' )
			{
				$class = 'vote-up-on';
			}


		}
		else if( $type == 'down' )
		{
			$class = 'vote-down-off';
			if( !empty( $user_votes[$post_id] ) && $user_votes[$post_id]->action == 'vote_down' )
			{
				$class = 'vote-down-on';
			}

		}
		$params = array('request' => $type, 'post_id' => $post_id, 'vote_id' => $vote_id );

		$url0 = bp_get_the_topic_permalink();
		if( !is_user_logged_in() )
			$url = registrationURL( $url0 );
		else $url = esc_url(  wp_nonce_url( add_query_arg( $params, $url0 ), 'toggle-vote_' . $post_id ) );

		//echo "<span>$pre<a href='$url'>$mid</a>$post</span>";
		echo '<a rel="nofollow" class="engagement ' . $class . '" href="' . $url . '"></a>';

		/*if (  !is_null($is_fav) )
			echo "<span>$pre<a href='$url'>$mid</a>$post</span>";*/
	}
function voter_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {

	global $bp;
	$post = bp_forums_get_post( $secondary_item_id );

	$topic_url = $bp->loggedin_user->domain . $bp->activity->slug . '/' . $bp->gifts->slug . '/';
	$topic_id = $post->topic_id;

	$topic = bp_forums_get_topic_details( $topic_id );
	$topic_link = '<a href="' . bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $topic->object_slug . '/' . 'forum/topic/' . $topic->topic_slug . '/">' . $topic->topic_title . '</a>';

	$voter_link = bp_core_get_userlink( $item_id );

	$notification = "$voter_link likes your post in $topic_link";

	return array( 'text' => $notification,
					'link' => '' );
}
function voter_setup_globals()
{
	global $bp;

	$bp->votes->notification_callback        = 'voter_format_notifications';
}
