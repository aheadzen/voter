<?php
class backendvoter
{
	const VOTER_OPTION_FOR_PAGES = "chk_vote_for_pages";
	const VOTER_OPTION_FOR_POST = "chk_vote_for_post";
	const VOTER_OPTION_FOR_POST_TYPE = "chk_vote_for_custom_post_type";
	const VOTER_OPTION_FOR_COMMENTS = "chk_vote_for_comments";
	const VOTER_OPTION_FOR_BP_ACTIVITY = "chk_vote_for_buddypress_activity";
	const VOTER_OPTION_FOR_BP_GROUP = "chk_vote_for_buddypress_groups";
	const VOTER_OPTION_FOR_BP_FORUM = "chk_vote_for_buddypress_forums";
	const VOTER_OPTION_FOR_BP_PROFILE = "chk_vote_for_buddypress_profile";
	const VOTER_OPTION_FOR_BP_MESSAGE = "chk_vote_for_messages";

	function voter_admin_menu()
	{
		add_submenu_page('options-general.php', 'VOTER Options', 'VOTER', 'manage_options', 'voter',array(&$this, 'voter_settings_page'));
	}
	function voter_settings_page()
	{
		global $bp,$post;
		include(dirname(__FILE__).'/admin.voter.settings.php');
	}	
}
?>