<?php //BuddyPress functions

//WDS Buddypress check
function wds_bp_check(){}

//SIDEBAR LOGIN
add_action('bp_after_sidebar_login_form','wds_gplus_bp_after_sidebar_login_form');
function wds_gplus_bp_after_sidebar_login_form(){
	wds_google_connect_button(true);
}

//REGISTRATION PAGE
add_action('bp_before_account_details_fields','wds_gplus_bp_before_account_details_fields');
function wds_gplus_bp_before_account_details_fields(){
	echo "<div id='gplus-connect-register' class='gplus-connect-register'>";
	//echo "<div id='gplus-connect-register-text' class='gplus-connect-register-text'>You can save time by registering via your Google+ account by clicking on the button below:</div>";
	wds_google_connect_button(true);
	echo "</div>";
}

//PROFILE SETTINGS PAGE
add_action('init','wds_google_connect_bp_settings');
function wds_google_connect_bp_settings(){
	global $bp,$user_ID;
  	$settings_link = $bp->loggedin_user->domain . $bp->settings->slug . '/';
	bp_core_new_subnav_item( array(
	   'name' => __('Google+', 'buddypress'),
	   'parent_url' => $settings_link,
	   'parent_slug' => $bp->settings->slug,
	   'slug' => 'google-plus',
	   'show_for_displayed_user' => false,
	   'screen_function' => 'wds_google_connect_bp_profile_screen',
	   'position' => 70 ) );
}

function wds_google_connect_bp_profile_screen() {
	add_action( 'bp_template_title', 'wds_google_connect_bp_template_title' );
	add_action( 'bp_template_content', 'wds_google_connect_bp_template_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function wds_google_connect_bp_template_title() {
	echo 'Google+ Connect Settings';
}

function wds_google_connect_bp_template_content() {
	global $user_ID;
	if($_POST['save_google_plus']){
		if($_POST['wds_google_connect_bp_activity_import']){
			update_user_meta($user_ID,'wds_google_connect_bp_activity_import','1');
			wds_google_connect_bp_user_activity($user_ID);
		}else{
			delete_user_meta($user_ID,'wds_google_connect_bp_activity_import');
		}
	}
	$wds_google_connect_bp_activity_import=get_user_meta($user_ID,'wds_google_connect_bp_activity_import',1);
	if($wds_google_connect_bp_activity_import){
		$wds_google_connect_bp_activity_import="checked";
	}
	?>
    <form method="post">
    	<input type="checkbox" name="wds_google_connect_bp_activity_import" value="1" <?php echo $wds_google_connect_bp_activity_import;?> />Stream your Google+ activity into your activity here.
        <br /><br />
        <div class="submit">
        <input type="submit" name="save_google_plus" value="Save Changes" />
        </div>
    </form>
    <?php
}

//add photo and update bp profile name for new accounts
function wds_google_connect_bp_user($user_id,$gplus_displayName,$gplus_photo){
	global $wpdb;
	//echo $sql;
	//Import Avatar
	if ( defined( 'BP_AVATAR_FULL_WIDTH' ) ){
		$full=BP_AVATAR_FULL_WIDTH;
	}else{
		$full="150";
	}
	if ( defined( 'BP_AVATAR_THUMB_WIDTH' ) ){
		$thumb=BP_AVATAR_THUMB_WIDTH;
	}else{
		$thumb="50";
	}
	$upload_path=get_option( 'upload_path' )."/avatars/".$user_id."/";
	if(!file_exists($upload_path)){
		mkdir( $upload_path );
	}
	$upload_path.="webdevstudios-".rand(100000000000, 9999999999999);
	file_put_contents( $upload_path."-bpfull.jpg", file_get_contents( $gplus_photo."?sz=".$full ) );
	$image = new WDS_SimpleImage();
	$image->load($upload_path."-bpfull.jpg");
	$image->resize($thumb,$thumb);
	$image->save($upload_path."-bpthumb.jpg");
	//update bp name
	$sql="insert into ".$wpdb->prefix."bp_xprofile_data (field_id,user_id,value) values(1,".$user_id.",'".$gplus_displayName."')";
	$wpdb->query( $wpdb->prepare( $sql ) );
}


//allow for html tags in activity
add_filter( 'bp_activity_allowed_tags', 'wds_digit_allow_p_h');
function wds_digit_allow_p_h( $activity_allowedtags ) {
	$activity_allowedtags['p'] = array();
	$activity_allowedtags['h3'] = array();
	return $activity_allowedtags;
}

//pull users activity
if($_GET['import']=="google"){
	add_action('init','wds_google_connect_cron_activity');
}
function wds_google_connect_bp_user_activity($user_id=""){
	global $user_ID, $wpdb, $bp;
	if(!$user_id){
		$user_id=$user_ID;
	}
	$gplus_id=get_user_meta($user_id, 'wds_google_connect_user_id');
	if($gplus_id){
	  $gplus_token=get_user_meta($user_id, 'wds_google_connect_token',true);
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
	  $client->setAccessToken($gplus_token);
	  if ($client->getAccessToken()) {
		  //$_SESSION['token'] = $client->getAccessToken();
		  //get any gplus post ids that exist for user
		  $sql="SELECT b.meta_value FROM {$bp->activity->table_name} a, {$bp->activity->table_name}_meta b WHERE a.id=b.activity_id and a.user_id=".$user_id." and b.meta_key = 'gplus_id'";
		  $gplus_ids[]="messenlehner";
		  $rs = $wpdb->get_results( $sql );
		  if ( count( $rs ) > 0 ) {
			  foreach( $rs as $r ) {
				  $gplus_ids[] = $r->meta_value;	
			  }
		  }
		  //query g+ api
		  $optParams = array('maxResults' => 100);
		  $activities = $plus->activities->listActivities($gplus_id, 'public',$optParams);
		  foreach($activities['items'] as $key=>$value){
		  	$content="";
			$id=$activities['items'][$key]['id'];
			$title=$activities['items'][$key]['title'];
			$kind=$activities['items'][$key]['kind'];
			$published=$activities['items'][$key]['published'];
			$url=$activities['items'][$key]['url'];
			$verb=$activities['items'][$key]['verb'];
			$objectType=$activities['items'][$key]['object']['objectType'];
			$gcontent=$activities['items'][$key]['object']['content'];
			//images
			$image=$activities['items'][$key]['object']['attachments'][0]['image']['url'];
			$image=str_replace("resize_h=100","resize_h=300",$image);
			$fullImage=$activities['items'][$key]['object']['attachments'][0]['fullImage']['url'];
			$image2=$activities['items'][$key]['object']['attachments'][1]['image']['url'];
			$image2=str_replace("resize_h=100","resize_h=300",$image2);
			$fullImage2=$activities['items'][$key]['object']['attachments'][1]['fullImage']['url'];
			//attachment links and info
			$attachmentobjectType=$activities['items'][$key]['object']['attachments'][0]['objectType'];
			$attachmentdisplayName=$activities['items'][$key]['object']['attachments'][0]['displayName'];
			$attachmentcontent=$activities['items'][$key]['object']['attachments'][0]['content'];
			$attachmenturl=$activities['items'][$key]['object']['attachments'][0]['url'];
			$geocode=$activities['items'][$key]['geocode'];
			$address=$activities['items'][$key]['address'];
			$placeName=$activities['items'][$key]['placeName'];
			if($title!=$attachmentdisplayName && $verb!="share"){
				$content.="<p><b>".$title."</b></p>";	
			}
			if($gcontent!=$attachmentdisplayName && $gcontent!=$title){
				$content.="<p>".$gcontent."</p>";	
			}
			if($placeName){
				$content.="<p><b>Google+ checked in at ".$placeName."</b></p>";	
			}
			if($address){
				$content.="<p>".$address."</p>";	
			}
			if($attachmentdisplayName){
				$content.="<p><a target='_blank' href='".$attachmenturl."'>".$attachmentdisplayName."</a></p>";	
			}
			if($address){
				$attachmentcontent.="<p>".$attachmentcontent."</p>";	
			}
			if($image){
				$content.='<p><img src="'.$image.'"></p>';	
			}
			if($image2){
				$content.='<p><img src="'.$image2.'"></p>';	
			}
			if($url){
				//$content.="<p><a target='_blank' href='".$url."'>View on Google+</a></p>";	
			}	
			//bp_activity_get_meta( $activity_id = 0, $meta_key = '' );
			//only add posts not added
			if(!in_array($id,$gplus_ids)){
				$activity_id=bp_activity_add( array(
				 'user_id' => $user_id,
				 'content' => $content,
				 'component' => 'profile',
				 'type' => 'activity_update',
				 'recorded_time' => $published
				 ) );
				 bp_activity_update_meta( $activity_id, 'gplus_id', $id );
				 if($placeName){
					bp_activity_update_meta( $activity_id, 'gplus_placeName', $placeName );	
				 }
				 if($address){
					bp_activity_update_meta( $activity_id, 'gplus_address', $address );
				 }
				 if($geocode){
					bp_activity_update_meta( $activity_id, 'gplus_geocode', $geocode );
				 }
				 if($verb){
					bp_activity_update_meta( $activity_id, 'gplus_verb', $verb );
				 }
			}
		  }
		  /*echo "<pre>";
		  print_r($activities);
		  echo "</pre>";*/
	  }
	}
}


//cron for new activity
function wds_google_connect_cron_schedules() {
    return array( 
		'halfhour' => array(
			'interval' => 1800, /* 60 seconds * 30 minutes */
			'display' => 'Twice hour'
		),
		'weekly' => array(
			'interval' => 604800, /* 60 seconds * 60 minutes * 24 hours * 7 days */
			'display' => 'Weekly'
		)
	);
}
add_filter('cron_schedules', 'wds_google_connect_cron_schedules');

if (!wp_next_scheduled('wds_google_connect_cron')) {
	wp_schedule_event( time(), 'halfhour', 'wds_google_connect_cron' );
}
add_action( 'wds_google_connect_cron', 'wds_google_connect_cron_activity' ); 
function wds_google_connect_cron_activity() {
	$users=get_users(array('meta_key' => 'wds_google_connect_bp_activity_import'));
	foreach ($users as $user) {
        $gusers[]=$user->ID;
	}
	$users=get_users(array('meta_key' => 'wds_google_connect_token'));
	foreach ($users as $user) {
        if(is_array($gusers) && in_array($user->ID,$gusers)){
			wds_google_connect_bp_user_activity($user->ID);
		}
	}
}

//CLASS FOR RESIZING IMAGES
class WDS_SimpleImage {
 
   var $image;
   var $image_type;
 
   function load($filename) {
 
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
 
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
 
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
 
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image,$filename);
      }
      if( $permissions != null) {
 
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image);
      }
   }
   function getWidth() {
 
      return imagesx($this->image);
   }
   function getHeight() {
 
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
 
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
 
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
 
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
 
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }      
 
}

?>