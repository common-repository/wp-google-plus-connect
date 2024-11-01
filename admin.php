<?php
//admin menu
add_action('admin_menu', 'wds_google_connect_menu');
function wds_google_connect_menu(){
        add_options_page(__('Google+ Connect', 'wp-google-plus'), __('Google+ Connect', 'wp-google-plus'), 'manage_options', 'google-plus-connect-options', 'wds_google_connect_settings_page');
		add_action( 'admin_init', 'wds_google_connect_register_settings' );
}

//register settings
function wds_google_connect_register_settings() {
	//API
	register_setting( 'wds_google_connect_settings_group', 'wds_google_connect_app_name' );
	register_setting( 'wds_google_connect_settings_group', 'wds_google_connect_client_id' );
	register_setting( 'wds_google_connect_settings_group', 'wds_google_connect_client_secret' );
	register_setting( 'wds_google_connect_settings_group', 'wds_google_connect_redirect_url' );
	register_setting( 'wds_google_connect_settings_group', 'wds_google_connect_developer_key' );
	//Badge
	register_setting( 'wds_google_connect_settings_badge', 'wds_google_connect_badge_page_id' );
	register_setting( 'wds_google_connect_settings_badge', 'wds_google_connect_badge_name' );
	register_setting( 'wds_google_connect_settings_badge', 'wds_google_connect_badge_type' );
	register_setting( 'wds_google_connect_settings_badge', 'wds_google_connect_badge_lang' );
	//General
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_import_author' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_sync_schedule' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_post_type' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_post_status' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_category' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_tags' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_hash_tags' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_hash_tags_2_tags' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_wp_comments' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_show_glink' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_gcomments' );
	register_setting( 'wds_google_connect_settings_general', 'wds_google_connect_ghost_users' );
	//buddypress
	register_setting( 'wds_google_connect_settings_bp', 'wds_google_connect_bp_login' );
	register_setting( 'wds_google_connect_settings_bp', 'wds_google_connect_bp_profile' );
	register_setting( 'wds_google_connect_settings_bp', 'wds_google_connect_bp_registration' );
	register_setting( 'wds_google_connect_settings_bp', 'wds_google_connect_bp_required' );
	register_setting( 'wds_google_connect_settings_bp', 'wds_google_connect_bp_activity' );
	register_setting( 'wds_google_connect_settings_bp', 'wds_google_connect_bp_activity_comments' );
	register_setting( 'wds_google_connect_settings_bp', 'wds_google_connect_bp_ghost_users' );
}

//admin options page
function wds_google_connect_settings_page(){
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br /></div>
		<h2>Google+ Connect Settings</h2>
        <br />
		<?php $view=$_GET['view'];
		if(!$view){
			$view="badge";
		}?>
			<a <?php if($view=="badge"){echo "style='font-weight:bold;'";}?> href="<?php echo add_query_arg( array ( 'view' => 'badge' ) );?>">Direct Connect & Badge Settings</a> |
            <a <?php if($view=="api"){echo "style='font-weight:bold;'";}?> href="<?php echo add_query_arg( array ( 'view' => 'api' ) );?>">Google+ API Configuration</a><!-- |
            <a <?php if($view=="general"){echo "style='font-weight:bold;'";}?> href="<?php echo add_query_arg( array ( 'view' => 'general' ) );?>">General Plugin Settings</a>
            <?php if(function_exists('wds_bp_check')){ ?> |
            	<a <?php if($view=="bp"){echo "style='font-weight:bold;'";}?> href="<?php echo add_query_arg( array ( 'view' => 'bp' ) );?>">BuddyPress Settings</a>-->
            <?php } ?>
			<form method="post" action="options.php">
              <table class="form-table">
              <?php if($view=="api"){//API settings
			  	  $wds_google_connect_app_name=esc_attr(get_option('wds_google_connect_app_name'));
				  if(!$wds_google_connect_app_name){
					  $wds_google_connect_app_name=get_bloginfo('name');
				  }
				  $wds_google_connect_client_id=esc_attr(get_option('wds_google_connect_client_id'));
				  $wds_google_connect_client_secret=esc_attr(get_option('wds_google_connect_client_secret'));
				  $wds_google_connect_redirect_url=esc_attr(get_option('wds_google_connect_redirect_url'));
				  if(!$wds_google_connect_redirect_url){
					  $wds_google_connect_redirect_url=site_url();
				  }
				  $wds_google_connect_developer_key=esc_attr(get_option('wds_google_connect_developer_key'));
				  settings_fields( 'wds_google_connect_settings_group' );?> 
                  <tr>
                  	<th colspan="2">
                    	<ul>
                          <li>Visit the <a rel="nofollow" target="_blank" href="https://code.google.com/apis/console/?api=plus">Google API Console</a> to generate your developer key, OAuth2 client id, OAuth2 client secret, and register your OAuth2 redirect uri. </li>
                          <li>Click on "Services" in the left column and turn on "Google+ API".</li>
                          <li>Click on "API Access" in the left column and click the button labeled "Create an OAuth2 client ID" </li>
                          <li>Give your application a name and click "Next" </li>
                          <li>Select "Web Application" as the "Application type" </li>
                          <li>Click on (more options and enter <?php echo site_url();?> into each textarea box)</li>
                          <li>Click "Create client ID" </li>
                          <li>Fill in the fields below with the generated information</li>
                        </ul>
                    </th>
                  </tr>
                  <tr valign="top">
                      <th>Product Name:</th>
                      <td><input type="text" name="wds_google_connect_app_name" value="<?php echo $wds_google_connect_app_name; ?>" size="50" /></td>
                  </tr>
                  <tr valign="top">
                      <th>Client ID:</th>
                      <td><input type="text" name="wds_google_connect_client_id" value="<?php echo $wds_google_connect_client_id; ?>" size="50" /></td>
                  </tr>
                  <tr valign="top">
                      <th>Client Secret:</th>
                      <td><input type="text" name="wds_google_connect_client_secret" value="<?php echo $wds_google_connect_client_secret; ?>" size="50" /></td>
                  </tr>
                  <tr valign="top">
                      <th>Redirect URI:</th>
                      <td><input type="text" name="wds_google_connect_redirect_url" value="<?php echo $wds_google_connect_redirect_url; ?>" size="50" /></td>
                  </tr>
                  <tr valign="top">
                      <th>API Key:</th>
                      <td><input type="text" name="wds_google_connect_developer_key" value="<?php echo $wds_google_connect_developer_key; ?>" size="50" /></td>
                  </tr>
              <?php }elseif($view=="badge"){ //Badge Settings
			  	  $wds_google_connect_badge_page_id=esc_attr(get_option('wds_google_connect_badge_page_id'));
				  $wds_google_connect_badge_name=esc_attr(get_option('wds_google_connect_badge_name'));
			  	  $wds_google_connect_badge_type=esc_attr(get_option('wds_google_connect_badge_type'));
				  $wds_google_connect_badge_lang=esc_attr(get_option('wds_google_connect_badge_lang'));
				  settings_fields( 'wds_google_connect_settings_badge' );?>
                  <tr>
                  	<td colspan="2">
                    Google+ Direct Connect helps visitors find your Google+ page and add it to their circles from<br />directly within Google Search. Once you've created your Google+ page, finish connecting it to<br />your site by adding the following code inside the &lt;head&gt; element of your site:
                    </td>
                  </tr>
                  <tr valign="top">
                      <th>Link to your Google+ page:</th>
                      <td>https://plus.google.com/<input type="text" name="wds_google_connect_badge_page_id" value="<?php echo $wds_google_connect_badge_page_id; ?>" size="50" /></td>
                  </tr>
                  <!--<tr valign="top">
                      <th>Customize name:</th>
                      <td><input type="text" name="wds_google_connect_badge_name" value="<?php echo $wds_google_connect_badge_name; ?>" size="75" /></td>
                  </tr>-->
                  <tr>
                  	<td colspan="2">
                    The Google+ badge allows visitors to directly connect with and promote your brand on Google+
                    </td>
                  </tr>
                  <tr valign="top">
                      <th>Badge Style:</th>
                      <td>
                        <select name="wds_google_connect_badge_type" onchange="form.submit();">
                        	<option value="">No Badge
                            <option value="badge" <?php if($wds_google_connect_badge_type=="badge"){echo "selected";}?>>Standard Badge
                            <option value="smallbadge" <?php if($wds_google_connect_badge_type=="smallbadge"){echo "selected";}?>>Small Badge
                            <option value="16" <?php if($wds_google_connect_badge_type=="16"){echo "selected";}?>>Small Icon
                            <option value="32" <?php if($wds_google_connect_badge_type=="32"){echo "selected";}?>>Medium Icon
                            <option value="64" <?php if($wds_google_connect_badge_type=="64"){echo "selected";}?>>Large Icon
                        </select>
                      </td>
                  </tr>
                  <tr valign="top">
                      <th>Badge Language:</th>
                      <td>
                        <select name="wds_google_connect_badge_lang" onchange="form.submit();">
                        	<option value="en-US" selected="selected" <?php if ($wds_google_connect_badge_lang == "en-US"){ echo "selected";}?> >English (US)
                            <option value="en-GB" <?php if ($wds_google_connect_badge_lang == "en-GB"){ echo "selected";}?> >English (UK)
                            <option value="ar" <?php if ($wds_google_connect_badge_lang == "ar"){ echo "selected";}?> >Arabic
                            <option value="bg" <?php if ($wds_google_connect_badge_lang == "bg"){ echo "selected";}?> >Bulgarian
                            <option value="ca" <?php if ($wds_google_connect_badge_lang == "ca"){ echo "selected";}?> >Catalan
                            <option value="zh-CN" <?php if ($wds_google_connect_badge_lang == "zh-CN"){ echo "selected";}?> >Chinese (Simplified)
                            <option value="zh-TW" <?php if ($wds_google_connect_badge_lang == "zh-TW"){ echo "selected";}?> >Chinese (Traditional)
                            <option value="hr" <?php if ($wds_google_connect_badge_lang == "hr"){ echo "selected";}?> >Croatian
                            <option value="cs" <?php if ($wds_google_connect_badge_lang == "cs"){ echo "selected";}?> >Czech
                            <option value="da" <?php if ($wds_google_connect_badge_lang == "da"){ echo "selected";}?> >Danish
                            <option value="nl" <?php if ($wds_google_connect_badge_lang == "nl"){ echo "selected";}?> >Dutch
                            <option value="et" <?php if ($wds_google_connect_badge_lang == "et"){ echo "selected";}?> >Estonian
                            <option value="fil" <?php if ($wds_google_connect_badge_lang == "fil"){ echo "selected";}?> >Filipino
                            <option value="fi" <?php if ($wds_google_connect_badge_lang == "fi"){ echo "selected";}?> >Finnish
                            <option value="fr" <?php if ($wds_google_connect_badge_lang == "fr"){ echo "selected";}?> >French
                            <option value="de" <?php if ($wds_google_connect_badge_lang == "de"){ echo "selected";}?> >German
                            <option value="el" <?php if ($wds_google_connect_badge_lang == "el"){ echo "selected";}?> >Greek
                            <option value="iw" <?php if ($wds_google_connect_badge_lang == "iw"){ echo "selected";}?> >Hebrew
                            <option value="hi" <?php if ($wds_google_connect_badge_lang == "hi"){ echo "selected";}?> >Hindi
                            <option value="hu" <?php if ($wds_google_connect_badge_lang == "hu"){ echo "selected";}?> >Hungarian
                            <option value="id" <?php if ($wds_google_connect_badge_lang == "id"){ echo "selected";}?> >Indonesian
                            <option value="it" <?php if ($wds_google_connect_badge_lang == "it"){ echo "selected";}?> >Italian
                            <option value="ja" <?php if ($wds_google_connect_badge_lang == "ja"){ echo "selected";}?> >Japanese
                            <option value="ko" <?php if ($wds_google_connect_badge_lang == "ko"){ echo "selected";}?> >Korean
                            <option value="lv" <?php if ($wds_google_connect_badge_lang == "lv"){ echo "selected";}?> >Latvian
                            <option value="lt" <?php if ($wds_google_connect_badge_lang == "lt"){ echo "selected";}?> >Lithuanian
                            <option value="ms" <?php if ($wds_google_connect_badge_lang == "ms"){ echo "selected";}?> >Malay
                            <option value="no" <?php if ($wds_google_connect_badge_lang == "no"){ echo "selected";}?> >Norwegian
                            <option value="fa" <?php if ($wds_google_connect_badge_lang == "fa"){ echo "selected";}?> >Persian
                            <option value="pl" <?php if ($wds_google_connect_badge_lang == "pl"){ echo "selected";}?> >Polish
                            <option value="pt-BR" <?php if ($wds_google_connect_badge_lang == "pt-BR"){ echo "selected";}?> >Portuguese (Brazil)
                            <option value="pt-PT" <?php if ($wds_google_connect_badge_lang == "pt-PT"){ echo "selected";}?> >Portuguese (Portugal)
                            <option value="ro" <?php if ($wds_google_connect_badge_lang == "ro"){ echo "selected";}?> >Romanian
                            <option value="ru" <?php if ($wds_google_connect_badge_lang == "ru"){ echo "selected";}?> >Russian
                            <option value="sr" <?php if ($wds_google_connect_badge_lang == "sr"){ echo "selected";}?> >Serbian
                            <option value="sv" <?php if ($wds_google_connect_badge_lang == "sv"){ echo "selected";}?> >Swedish
                            <option value="sk" <?php if ($wds_google_connect_badge_lang == "sk"){ echo "selected";}?> >Slovak
                            <option value="sl" <?php if ($wds_google_connect_badge_lang == "sl"){ echo "selected";}?> >Slovenian
                            <option value="es" <?php if ($wds_google_connect_badge_lang == "es"){ echo "selected";}?> >Spanish
                            <option value="es-419" <?php if ($wds_google_connect_badge_lang == "es-419"){ echo "selected";}?> >Spanish (Latin America)
                            <option value="th" <?php if ($wds_google_connect_badge_lang == "th"){ echo "selected";}?> >Thai
                            <option value="tr" <?php if ($wds_google_connect_badge_lang == "tr"){ echo "selected";}?> >Turkish
                            <option value="uk" <?php if ($wds_google_connect_badge_lang == "uk"){ echo "selected";}?> >Ukrainian
                            <option value="vi" <?php if ($wds_google_connect_badge_lang == "vi"){ echo "selected";}?> >Vietnamese
                        </select>
                      </td>
                  </tr>
			  	  <tr>
                  	<td colspan="2">
                    	<?php wp_gplus_badge();
						if($wds_google_connect_badge_type){?>
                          <br />
                          <strong>Badge Short Code:</strong> [gplus_badge]<br />
                          <strong>PHP Function:</strong> &lt;?php wp_gplus_badge();?&gt;<br /><br />
                        <?php } ?>
                        
                    </td>
                  </tr>
			  <?php }elseif($view=="general"){ //General Settings
			  	  $wds_google_connect_import_author=esc_attr(get_option('wds_google_connect_import_author'));
			  	  $wds_google_connect_sync_schedule=esc_attr(get_option('wds_google_connect_sync_schedule'));
				  $wds_google_connect_post_type=esc_attr(get_option('wds_google_connect_post_type'));
			  	  $wds_google_connect_post_status=esc_attr(get_option('wds_google_connect_post_status'));
			  	  $wds_google_connect_category=esc_attr(get_option('wds_google_connect_category'));
			  	  $wds_google_connect_tags=esc_attr(get_option('wds_google_connect_tags'));
			  	  $wds_google_connect_hash_tags=esc_attr(get_option('wds_google_connect_hash_tags'));
			  	  $wds_google_connect_hash_tags_2_tags=esc_attr(get_option('wds_google_connect_hash_tags_2_tags'));
			  	  if($wds_google_connect_hash_tags_2_tags){
					  $wds_google_connect_hash_tags_2_tags="checked";
				  }
				  $wds_google_connect_wp_comments=esc_attr(get_option('wds_google_connect_wp_comments'));
			  	  if($wds_google_connect_wp_comments){
					  $wds_google_connect_wp_comments="checked";
				  }
				  $wds_google_connect_show_glink=esc_attr(get_option('wds_google_connect_show_glink'));
			  	  if($wds_google_connect_show_glink){
					  $wds_google_connect_show_glink="checked";
				  }
				  $wds_google_connect_gcomments=esc_attr(get_option('wds_google_connect_gcomments'));
			  	  if($wds_google_connect_gcomments){
					  $wds_google_connect_gcomments="checked";
				  }
				  $wds_google_connect_ghost_users=esc_attr(get_option('wds_google_connect_ghost_users'));
			  	  if($wds_google_connect_ghost_users){
					  $wds_google_connect_ghost_users="checked";
				  }
				  settings_fields( 'wds_google_connect_settings_general' );
				  global $wpdb;?>
              	  <tr valign="top">
                      <th>Assign which Google+ connected Author(s) can feed posts:</th>
                      <td>
                      <div style="">
                      		<?php
							//$users=get_users(array('meta_key' => 'wds_google_connect_user_id'));
							//print_r($users);
							$sql="select a.ID,a.display_name,b.meta_value FROM $wpdb->users a, $wpdb->usermeta b where a.ID=b.user_id and b.meta_key='wds_google_connect_photo'";
							$users = $wpdb->get_results($sql, OBJECT);
							foreach ($users as $user) {
        						echo '<input type="checkbox" name="wds_google_connect_import_author" value="'.$user->ID.'">
								<img width="40px;" src="'.$user->meta_value.'">
								'.$user->display_name.'
                                <br>';
    						}
							?>
                      </div>
                      <input type="text" name="wds_google_connect_import_author" value="<?php echo $wds_google_connect_import_author; ?>" size="75" /></td>
                  </tr>
                  <tr valign="top">
                      <th>Google+ stream sync schedule:</th>
                      <td>
                        <select name="wds_google_connect_sync_schedule">
                        	<option value="manual" <?php if($wds_google_connect_sync_schedule=="manual"){echo "selected";}?>>Manual
                            <option value="real" <?php if($wds_google_connect_sync_schedule=="real"){echo "selected";}?>>Real Time
                            <option value="5min" <?php if($wds_google_connect_sync_schedule=="5min"){echo "selected";}?>>5 Minutes
                            <option value="15min" <?php if($wds_google_connect_sync_schedule=="15min" || $wds_google_connect_sync_schedule==""){echo "selected";}?>>15 Minutes
                            <option value="30min" <?php if($wds_google_connect_sync_schedule=="30min"){echo "selected";}?>>30 Minutes
                            <option value="1hr" <?php if($wds_google_connect_sync_schedule=="1hr"){echo "selected";}?>>1 Hour
                            <option value="6hrs" <?php if($wds_google_connect_sync_schedule=="6hrs"){echo "selected";}?>>6 Hours
                            <option value="12hrs" <?php if($wds_google_connect_sync_schedule=="12hrs"){echo "selected";}?>>12 Hours
                        </select>
                      </td>
                  </tr>
                  <tr valign="top">
                      <th>Post Type:</th>
                      <td>
                      	<select id='wds_google_connect_post_type' name='wds_google_connect_post_type'>
                          <?php
							  $post_types	= get_post_types( array( 'public' => true, 'show_ui' => true ), 'objects' );
							  foreach ( $post_types as $post_type => $pt ) {
								  $selected = ""; if ($wds_google_connect_post_type == esc_attr( $pt->name ) || $wds_google_connect_post_type == "" && esc_attr( $pt->name )=="post") $selected = " selected";
								  echo '<option value="'. esc_attr( $pt->name ).'" '.$selected.'>'.$pt->labels->singular_name;
							  }
						  ?>
                        </select>
                      </td>
                  </tr>
                  <tr valign="top">
                      <th>Post Status:</th>
                      <td>
                      	<select id='wds_google_connect_post_status' name='wds_google_connect_post_status'>
                          <option value="publish" <?php if($wds_google_connect_post_status=="publish"){echo "selected";}?>>Publish
                          <option value="pending" <?php if($wds_google_connect_post_status=="pending"){echo "selected";}?>>Pending
                          <option value="private" <?php if($wds_google_connect_post_status=="private"){echo "selected";}?>>Private
                          <option value="draft" <?php if($wds_google_connect_post_status=="draft"){echo "selected";}?>>Draft
                        </select>
                      </td>
                  </tr>
                  <tr valign="top">
                      <th>Assign to Category:</th>
                      <td><input type="text" name="wds_google_connect_category" value="<?php echo $wds_google_connect_category; ?>" size="75" /></td>
                  </tr>
                  <tr valign="top">
                      <th>Default Tags (separated by commas):</th>
                      <td><input type="text" name="wds_google_connect_tags" value="<?php echo $wds_google_connect_tags; ?>" size="75" /></td>
                  </tr>
                  <tr valign="top">
                      <th>Import Hashtags (separated by commas):</th>
                      <td><input type="text" name="wds_google_connect_hash_tags" value="<?php echo $wds_google_connect_hash_tags; ?>" size="75" /></td>
                  </tr>
                  <tr valign="top">
                      <td colspan="2"><input type="checkbox" name="wds_google_connect_hash_tags_2_tags" value="1" <?php echo $wds_google_connect_hash_tags_2_tags;?>  />
                      Save hashtags from Google+ posts as WordPress tags</td>
                  </tr>
                  <tr valign="top">
                      <td colspan="2"><input type="checkbox" name="wds_google_connect_wp_comments" value="1" <?php echo $wds_google_connect_wp_comments;?>  />
                      Enable comments on imported Google+ posts</td>
                  </tr>
                  <tr valign="top">
                      <td colspan="2"><input type="checkbox" name="wds_google_connect_show_glink" value="1" <?php echo $wds_google_connect_show_glink;?>  />
                      Display orginal Google+ link</td>
                  </tr>
                  <tr valign="top">
                      <td colspan="2"><input type="checkbox" name="wds_google_connect_gcomments" value="1" <?php echo $wds_google_connect_gcomments;?>  />
                      Import Google+ post comments & plus 1s from imported Google+ posts</td>
                  </tr>
                  <tr valign="top">
                      <td colspan="2"><input type="checkbox" name="wds_google_connect_ghost_users" value="1" <?php echo $wds_google_connect_ghost_users;?>  />
                      Create ghost users from Google+ post comments (ghost users can claim their accounts and opt out of being on your website)</td>
                  </tr>
              <?php }elseif($view=="bp"){ //BuddyPress Settings
			  	  $wds_google_connect_bp_login=esc_attr(get_option('wds_google_connect_bp_login'));
				  if($wds_google_connect_bp_login){
					  $wds_google_connect_bp_login="checked";
				  }
				  $wds_google_connect_bp_profile=esc_attr(get_option('wds_google_connect_bp_profile'));
				  if($wds_google_connect_bp_profile){
					  $wds_google_connect_bp_profile="checked";
				  }
				  $wds_google_connect_bp_registration=esc_attr(get_option('wds_google_connect_bp_registration'));
				  if($wds_google_connect_bp_registration){
					  $wds_google_connect_bp_registration="checked";
				  }
				  $wds_google_connect_bp_required=esc_attr(get_option('wds_google_connect_bp_required'));
				  if($wds_google_connect_bp_required){
					  $wds_google_connect_bp_required="checked";
				  }
				  $wds_google_connect_bp_activity=esc_attr(get_option('wds_google_connect_bp_activity'));
				  if($wds_google_connect_bp_activity){
					  $wds_google_connect_bp_activity="checked";
				  }
				  $wds_google_connect_bp_activity_comments=esc_attr(get_option('wds_google_connect_bp_activity_comments'));
				  if($wds_google_connect_bp_activity_comments){
					  $wds_google_connect_bp_activity_comments="checked";
				  }
				  $wds_google_connect_bp_ghost_users=esc_attr(get_option('wds_google_connect_bp_ghost_users'));
				  if($wds_google_connect_bp_ghost_users){
					  $wds_google_connect_bp_ghost_users="checked";
				  }
				  settings_fields( 'wds_google_connect_settings_bp' );?>
                  <tr valign="top">
                      <td>
                      <input type="checkbox" name="wds_google_connect_bp_login" value="1" <?php echo $wds_google_connect_bp_login;?>  />
                      Add Google+ login button to BuddyPress sidebar login
                      </td>
                  </tr>
                  <tr valign="top">
                      <td>
                      <input type="checkbox" name="wds_google_connect_bp_profile" value="1" <?php echo $wds_google_connect_bp_profile;?>  />
                      Add Google+ login button to logged in members profile
                      </td>
                  </tr>
                  <tr valign="top">
                      <td>
                      <input type="checkbox" name="wds_google_connect_bp_registration" value="1" <?php echo $wds_google_connect_bp_registration;?>  />
                      Add Google+ login button to BuddyPress registration page
                      </td>
                  </tr>
                  <tr valign="top">
                      <td>
                      <input type="checkbox" name="wds_google_connect_bp_required" value="1" <?php echo $wds_google_connect_bp_required;?>  />
                      Require Google+ connected members to fill out required BuddyPress profile fields
                      </td>
                  </tr>
                  <tr valign="top">
                      <td>
                      <input type="checkbox" name="wds_google_connect_bp_activity" value="1" <?php echo $wds_google_connect_bp_activity;?>  />
                      Allow members to import their Google+ activity as BuddyPress activity
                      </td>
                  </tr>
                  <tr valign="top">
                      <td>
                      <input type="checkbox" name="wds_google_connect_bp_activity_comments" value="1" <?php echo $wds_google_connect_bp_activity_comments;?>  />
                      Allow members to import their Google+ post comments into BuddyPress activity comments
                      </td>
                  </tr>
                  <tr valign="top">
                      <td>
                      <input type="checkbox" name="wds_google_connect_bp_ghost_users" value="1" <?php echo $wds_google_connect_bp_ghost_users;?>  />
                      Allow members to create ghost accounts from their Google+ post commenters (ghost users can claim their accounts and opt out of being on your website)
                      </td>
                  </tr>
              <?php } ?>
              </table>
              <p class="submit">
              <input type="submit" class="button-primary" value="Save Settings" />
              </p>
          </form>
          
		
        <?php if($view=="api"){
			if($wds_google_connect_app_name && $wds_google_connect_client_id && $wds_google_connect_client_secret && $wds_google_connect_redirect_url && $wds_google_connect_developer_key){
				if (!isset($_SESSION['token'])) {?>
        			Test your settings by clicking on the Google+ login button below:
            		<?php wp_gplus_login_button();?>
            	<?php }else{ ?>
                	You are connected!
                <?php }?>
            	<strong>Button Short Code:</strong> [gplus_button]<br />
            	<strong>PHP Function:</strong> &lt;?php wp_gplus_login_button();?&gt;<br /><br />
        <?php } 
		}?>
        
    	<hr />
        <h3>For support please visit the <a target="_blank" href="http://plugins.webdevstudios.com/support/forum/wordpress-google-plus-connect/">WP Google+ Connect Plugin Support Forum</a></h3>
		<table border=0>
        <tr>
        	<td><a target="_blank" href="http://webdevstudios.com"><img width="50" src="http://webdevstudios.com/wp-content/uploads/2011/01/WDS1-150x150.png" /></a></td>
            <td><strong>Follow WebDevStudios!</strong><br />
            	<a target="_blank" href="https://plus.google.com/108871619014334838112"><img src="http://webdevstudios.com/wp-content/uploads/2011/11/google.png" /></a>
                <a target="_blank" href="http://twitter.com/webdevstudios"><img src="http://webdevstudios.com/wp-content/uploads/2011/11/twitterIcon.png" /></a>
                <a target="_blank" href="http://facebook.com/webdevstudios"><img src="http://webdevstudios.com/wp-content/uploads/2011/11/facebookIcon.png" /></a>
            <td>
        </tr>
        <tr>
        	<td><a target="_blank" href="http://webdevstudios.com/team/brian-messenlehner/"><img src="https://lh3.googleusercontent.com/-eCNkGgNdWx8/AAAAAAAAAAI/AAAAAAAAAGQ/kjKbI1XZv3Y/photo.jpg?sz=50" /></a></td>
            <td><strong>Follow Brian Messenlehner!</strong><br />
            	<a target="_blank" href="https://plus.google.com/117578069784985312197"><img src="http://webdevstudios.com/wp-content/uploads/2011/11/google.png" /></a>
            	<a target="_blank" href="http://twitter.com/bmess"><img src="http://webdevstudios.com/wp-content/uploads/2011/11/twitterIcon.png" /></a>
            	<a target="_blank" href="http://facebook.com/bmess"><img src="http://webdevstudios.com/wp-content/uploads/2011/11/facebookIcon.png" /></a>
            </td>
        </tr>
        </table>
	</div>
    <?php  
}


//BADGE WIDGET
//Google+ Badge Widget
add_action('widgets_init', 'wds_google_connect_badge_register_widgets');
function wds_google_connect_badge_register_widgets() {
	register_widget( 'wds_google_connect_badge_widget' );
}
class wds_google_connect_badge_widget extends WP_Widget {

	//process new widget
	function wds_google_connect_badge_widget() {
		$widget_ops = array('classname' => 'wds_google_connect_badge_widget', 'description' => 'Displays a Google+ Page Badge.');
		$this->WP_Widget('wds_google_connect_badge_widget', __('Google+ Connect - Badge'), $widget_ops);
	}
 
 	//build widget settings form
	function form($instance) {
		$defaults = array( 'name' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$name = $instance['name'];
		?>
        	<p>Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="text" value="<?php echo esc_attr($name); ?>" /></p>
            <p><a href="/wp-admin/options-general.php?page=google-plus-connect-options">Click here to configure!</a></p>
		<?php
	}
 
  	//save widget settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['name'] = esc_attr($new_instance['name']);
		return $instance;
	}
 
 	//display widget
	function widget($args, $instance) {
		extract($args);
 		$name = apply_filters( 'widget_name', empty($instance['name']) ? '' : $instance['name'], $instance, $this->id_base);
		echo $before_widget;
		echo $before_title . $name . $after_title;
		wp_gplus_badge();
		echo $after_widget;
	}
}
?>