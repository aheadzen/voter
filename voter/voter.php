<?php
/*
    Plugin Name: Voter Plugin
    Plugin URI: http://www.ask-oracle.com
    Description: Through this plugin votes can be given to wordpress pages, post, custom post types, comments section, buddypress activity, groups, member profiles etc.
    Author: Tapan Sodagar
    Version: 1.0
    Author URI: http://www.ask-oracle.com
*/
include(dirname(__FILE__).'/class_frontend_voter.php');
include(dirname(__FILE__).'/class_backend_voter.php');
$frontend_voter = new frontendvoter();
$backend_voter = new backendvoter();
register_activation_hook(__FILE__, array($frontend_voter, 'voter_install'));
register_deactivation_hook(__FILE__, array($frontend_voter, 'voter_uninstall'));
add_action('admin_menu', array($backend_voter, 'voter_admin_menu'));
add_action('wp_enqueue_scripts', array($frontend_voter, 'voter_add_custom_scripts'));
add_filter('the_content', array($frontend_voter, 'voter_add_votes'));
add_filter('comment_reply_link', array($frontend_voter, 'voter_add_votes'));
add_action('init', array($frontend_voter, 'voter_function_giving_votes'));
add_action('bp_activity_entry_meta', array($frontend_voter, 'voter_add_votes'));
add_action('bp_after_profile_field_content', array($frontend_voter, 'voter_add_votes'));
add_action('bp_after_group_header', array($frontend_voter, 'voter_add_votes'));
add_action('bp_after_message_content', array($frontend_voter, 'voter_add_votes'));
add_action('bbp_theme_before_reply_author_details', array($frontend_voter, 'voter_add_votes'));
?>