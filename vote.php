<?php
/*
Plugin Name: Vote
Plugin URI: http://www.ask-oracle.com
Description: A highly documented plugin that demonstrates how to create custom List Tables using official WordPress APIs.
Version: 0.1
Author: Tapan Sodagar
Author URI: http://www.ask-oracle.com
*/
function add_js_for_vote()
{
	wp_enqueue_script('vote-js', plugins_url('js/vote.js', __FILE__));
}
function add_stylesheet_for_votes_layout()
{
	wp_enqueue_style('votes-style', plugins_url('css/vote.css', __FILE__));
}
function voter_layout_top_area()
{
	global $post;
	$post_type = $post->post_type;
	if($post_type == "page")
	{
		$user_id = get_current_user_id();
		$component_name = "blog";
		$type = $post->post_type;
		$item_id = 1;
		$secondary_item_id = $post->ID;
	}
	else if($post_type == "post")
	{
		$user_id = get_current_user_id();
		$component_name = "blog";
		$type = $post->post_type;
		$item_id = 1;
		$secondary_item_id = $post->ID;
	}
	$current_user_votes = voter_get_current_user_votes_top_area($user_id,$component_name,$type,$item_id,$secondary_item_id);
	
	echo '<div class="vote alignright">';
		echo voter_vote_link($post->ID,'up',$current_user_votes);
			echo '<span class="vote-count-post">';
			echo voter_get_post_votes($post->ID);
			echo '</span>';
		echo voter_vote_link($post->ID,'down',$current_user_votes);
	echo '</div>';
}
function voter_layout_bottom_area()
{
	global $post;	
	$post_type = $post->post_type;
	$comment_id = get_comment_ID();

	if($post_type == "page")
	{
		$user_id = get_current_user_id();
		$component_name = "blog";
		$type = $post->post_type;
		$item_id = $post->ID;
		$secondary_item_id = $comment_id;
	}
	else if($post_type == "post")
	{
		$user_id = get_current_user_id();
		$component_name = "blog";
		$type = $post->post_type;
		$item_id = $post->ID;
		$secondary_item_id = $comment_id;
	}

	$current_user_votes = voter_get_current_user_votes_bottom_area($user_id,$component_name,$type,$item_id,$secondary_item_id);
	echo '<div class="vote alignright">';
		echo voter_vote_link_bottom($post->ID,'up',$current_user_votes);
			echo '<span class="vote-count-post">';
			echo voter_get_post_votes($post->ID);
			echo '</span>';
		echo voter_vote_link_bottom($post->ID,'down',$current_user_votes);
	echo '</div>';
}
function voter_vote_link_bottom($post_id,$type,$user_votes = false)
{
		global $post;
		$post_type = $post->post_type;
		$comment_id = get_comment_ID();

		$vote_id = 0;
		if( !empty( $user_votes[$comment_id] ) )
			$vote_id = $user_votes[$comment_id]->id;			
			
		if( $type == 'up' )
		{
			$class = 'vote-up-off';
			if(!empty($user_votes[$comment_id]) && $user_votes[$comment_id]->action == 'up')
			{
				$class = 'vote-up-on';
			}
		}
		else if( $type == 'down' )
		{
			$class = 'vote-down-off';
			if(!empty($user_votes[$comment_id]) && $user_votes[$comment_id]->action == 'down')
			{
				$class = 'vote-down-on';
			}
		}
		
		if($vote_id == 0 && is_user_logged_in())
		{
			$user_id = get_current_user_id();
			$params = array('user_id' => $user_id, 'component' => 'blog','type' => $post_type,'action' => $type,'item_id' => $post_id,'secondary_item_id' => $comment_id);
			$url0 = get_permalink($post_id);
			$url = esc_url(wp_nonce_url(add_query_arg($params,$url0),'toggle-vote_' . $post_id));
			echo '<a rel="nofollow" class="engagement ' . $class . '" href="' . $url . '"></a>';
		}
		else
		{
			echo '<a rel="nofollow" class="engagement ' . $class . '" href="' . $url . '"></a>';
		}
}
function voter_vote_link($post_id,$type,$user_votes = false)
{
		global $post;
		$post_type = $post->post_type;

		$vote_id = 0;
		if( !empty( $user_votes[$post_id] ) )
			$vote_id = $user_votes[$post_id]->id;

		if( $type == 'up' )
		{
			$class = 'vote-up-off';
			if(!empty($user_votes[$post_id]) && $user_votes[$post_id]->action == 'up')
			{
				$class = 'vote-up-on';
			}
		}
		else if( $type == 'down' )
		{
			$class = 'vote-down-off';
			if(!empty($user_votes[$post_id]) && $user_votes[$post_id]->action == 'down')
			{
				$class = 'vote-down-on';
			}
		}
		
		if($vote_id == 0 && is_user_logged_in())
		{
			$user_id = get_current_user_id();
			$params = array('user_id' => $user_id, 'component' => 'blog','type' => $post_type,'action' => $type,'item_id' => 1,'secondary_item_id' => $post_id);
			
			$url0 = get_permalink($post_id);
			$url = esc_url(wp_nonce_url(add_query_arg($params,$url0),'toggle-vote_' . $post_id));
			echo '<a rel="nofollow" class="engagement ' . $class . '" href="' . $url . '"></a>';
		}
		else
		{
			echo '<a rel="nofollow" class="engagement ' . $class . '" href="' . $url . '"></a>';
		}
}
function voter_get_post_votes($post_id)
{
	return $post_id;
}
function voter_get_current_user_votes_top_area($user_id,$component_name,$type,$item_id,$secondary_item_id)
{
	if(!is_user_logged_in())
		return false;

	return get_specific_user($user_id,$component_name,$type,$item_id,$secondary_item_id);
}
function voter_get_current_user_votes_bottom_area($user_id,$component_name,$type,$item_id,$secondary_item_id)
{
	if(!is_user_logged_in())
		return false;

	return get_specific_user($user_id,$component_name,$type,$item_id,$secondary_item_id);
}
function get_specific_user($user_id,$component_name,$type,$item_id,$secondary_item_id)
{
	global $wpdb;
	$current_user = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM ask_votes WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
	return $current_user;
}
function voter_function_giving_votes_ajax()
{
	$user_id = $_POST['user_id'];
	$component = $_POST['component'];
	echo $component;
	exit;
}
function voter_function_giving_votes()
{
	global $wpdb;
	if(isset($_REQUEST) && !empty($_REQUEST))
	{
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['component']) && isset($_REQUEST['type']) && isset($_REQUEST['action']) &&  isset($_REQUEST['item_id']) && isset($_REQUEST['secondary_item_id']))
		{
			$q = $wpdb->query("INSERT INTO ask_votes (user_id, component, type, action, date_recorded, item_id, secondary_item_id) VALUES ('".$_REQUEST['user_id']."', '".$_REQUEST['component']."', '".$_REQUEST['type']."', '".$_REQUEST['action']."', '".date("Y/m/d")."', '".$_REQUEST['item_id']."', '".$_REQUEST['secondary_item_id']."')");
			if(!($q))
				return false;
		}
	}
}
add_action('wp_enqueue_scripts', 'add_stylesheet_for_votes_layout');
add_filter('comment_reply_link','voter_layout_bottom_area');
add_filter('the_content','voter_layout_top_area');
add_action('wp_enqueue_scripts', 'add_js_for_vote');
add_action('wp_ajax_up', 'voter_function_giving_votes_ajax');
add_action('init', 'voter_function_giving_votes');
?>