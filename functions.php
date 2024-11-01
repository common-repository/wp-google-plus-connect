<?php

//******************************************************************************************************************
//Direct Connect & Badge

//Google+ Direct Connect & Badge Header
add_action('wp_head', 'wds_google_connect_badge_wp_head');
add_action('admin_head', 'wds_google_connect_badge_wp_head');
function wds_google_connect_badge_wp_head(){
	$wds_google_connect_badge_page_id=esc_attr(get_option('wds_google_connect_badge_page_id'));
	$wds_google_connect_badge_lang=esc_attr(get_option('wds_google_connect_badge_lang'));
	if($wds_google_connect_badge_page_id){?>
      <link href="https://plus.google.com/<?php echo $wds_google_connect_badge_page_id;?>" rel="publisher" />
      <script type="text/javascript">
      window.___gcfg = {lang: '<?php echo $wds_google_connect_badge_lang;?>'};
	  (function() 
      {var po = document.createElement("script");
      po.type = "text/javascript"; po.async = true;po.src = "https://apis.google.com/js/plusone.js";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(po, s);
      })();</script>
      <?php
	}
}
//Google+ Badge Short Code
add_shortcode( 'gplus_badge', 'gplus_badge_shortcode' );
function gplus_badge_shortcode( $atts ) {
	ob_start();
	wp_gplus_badge();
	$return=ob_get_contents();;
	ob_end_clean();
	return $return;
}

//Google+ Badge Function
function wp_gplus_badge(){
  $wds_google_connect_badge_page_id=esc_attr(get_option('wds_google_connect_badge_page_id'));
  $wds_google_connect_badge_name=esc_attr(get_option('wds_google_connect_badge_name'));
  $wds_google_connect_badge_type=esc_attr(get_option('wds_google_connect_badge_type'));
  if($wds_google_connect_badge_type && $wds_google_connect_badge_page_id){
	if($wds_google_connect_badge_type=="badge" || $wds_google_connect_badge_type=="smallbadge" ){?>
		<g:plus href="https://plus.google.com/<?php echo $wds_google_connect_badge_page_id;?>" size="<?php echo $wds_google_connect_badge_type;?>"></g:plus>
	<?php }else{?>
		<a href="https://plus.google.com/<?php echo $wds_google_connect_badge_page_id;?>?prsrc=3" style="text-decoration:none;">
		<img src="https://ssl.gstatic.com/images/icons/gplus-<?php echo $wds_google_connect_badge_type;?>.png" alt="" style="border:0;width:<?php echo $wds_google_connect_badge_type;?>px;height:<?php echo $wds_google_connect_badge_type;?>px;"/>
		</a>
	<?php }
  }
}
//******************************************************************************************************************

//******************************************************************************************************************
//Google+ Login 

//Google+ Auth and Connect Button
add_action('init', 'wds_google_connect_button');
function wds_google_connect_button($button=false){
  $url=$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  $site=str_replace("http://","",site_url());
  $site=str_replace("https://","",$site);
  if($url!=$site."/wp-admin/admin-ajax.php" && $url!=$site."/wp-load.php"){
  	global $user_ID;
	//send to google
	if(isset($_GET['wds_gplus_connect_login'])){
		$authUrl=$_GET['wds_gplus_connect_login'];
		$authUrl=str_replace("*","&",$authUrl);
		//echo $authUrl;
		wp_redirect( $authUrl );
		exit();
	}

	if (isset($_GET['logout'])) {
	  unset($_SESSION['token']);
	  header('Location: '.site_url());
	}
	if (isset($_SESSION['token']) && $user_ID!=0) {
		update_user_meta($user_ID, 'wds_google_connect_token', $_SESSION['token']);
	}
	if (!$button) {
	  if (!class_exists('apiException')) {
	  	require_once 'src/apiClient.php';
	  	require_once 'src/contrib/apiPlusService.php';
	  }
	  session_start();
	}
	if (isset($_GET['code']) || $button==true) {
	  $wds_google_connect_app_name=esc_attr(get_option('wds_google_connect_app_name'));
	  $wds_google_connect_client_id=esc_attr(get_option('wds_google_connect_client_id'));
	  $wds_google_connect_client_secret=esc_attr(get_option('wds_google_connect_client_secret'));
	  $wds_google_connect_redirect_url=esc_attr(get_option('wds_google_connect_redirect_url'));
	  $wds_google_connect_developer_key=esc_attr(get_option('wds_google_connect_developer_key'));
	  $client = new apiClient();
	  $client->setApplicationName($wds_google_connect_app_name);
	  $client->setClientId($wds_google_connect_client_id);
	  $client->setClientSecret($wds_google_connect_client_secret);
	  $client->setRedirectUri($wds_google_connect_redirect_url);
	  $client->setDeveloperKey($wds_google_connect_developer_key);
	  $plus = new apiPlusService($client);
	  //kill session if new login
	  if (isset($_GET['code'])) {
		if (strval($_SESSION['state']) !== strval($_GET['state'])) {
		  die("The session state ({$_SESSION['state']}) didn't match the state parameter ({$_GET['state']})");
		}
		try {
			$client->authenticate();
			$_SESSION['token'] = $client->getAccessToken();
		} catch (Exception $e) {
			  unset($_SESSION['token']);
			  //echo $e;
			  print "<p style=\"color:red\">Invalid API Credentials!<p>\n";
			  echo '<meta http-equiv="refresh" content="3; url='.site_url().'">';
			  exit();
		}
	  }
	  //new token
	  if (isset($_SESSION['token'])) {
		$client->setAccessToken($_SESSION['token']);
	  }
	  //if token then grab data
	  if ($client->getAccessToken()) {
		//Check if user exists and/or create
		if (isset($_GET['code'])) {
		  try {
			$me = $plus->people->get('me');
		  } catch (Exception $e) {
			  unset($_SESSION['token']);
			  print "<p style=\"color:red\">You must login with a valid Google+ Account!<p>\n";
			  echo '<meta http-equiv="refresh" content="3; url='.site_url().'">';
			  exit();
		  }
		  //Get or Create User
		  //Check if G+ ID for a user already exists
		  $gplus_id=$me['id'];
		  $gplus_email=$me['emails'][0]['value'];
		  if(!$gplus_email){
			$gplus_email="gplus.".$gplus_id."@gmail.com";
		  }
		  $gplus_displayName=$me['displayName'];
		  $gplus_photo=$me['image']['url'];
		  if($gplus_id){
			if($user_ID!=0){//if already logged into wp account but want to link with g+
				$user_id=$user_ID;
				if(!get_user_meta($user_id, 'wds_google_connect_user_id')){
					update_user_meta($user_id, 'wds_google_connect_user_id', $gplus_id);
				}
			}else{//not logged into wp
			  $users=get_users(array('meta_key' => 'wds_google_connect_user_id', 'meta_value' => $gplus_id));
			  $user_id=(int)$users[0]->ID;
			  if(!$user_id){
				$new_user=true;
				$user_name=$gplus_displayName;
				$arr_user_name=explode(" ",$user_name);
				$user_name=$arr_user_name[0];//user first name
				$user_name=sanitize_user( $user_name );
				$user_name=str_replace(array(" ","."),"",$user_name);
				$user = username_exists( $user_name );
				if ( $user ) { //try last name
					$user_name=$arr_user_name[1];
					$user_name=sanitize_user( $user_name );
					$user_name=str_replace(array(" ","."),"",$user_name);
					$user = username_exists( $user_name );
				}
				if ( $user ) { //try first & last name
					$user_name=$gplus_displayName;
					$user_name=sanitize_user( $user_name );
					$user_name=str_replace(array(" ","."),"",$user_name);
					$user = username_exists( $user_name );
				}
				if ( $user ) { //if username happens to exsit tie a random 3 digit number to the end 
					$user_name=$arr_user_name[0];
					$user_name=sanitize_user( $user_name );
					$user_name=str_replace(array(" ","."),"",$user_name);
					$user_name=$user_name.rand(100, 999);
					$user = username_exists( $user_name );
				}
				if ( !$user ) { //if no user create them
					  $random_password = wp_generate_password( 12, false );
					  $user_id = wp_create_user( $user_name, $random_password, $gplus_email );
					  if(!is_int($user_id)){//if email address already exists (wp user but new g+ user)
						  $user=get_user_by_email($gplus_email);
						  $user_id=$user->ID;
						  //echo "gplus_email: ".$gplus_email."<br>";
					  }else{
						  update_user_meta($user_id, 'nickname', $gplus_displayName);
						  update_user_meta($user_id, 'display_name', $gplus_displayName);
						  //budypress functions
						  if(function_exists('wds_bp_check')){
							  wds_google_connect_bp_user($user_id,$gplus_displayName,$gplus_photo);//buddypress.php
						  }
					  }
					  update_user_meta($user_id, 'wds_google_connect_user_id', $gplus_id);
					  //echo "gplus_id: ".$gplus_id."<br>";
					  //echo "user_id: ".$user_id."<br>";
				}else{//if username already exists even after random (maybe put in loop to check name so will always make a new one if random lands twicw)
					  echo "<p style=\"color:red\">Please try to connect again!<p>\n";
					  echo '<meta http-equiv="refresh" content="3; url='.site_url().'">';
					  exit();
				}
			  }
			  //login user and redirect
			  wp_set_auth_cookie( $user_id, false, is_ssl() );
			}
			update_user_meta($user_id, 'wds_google_connect_token', $_SESSION['token']);
			update_user_meta($user_id, 'wds_google_connect_photo', $gplus_photo);
			wp_redirect( site_url() );
			exit();
		  }
		}
		if (isset($_GET['code'])) {
		  $_SESSION['token'] = $client->getAccessToken();
		}
	  }
	  //display button
	  if ($button==true) {
		if (!$client->getAccessToken()) {
			if($_SESSION['state']){
			  $state=$_SESSION['state'];
			}else{
			  $state = mt_rand();
			}
			$client->setState($state);
			$_SESSION['state'] = $state;
			$authUrl = $client->createAuthUrl();
			//$authUrl=str_replace("&","*",$authUrl);
			if($wds_google_connect_app_name && $wds_google_connect_client_id && $wds_google_connect_client_secret && $wds_google_connect_redirect_url && $wds_google_connect_developer_key){
				//echo $authUrl;
				//add_query_arg( 'wds_gplus_connect_login', $authUrl )
				echo "<div class='gplus-login' style='padding:5px 0;'><a class='login' href='".$authUrl."'><img title='Google+ Connect!' src='".plugins_url('gplus-button.png',__FILE__)."'></a></div>";
			}
		}
	  }
	}
  }
}

//kill g+ session on wp log out
add_action('wp_logout','wds_gplus_wp_logout');
function wds_gplus_wp_logout(){
	unset($_SESSION['token']);
}

//g+ login button on wp login screen
add_action('login_form', 'wp_gplus_login_button');
function wp_gplus_login_button(){
	wds_google_connect_button(true);
}

//short code
add_shortcode( 'gplus_button', 'gplus_button_shortcode' );
function gplus_button_shortcode( $atts ) {
	ob_start();
	wds_google_connect_button(true);
	$return=ob_get_contents();;
	ob_end_clean();
	return $return;
}
//******************************************************************************************************************
?>