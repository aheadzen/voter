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
	global $post,$bp;
	$post_type = $post->post_type;
	if($post_type == "page")
	{
		if(bp_is_blog_page())
		{
			$user_id = get_current_user_id();
			$component_name = "blog";
			$type = $post->post_type;
			$item_id = 0;
			$secondary_item_id = $post->ID;
		}
		else
		{
			$group_id = $bp->groups->current_group->id;
			if(isset($group_id) && $group_id != "")
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $post->post_title;
				$item_id = $post->ID;
				$secondary_item_id = $group_id;
			}
		}
	}
	else if($post_type == "post")
	{
		if(bp_is_blog_page())
		{
			$user_id = get_current_user_id();
			$component_name = "blog";
			$type = $post->post_type;
			$item_id = 0;
			$secondary_item_id = $post->ID;
		}
		else
		{
			$group_id = $bp->groups->current_group->id;
			if(isset($group_id) && $group_id != "")
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $post->post_title;
				$item_id = $post->ID;
				$secondary_item_id = $group_id;
			}
		}
	}
	else
	{	
		if(bp_is_blog_page())
		{
			$user_id = get_current_user_id();
			$component_name = "blog";
			$type = $post->post_type;
			$item_id = 0;
			$secondary_item_id = $post->ID;
		}
		else
		{
			$group_id = $bp->groups->current_group->id;
			if(isset($group_id) && $group_id != "")
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $post->post_title;
				$item_id = $post->ID;
				$secondary_item_id = $group_id;
			}
		}
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
	global $post,$bp,$thread_template,$bbP,$forum_id;
	$post_type = $post->post_type;	
	$comment_id = get_comment_ID();

	if($bp)
	{
		$check_url_for_topic = $bp->unfiltered_uri;
		if (in_array("topic", $check_url_for_topic))
		{
			$topic_id = 1;
		}
	}	

	//$topic_id = bbp_get_reply_id();	
	//$topic_id = bbp_reply_topic_id();	
	//$topic_reply_id = bbp_get_reply_topic_id();
	//echo "Topic ID :- " . $topic_id . "<br>";
	//echo "Topic Reply ID :- " . $topic_reply_id . "<br>";
	//exit;

	
	if($post_type == "page")
	{
		if(bp_is_blog_page() && (!isset($topic_id) && $topic_id == ""))
		{
			$user_id = get_current_user_id();
			$component_name = "blog";
			$type = $post->post_type;
			$item_id = $post->ID;
			$secondary_item_id = $comment_id;		
		}
		else
		{
			$activity_id = bp_get_activity_id();
			$group_id = $bp->groups->current_group->id;
			$member_id = $bp->displayed_user->id;
						
			if(isset($activity_id) && $activity_id != "")
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $bp->current_component;
				$item_id = $post->ID;
				$activity_id = bp_get_activity_id();
				$secondary_item_id = $activity_id;
			}			
			else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
			{			
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $bp->current_component;
				$item_id = $post->ID;
				$secondary_item_id = $group_id;
			}
			else if(isset($member_id) && $member_id != "")
			{
				if(strtolower($bp->current_component) == "profile")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $member_id;
				}
				else if(strtolower($bp->current_component) == "messages")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $thread_template->message->id;
				}
			}
			else if(isset($topic_id) && $topic_id != "")
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = "forum";
				$item_id = $post->ID;
				$secondary_item_id = $post->ID;
			}
			else
			{
				// bp_get_member_user_id
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $post->post_title;
				$item_id = $post->ID;
				$member_id = get_current_user_id();
				$secondary_item_id = $member_id;
			}		
		}	
	}
	else if($post_type == "post" && (!isset($topic_id) && $topic_id == ""))
	{
		if(bp_is_blog_page())
		{
			$user_id = get_current_user_id();
			$component_name = "blog";
			$type = $post->post_type;
			$item_id = $post->ID;
			$secondary_item_id = $comment_id;
		}
		else
		{
			$activity_id = bp_get_activity_id();
			$group_id = $bp->groups->current_group->id;
			$member_id = $bp->displayed_user->id;
						
			if(isset($activity_id) && $activity_id != "")
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $bp->current_component;
				$item_id = $post->ID;
				$activity_id = bp_get_activity_id();
				$secondary_item_id = $activity_id;
			}			
			else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
			{			
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $bp->current_component;
				$item_id = $post->ID;
				$secondary_item_id = $group_id;
			}
			else if(isset($member_id) && $member_id != "")
			{
				if(strtolower($bp->current_component) == "profile")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $member_id;
				}
				else if(strtolower($bp->current_component) == "messages")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $thread_template->message->id;
				}
			}
			else if(isset($topic_id) && $topic_id != "")
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = "forum";
				$item_id = $post->ID;
				$secondary_item_id = $post->ID;
			}
			else
			{
				// bp_get_member_user_id
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $post->post_title;
				$item_id = $post->ID;
				$member_id = get_current_user_id();
				$secondary_item_id = $member_id;
			}
		}	
	}
	else
	{
		if(bp_is_blog_page() && (!isset($topic_id) && $topic_id == ""))
		{
			$user_id = get_current_user_id();
			$component_name = "blog";
			$type = $post->post_type;
			$item_id = $post->ID;
			$secondary_item_id = $comment_id;
		}
		else
		{
			$activity_id = bp_get_activity_id();
			$group_id = $bp->groups->current_group->id;
			$member_id = $bp->displayed_user->id;
			if(isset($activity_id) && $activity_id != "")
			{				
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $bp->current_component;
				$item_id = $post->ID;
				$activity_id = bp_get_activity_id();
				$secondary_item_id = $activity_id;
			}			
			else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $bp->current_component;
				$item_id = $post->ID;
				$secondary_item_id = $group_id;
			}
			else if(isset($member_id) && $member_id != "")
			{
				if(strtolower($bp->current_component) == "profile")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $member_id;
				}
				else if(strtolower($bp->current_component) == "messages")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $thread_template->message->id;
				}
			}
			else if(isset($topic_id) && $topic_id != "")
			{
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = "forum";
				$item_id = $post->ID;
				$secondary_item_id = $post->ID;
			}
			else
			{
				// bp_get_member_user_id
				$user_id = get_current_user_id();
				$component_name = "buddypress";
				$type = $post->post_title;
				$item_id = $post->ID;
				$member_id = get_current_user_id();
				$secondary_item_id = $member_id;
			}
		}
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
		global $post,$bp,$thread_template;
		
		if($bp)
		{
			$check_url_for_topic = $bp->unfiltered_uri;
			if (in_array("topic", $check_url_for_topic))
			{
				$topic_id = 1;
			}
		}		
		
		if(bp_is_blog_page() && (!isset($topic_id) && $topic_id == ""))
		{
			$post_type = $post->post_type;
			$comment_id = get_comment_ID();
		}
		else
		{		
			$activity_id = bp_get_activity_id();
			$group_id = $bp->groups->current_group->id;
			$member_id = $bp->displayed_user->id;
			if(isset($activity_id) && $activity_id != "")
			{
				$post_type = $post->post_title;
				$comment_id = bp_get_activity_id();
			}
			else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
			{
				$post_type = $post->post_title;
				$comment_id = $bp->groups->current_group->id;
			}
			else if(isset($member_id) && $member_id != "")
			{								
				if(strtolower($bp->current_component) == "profile")
				{
					$post_type = $bp->current_component;
					$comment_id = $bp->displayed_user->id;
				}
				else if(strtolower($bp->current_component) == "messages")
				{
					$post_type = $bp->current_component;
					$comment_id = $thread_template->message->id;
				}
			}
			else if(isset($topic_id) && $topic_id != "")
			{                                                     
				$post_type = "forum";
				$comment_id = $post->ID;
			}			
			else
			{
				$post_type = $post->post_title;
				$comment_id = get_current_user_id();
			}			
		}

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
			if(bp_is_blog_page() && (!isset($topic_id) && $topic_id == ""))
			{
				$params = array('user_id' => $user_id, 'component' => 'blog','type' => $post_type,'action' => $type,'item_id' => $post_id,'secondary_item_id' => $comment_id);
			}
			else if(isset($topic_id) && $topic_id != "")
			{				
				$params = array('user_id' => $user_id, 'component' => 'buddypress','type' => 'forum','action' => $type,'item_id' => $post_id,'secondary_item_id' => $comment_id);
			}
			else
			{
				$params = array('user_id' => $user_id, 'component' => 'buddypress','type' => $post_type,'action' => $type,'item_id' => $post_id,'secondary_item_id' => $comment_id);

			}	
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
		global $post,$bp;
		
		if(bp_is_blog_page())
		{
			$post_type = $post->post_type;
			$secondary_item_id = $post_id;
		}
		else
		{
			$group_id = $bp->groups->current_group->id;
			if(isset($group_id) && $group_id != "")
			{
				$post_type = $post->post_title;
				$secondary_item_id = $group_id;
			}
		}

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
			if(bp_is_blog_page())
			{
				$params = array('user_id' => $user_id, 'component' => 'blog','type' => $post_type,'action' => $type,'item_id' => 0,'secondary_item_id' => $secondary_item_id);
			}
			else
			{
				$params = array('user_id' => $user_id, 'component' => 'buddypress','type' => $post_type,'action' => $type,'item_id' => 0,'secondary_item_id' => $secondary_item_id);

			}	
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
	global $wpdb, $bp;
	/*echo $user_id . "<br>";
	echo $component_name . "<br>";
	echo $type . "<br>";
	echo $item_id . "<br>";
	echo $secondary_item_id . "<br>";*/
	
	if($bp)
	{
		$check_url_for_topic = $bp->unfiltered_uri;
		if (in_array("topic", $check_url_for_topic))
		{
			$topic_id = 1;
		}
	}
		
	if(bp_is_blog_page())
	{
		$current_user = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM ask_votes WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
	}
	else
	{
		$activity_id = bp_get_activity_id();
		$group_id = $bp->groups->current_group->id;
		$member_id = $bp->displayed_user->id;
		
		if(isset($activity_id) && $activity_id != "")
		{
			$current_user = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM ask_votes WHERE user_id = $user_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
		}
		else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
		{
			$current_user = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM ask_votes WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
		}
		else if(isset($member_id) && $member_id != "")
		{
			$current_user = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM ask_votes WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
		}
		else if(isset($topic_id) && $topic_id != "")
		{
			$current_user = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM ask_votes WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
		}		
		else
		{
			$current_user = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM ask_votes WHERE user_id = $user_id AND item_id = 0 AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
		}
	
	}
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
add_filter('woocommerce_product_review_comment_form_args', 'voter_product_review');
add_action('bp_activity_entry_meta','voter_layout_bottom_area');
add_action('bp_after_profile_field_content','voter_layout_bottom_area');
add_action('bp_after_group_header','voter_layout_bottom_area');
add_action('bp_after_message_content','voter_layout_bottom_area');
//add_action('bp_group_forum_post_meta','voter_layout_bottom_area');
add_action('bbp_theme_before_reply_author_details','voter_layout_bottom_area');
?>