<div class="wrap">
	<?php screen_icon('themes'); ?><h2>Voter Settings</h2>
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options') ?>
		<table class="form-table">
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_PAGES;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_PAGES;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_PAGES) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Pages</td>
					</tr>
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_POST;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_POST;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_POST) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Post</td>
					</tr>
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_POST_TYPE;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_POST_TYPE;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_POST_TYPE) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Custom Post Type</td>
					</tr>
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_COMMENTS;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_COMMENTS;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_COMMENTS) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Comments</td>
					</tr>
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_BP_ACTIVITY;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_BP_ACTIVITY;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_BP_ACTIVITY) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Buddypress Activity</td>
					</tr>
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_BP_GROUP;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_BP_GROUP;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_BP_GROUP) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Buddypress Groups</td>
					</tr>
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_BP_FORUM;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_BP_FORUM;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_BP_FORUM) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Buddypress Forums</td>
					</tr>
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_BP_PROFILE;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_BP_PROFILE;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_BP_PROFILE) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Buddypress Profile</td>
					</tr>
					<tr valign="top">
						<td><input type="checkbox" id="<?php echo backendvoter::VOTER_OPTION_FOR_BP_MESSAGE;?>" name="<?php echo backendvoter::VOTER_OPTION_FOR_BP_MESSAGE;?>" <?php if(get_option(backendvoter::VOTER_OPTION_FOR_BP_MESSAGE) == "on"){echo "checked=checked";}?>/>&nbsp;&nbsp;&nbsp;Vote For Buddypress Messages</td>
					</tr>
					<tr valign="top">
						<td>
							<?php
								$value = "" . backendvoter::VOTER_OPTION_FOR_PAGES . "," . backendvoter::VOTER_OPTION_FOR_POST . "," . backendvoter::VOTER_OPTION_FOR_POST_TYPE . "," . backendvoter::VOTER_OPTION_FOR_COMMENTS . "," . backendvoter::VOTER_OPTION_FOR_BP_ACTIVITY . "," . backendvoter::VOTER_OPTION_FOR_BP_GROUP . "," . backendvoter::VOTER_OPTION_FOR_BP_FORUM . "," . backendvoter::VOTER_OPTION_FOR_BP_PROFILE . "," . backendvoter::VOTER_OPTION_FOR_BP_MESSAGE . "";
							?>
							<input type="hidden" name="page_options" value="<?php echo $value;?>" />
							<input type="hidden" name="action" value="update" />
							<input type="submit" value="Save settings" class="button-primary"/>
						</td>
					</tr>					
				</table>
			</form>
<?php
	// Check that the user is allowed to update options  
		if (!current_user_can('manage_options'))
		{
			wp_die('You do not have sufficient permissions to access this page.');
		}
?>
		</div>