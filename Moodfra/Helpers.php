<?php

/*  Show thumb image in backend */ 
function thumb_img_backend($project_id,$image_name){
return   $image_path = 'https://moodfra.s3.amazonaws.com/project/'.$project_id.'/'.$image_name;
}

/* Download Image */ 
function image_download_path($project_id,$image_name){

  return $image_path = '/storage/uploads/project/'.$project_id.'/main_img/'.$image_name;

}
/* Resize image on the fly */
function timthumb($img,$w,$h){
		  $user_img =  url('plugin/timthumb/timthumb.php').'?src='.$img.'&w='.$w.'&h='.$h.'&zc=1&q=99';
		  return $user_img ;
}
/* Get Agency Name */ 
function agency($id){
          $acredits = DB::table('agency_credits')->where('id', '=', $id)->get()->toArray();
          $fname ='';
          foreach($acredits as $name){ 
             $fname = $name->name;
          }
          return $fname;
}

/* Embed Code of Sites Allow */ 
function embed_sites_array(){
    $embed_array=array('vimeo.com','youtube.com');
    return $embed_array;
}

/* Get Image from Embed Code */
function embed_code_image($html,$site_val){
    /* You tube */ 
    if($site_val=='youtube.com'){
        $video_id= getyoutube_video_id($html);
       $image ='http://img.youtube.com/vi/'.$video_id.'/mqdefault.jpg';
    }
	/* Vimeo */
    if($site_val=='vimeo.com'){
       
       $video_id= getvimeo_video_id($html);
       $image = getVimeoVideoThumbnailByVideoId( $video_id,'large' );
	}
    return $image;
}
/* Get Image from vimeo embed  code */ 
function getVimeoVideoThumbnailByVideoId( $id, $thumbType = 'large' ) {
		$id = trim( $id );
        if ( $id == '' ) {
            return FALSE;
        }
        $apiData = unserialize(@file_get_contents("http://vimeo.com/api/v2/video/".$id.".php"));
		/* Return Small, Medium and Large */
        if ( is_array( $apiData ) && count( $apiData ) > 0 ) {
            $videoInfo = $apiData[ 0 ];
            switch ( $thumbType ) {
                case 'small':
                    return $videoInfo[ 'thumbnail_small' ];
                    break;
                case 'large':
					return $videoInfo[ 'thumbnail_large' ];
                    break;
                case 'medium':
                    return $videoInfo[ 'thumbnail_medium' ];
                default:
                    break;
            }
        }
        return FALSE; 
    }
/* Get Youtube Video Id */	
function getyoutube_video_id($html)
{
    $pattern = '#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=‌​(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#';
    preg_match($pattern, $html, $matches);
    $url = reset($matches);
    $url = strtok($url, '?');
    return $url;
}
/* Get Vimeo Video id */
function getvimeo_video_id($html)
{		
	if(preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $html, $output_array)) {
	 $vimeoId = 	$output_array[5];
	}
	return $vimeoId;
} 

/***************** Time ago script *************/
function timeAgo($time_ago)
{
    $time_ago = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        return "just now";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            return "one minute ago";
        }
        else{
            return "$minutes minutes ago";
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            return "an hour ago";
        }else{
            return "$hours hrs ago";
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            return "yesterday";
        }else{
            return "$days days ago";
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            return "a week ago";
        }else{
            return "$weeks weeks ago";
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            return "a month ago";
        }else{
            return "$months months ago";
        }
    }
    //Years
    else{
        if($years==1){
            return "one year ago";
        }else{
            return "$years years ago";
        }
    }
}


/*********************** User Data ************************/
function user_data($id){
		$user_data =  DB::table('users')->where('id','=',$id)->get();
	    return $user_data;
}

/* Get All Keyowrds */ 
 function getAllkeywords(){
	$records = DB::table('prjct_img');
	$records = $records->select('keywords');
	$records = $records->distinct('make');
	$records = $records->get();
	$newrecords = array();
	foreach($records as $key=>$value){
		if($value->keywords){
			$all_akeywords = explode(',',$value->keywords);
			foreach($all_akeywords as $k=>$v){
				$v= trim($v);
				if(!in_array($v,$newrecords)){
					$newrecords[] = $v;
				}	
			}
		}
	}
	return json_encode($newrecords);
}
/* For Print any array */ 
function pr($data){

  echo "<pre>";
  print_r($data);
  echo "</pre>" ;
}
