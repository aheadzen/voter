<?php
class frontendvoter extends backendvoter
{
	var $url;
	var $status = false;
	
	function voter_add_custom_scripts()
	{
		wp_enqueue_script('custom-voter-script', plugins_url('js/voter.js', __FILE__), array('jquery'));
		wp_enqueue_style('', plugins_url('css/voter.css', __FILE__));
	}
	function voter_install()
	{
        global $wpdb, $table_prefix;
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_prefix."ask_votes` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `user_id` bigint(20) NOT NULL,
			  `component` varchar(75) NOT NULL,
			  `type` varchar(75) NOT NULL,
			  `action` text NOT NULL,
			  `item_id` varchar(75) NOT NULL,
			  `secondary_item_id` varchar(75) DEFAULT NULL,
			  `date_recorded` datetime NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `user_id_2` (`user_id`,`component`,`type`,`item_id`,`secondary_item_id`),
			  KEY `date_recorded` (`date_recorded`),
			  KEY `user_id` (`user_id`),
			  KEY `item_id` (`item_id`),
			  KEY `secondary_item_id` (`secondary_item_id`),
			  KEY `component` (`component`),
			  KEY `type` (`type`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=103;";
				
        $wpdb->query($sql);
	}
	function voter_uninstall()
	{
        global $wpdb, $table_prefix;
        $sql = "DROP TABLE IF EXISTS `".$table_prefix."ask_votes`;";
        $wpdb->query($sql);
	}
	function voter_add_votes($content)
	{
		global $post, $bp, $thread_template, $bbP, $forum_id, $wpdb, $table_prefix;
		
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
		
		if($post_type == "page")
		{
			if(bp_is_blog_page() && (!isset($topic_id) && $topic_id == ""))
			{
				$user_id = get_current_user_id();
				$component_name = "blog";
				$type = $post->post_type;
				if(isset($comment_id) && $comment_id != "")
				{
					$item_id = $post->ID;
					$secondary_item_id = $comment_id;
					if(get_option(frontendvoter::VOTER_OPTION_FOR_COMMENTS) == "on")
					{
						$this->status = true;
					}
					
				}
				else
				{
					$item_id = 0;
					$secondary_item_id = $post->ID;
					if(get_option(frontendvoter::VOTER_OPTION_FOR_PAGES) == "on")
					{
						$this->status = true;
					}					
				}
				$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
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
					$type = "activity";
					$item_id = $post->ID;
					$activity_id = bp_get_activity_id();
					$secondary_item_id = $activity_id;
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_ACTIVITY) == "on")
					{
						$this->status = true;
					}
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
				}
				else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $group_id;					
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_GROUP) == "on")
					{
						$this->status = true;
					}
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
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
						if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_PROFILE) == "on")
						{
							$this->status = true;
						}
						$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
					}
					else if(strtolower($bp->current_component) == "messages")
					{
						$user_id = get_current_user_id();
						$component_name = "buddypress";
						$type = $bp->current_component;
						$item_id = $post->ID;
						$secondary_item_id = $thread_template->message->id;
						if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_MESSAGE) == "on")
						{
							$this->status = true;
						}
						$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
					}
				}
				else if(isset($topic_id) && $topic_id != "")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = "forum";
					$item_id = $post->ID;
					$secondary_item_id = $post->ID;
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_FORUM) == "on")
					{
						$this->status = true;
					}
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
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
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = 0 AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
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
				if(isset($comment_id) && $comment_id != "")
				{
					$item_id = $post->ID;
					$secondary_item_id = $comment_id;
					if(get_option(frontendvoter::VOTER_OPTION_FOR_COMMENTS) == "on")
					{
						$this->status = true;
					}					
				}
				else
				{
					$item_id = 0;
					$secondary_item_id = $post->ID;
					if(get_option(frontendvoter::VOTER_OPTION_FOR_PAGES) == "on")
					{
						$this->status = true;
					}
				}
				$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
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
					$type = "activity";
					$item_id = $post->ID;
					$activity_id = bp_get_activity_id();
					$secondary_item_id = $activity_id;
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_ACTIVITY) == "on")
					{
						$this->status = true;
					}
				}			
				else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
				{			
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $group_id;
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_GROUP) == "on")
					{
						$this->status = true;
					}					
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
						$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
						if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_PROFILE) == "on")
						{
							$this->status = true;
						}
					}
					else if(strtolower($bp->current_component) == "messages")
					{
						$user_id = get_current_user_id();
						$component_name = "buddypress";
						$type = $bp->current_component;
						$item_id = $post->ID;
						$secondary_item_id = $thread_template->message->id;
						$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
						if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_MESSAGE) == "on")
						{
							$this->status = true;
						}
					}
				}
				else if(isset($topic_id) && $topic_id != "")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = "forum";
					$item_id = $post->ID;
					$secondary_item_id = $post->ID;
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_FORUM) == "on")
					{
						$this->status = true;
					}
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
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = 0 AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
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

				if(isset($comment_id) && $comment_id != "")
				{
					$item_id = $post->ID;
					$secondary_item_id = $comment_id;
					if(get_option(frontendvoter::VOTER_OPTION_FOR_COMMENTS) == "on")
					{
						$this->status = true;
					}					
				}
				else
				{
					$item_id = 0;
					$secondary_item_id = $post->ID;
					if(get_option(frontendvoter::VOTER_OPTION_FOR_POST_TYPE) == "on")
					{
						$this->status = true;
					}
				}
				$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );				
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
					$type = "activity";
					$item_id = $post->ID;
					$activity_id = bp_get_activity_id();
					$secondary_item_id = $activity_id;
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_ACTIVITY) == "on")
					{
						$this->status = true;
					}					
				}			
				else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $group_id;
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_GROUP) == "on")
					{
						$this->status = true;
					}
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
						$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
						if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_PROFILE) == "on")
						{
							$this->status = true;
						}
					}
					else if(strtolower($bp->current_component) == "messages")
					{
						$user_id = get_current_user_id();
						$component_name = "buddypress";
						$type = $bp->current_component;
						$item_id = $post->ID;
						$secondary_item_id = $thread_template->message->id;
						$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
						if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_MESSAGE) == "on")
						{
							$this->status = true;
						}
					}
				}
				else if(isset($topic_id) && $topic_id != "")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = "forum";
					$item_id = $post->ID;
					$secondary_item_id = $post->ID;
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
					if(get_option(frontendvoter::VOTER_OPTION_FOR_BP_FORUM) == "on")
					{
						$this->status = true;
					}
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
					$current_user_votes = $wpdb->get_results("SELECT secondary_item_id,id,user_id,component,type,action,item_id FROM `".$table_prefix."ask_votes` WHERE user_id = $user_id AND item_id = 0 AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id" , OBJECT_K );
				}
			}			
		}
		
		if($this->status)
		{
			echo '<div class="vote alignright">';
				echo $this->voter_vote_link($post->ID,'up',$current_user_votes);
					echo '<span class="vote-count-post">';
					echo $this->voter_get_total_votes($post->ID);
					echo '</span>';
				echo $this->voter_vote_link($post->ID,'down',$current_user_votes);
			echo '</div>';
		}
		
		return $content;
	}
	function voter_vote_link($post_id,$type,$user_votes = false)
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
			$comment_id = get_comment_ID();
			if(isset($comment_id) && $comment_id != "")
			{
				$post_type = $post->post_type;
				$item_id = $post_id;
				$secondary_item_id = get_comment_ID();
			}
			else
			{
				$post_type = $post->post_type;
				$item_id = 0;
				$secondary_item_id = $post_id;
			}
		}
		else
		{
			$activity_id = bp_get_activity_id();
			$group_id = $bp->groups->current_group->id;
			$member_id = $bp->displayed_user->id;
						
			if(isset($activity_id) && $activity_id != "")
			{
				$post_type = "activity";
				$item_id = $post_id;
				$secondary_item_id = bp_get_activity_id();
			}
			else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
			{
				$post_type = $post->post_title;
				$item_id = $post_id;
				$secondary_item_id = $bp->groups->current_group->id;
			}
			else if(isset($member_id) && $member_id != "")
			{								
				if(strtolower($bp->current_component) == "profile")
				{
					$post_type = $bp->current_component;
					$item_id = $post_id;
					$secondary_item_id = $bp->displayed_user->id;
				}
				else if(strtolower($bp->current_component) == "messages")
				{
					$post_type = $bp->current_component;
					$item_id = $post_id;
					$secondary_item_id = $thread_template->message->id;
				}
			}
			else if(isset($topic_id) && $topic_id != "")
			{                                                     
				$post_type = "forum";
				$item_id = $post_id;
				$secondary_item_id = $post->ID;
			}			
			else
			{
				$post_type = $post->post_title;
				$item_id = $post_id;
				$secondary_item_id = get_current_user_id();
			}			
		}

		$vote_id = 0;
		if(!empty($user_votes[$secondary_item_id]))
			$vote_id = $user_votes[$secondary_item_id]->id;

		if($type == 'up')
		{
			$class = 'vote-up-off';
			if(!empty($user_votes[$secondary_item_id]) && $user_votes[$secondary_item_id]->action == 'up')
			{
				$class = 'vote-up-on';
			}
		}
		else if( $type == 'down' )
		{
			$class = 'vote-down-off';
			if(!empty($user_votes[$secondary_item_id]) && $user_votes[$secondary_item_id]->action == 'down')
			{
				$class = 'vote-down-on';
			}
		}
		
		if($vote_id == 0 && is_user_logged_in())
		{
			$user_id = get_current_user_id();
			if(bp_is_blog_page() && (!isset($topic_id) && $topic_id == ""))
			{
				$params = array('user_id' => $user_id, 'component' => 'blog','type' => $post_type,'action' => $type,'item_id' => $item_id,'secondary_item_id' => $secondary_item_id);
			}
			else if(isset($topic_id) && $topic_id != "")
			{				
				$params = array('user_id' => $user_id, 'component' => 'buddypress','type' => 'forum','action' => $type,'item_id' => $item_id,'secondary_item_id' => $secondary_item_id);
			}
			else
			{
				$params = array('user_id' => $user_id, 'component' => 'buddypress','type' => $post_type,'action' => $type,'item_id' => $item_id,'secondary_item_id' => $secondary_item_id);
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
	function voter_get_total_votes($post_id)
	{
		global $post, $bp, $thread_template, $bbP, $forum_id, $wpdb, $table_prefix;
		$post_type = $post->post_type;
		$post_id = $post->ID;		
		if($bp)
		{
			$check_url_for_topic = $bp->unfiltered_uri;
			if (in_array("topic", $check_url_for_topic))
			{
				$topic_id = 1;
			}
		}
		if($post_type == "page")
		{
			if(bp_is_blog_page() && (!isset($topic_id) && $topic_id == ""))
			{
				$user_id = get_current_user_id();
				$component_name = "blog";
				$type = $post->post_type;				
				
				$comment_id = get_comment_ID();
				if(isset($comment_id) && $comment_id != "")
				{
					$item_id = $post_id;
					$secondary_item_id = get_comment_ID();
				}
				else
				{
					$item_id = 0;
					$secondary_item_id = $post_id;
				}
								
				$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
				$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
					
				$total_records = $up_records->count - $down_records->count;
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
					$type = "activity";
					$item_id = $post->ID;
					$activity_id = bp_get_activity_id();
					$secondary_item_id = $activity_id;
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
					
					$total_records = $up_records->count - $down_records->count;
				}
				else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
				{
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $group_id;
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
															
					$total_records = $up_records->count - $down_records->count;
				}
				else if(isset($member_id) && $member_id != "")
				{
					if(strtolower($bp->current_component) == "profile")
					{
						$component_name = "buddypress";
						$type = $bp->current_component;
						$item_id = $post->ID;
						$secondary_item_id = $member_id;
											
						$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
						$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
											
						$total_records = $up_records->count - $down_records->count;
					}
					else if(strtolower($bp->current_component) == "messages")
					{
						$user_id = get_current_user_id();
						$component_name = "buddypress";
						$type = $bp->current_component;
						$item_id = $post->ID;
						$secondary_item_id = $thread_template->message->id;
						
						$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
						$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
											
						$total_records = $up_records->count - $down_records->count;
					}
				}
				else if(isset($topic_id) && $topic_id != "")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = "forum";
					$item_id = $post->ID;
					$secondary_item_id = $post->ID;
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");					
											
					$total_records = $up_records->count - $down_records->count;
					
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
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
																
					$total_records = $up_records->count - $down_records->count;
					
				}
			}
		}
		else if($post_type == "post" && (!isset($topic_id) && $topic_id == ""))
		{
			if(bp_is_blog_page())
			{
				$component_name = "blog";
				$type = $post->post_type;
				
				$comment_id = get_comment_ID();
				if(isset($comment_id) && $comment_id != "")
				{
					$item_id = $post_id;
					$secondary_item_id = get_comment_ID();
				}
				else
				{
					$item_id = 0;
					$secondary_item_id = $post_id;
				}				
				
				$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
				$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
				
				$total_records = $up_records->count - $down_records->count;
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
					$type = "activity";
					$item_id = $post->ID;
					$activity_id = bp_get_activity_id();
					$secondary_item_id = $activity_id;
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
					$total_records = $up_records->count - $down_records->count;					
				}
				else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
				{
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $group_id;
				
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM ask_votes WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
					$total_records = $up_records->count - $down_records->count;
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
					
						$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
						$down_records = $wpdb->get_row("SELECT count(*) as count FROM ask_votes WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
										
						$total_records = $up_records->count - $down_records->count;
					}
					else if(strtolower($bp->current_component) == "messages")
					{
						$user_id = get_current_user_id();
						$component_name = "buddypress";
						$type = $bp->current_component;
						$item_id = $post->ID;
						$secondary_item_id = $thread_template->message->id;
	
						$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
						$down_records = $wpdb->get_row("SELECT count(*) as count FROM ask_votes WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
						$total_records = $up_records->count - $down_records->count;					
					}
				}
				else if(isset($topic_id) && $topic_id != "")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = "forum";
					$item_id = $post->ID;
					$secondary_item_id = $post->ID;
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");														
					$total_records = $up_records->count - $down_records->count;				
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
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
											
					$total_records = $up_records->count - $down_records->count;
				}				
			}
		}
		else
		{
			if(bp_is_blog_page() && (!isset($topic_id) && $topic_id == ""))
			{
				$component_name = "blog";
				$type = $post->post_type;
				
				$comment_id = get_comment_ID();
				if(isset($comment_id) && $comment_id != "")
				{
					$item_id = $post_id;
					$secondary_item_id = get_comment_ID();
				}
				else
				{
					$item_id = 0;
					$secondary_item_id = $post_id;
				}
				
				$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
				$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
				
				$total_records = $up_records->count - $down_records->count;
			}
			else
			{
				$activity_id = bp_get_activity_id();
				$group_id = $bp->groups->current_group->id;
				$member_id = $bp->displayed_user->id;
				
				if(isset($activity_id) && $activity_id != "")
				{
					$component_name = "buddypress";
					$type = "activity";
					$item_id = $post->ID;
					$activity_id = bp_get_activity_id();
					$secondary_item_id = $activity_id;
				
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
					
					$total_records = $up_records->count - $down_records->count;				
				}
				else if(isset($group_id) && $group_id != "" && (!isset($topic_id) && $topic_id == ""))
				{
					$component_name = "buddypress";
					$type = $bp->current_component;
					$item_id = $post->ID;
					$secondary_item_id = $group_id;
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
					
					$total_records = $up_records->count - $down_records->count;				
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
						
						$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
						$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
						
						$total_records = $up_records->count - $down_records->count;
					}
					else if(strtolower($bp->current_component) == "messages")
					{
						$user_id = get_current_user_id();
						$component_name = "buddypress";
						$type = $bp->current_component;
						$item_id = $post->ID;
						$secondary_item_id = $thread_template->message->id;
						
						$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
						$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");						
											
						$total_records = $up_records->count - $down_records->count;					
					}					
				}
				else if(isset($topic_id) && $topic_id != "")
				{
					$user_id = get_current_user_id();
					$component_name = "buddypress";
					$type = "forum";
					$item_id = $post->ID;
					$secondary_item_id = $post->ID;
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
											
					$total_records = $up_records->count - $down_records->count;					
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
					
					$up_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'up'");
					$down_records = $wpdb->get_row("SELECT count(*) as count FROM `".$table_prefix."ask_votes` WHERE item_id = $item_id AND component = '".$component_name."' AND type = '".$type."' AND secondary_item_id = $secondary_item_id AND action = 'down'");
											
					$total_records = $up_records->count - $down_records->count;
					
				}
			}
		}
		return $total_records;
	}
	function voter_function_giving_votes()
	{
		global $wpdb, $table_prefix;
		if(isset($_REQUEST) && !empty($_REQUEST))
		{
			if(isset($_REQUEST['user_id']) && isset($_REQUEST['component']) && isset($_REQUEST['type']) && isset($_REQUEST['action']) &&  isset($_REQUEST['item_id']) && isset($_REQUEST['secondary_item_id']))
			{
				$q = $wpdb->query("INSERT INTO `".$table_prefix."ask_votes` (user_id, component, type, action, date_recorded, item_id, secondary_item_id) VALUES ('".$_REQUEST['user_id']."', '".$_REQUEST['component']."', '".$_REQUEST['type']."', '".$_REQUEST['action']."', '".date("Y/m/d")."', '".$_REQUEST['item_id']."', '".$_REQUEST['secondary_item_id']."')");
				if(!($q))
					return false;
			}
		}
	}
}
?>