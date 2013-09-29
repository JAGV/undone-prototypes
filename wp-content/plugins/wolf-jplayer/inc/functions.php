<?php
if( !function_exists('debug') ):
/**
* Debug function
*/
function debug($var){
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}
endif;

//-------------------------------------------------


if(!function_exists('wolf_delete_file')):
/**
* Delete file
*/
function wolf_delete_file($file_path){

	if( is_file( $file_path ) )
		unlink($file_path);

}
endif;

// --------------------------------------------------------------------------


function wolf_get_jplayer_option( $value )
{
	global $options;
	$settings = get_option('wolf_jplayer_settings');
	
	if( isset($settings[$value]) )
		return $settings[$value];

}

//-------------------------------------------------

/**
* Custom admin notice
*/
function wolf_jpayer_admin_notices($message = null, $type = null, $dismiss = false, $id = null )
{
	if( $dismiss ){

		$dismiss = __('Hide permanently', 'wolf');

		if($id){
			if( !isset($_COOKIE[$id]) )
				echo '<div class="wrap"><div class="'.$type.'"><p>'.$message.'<span class="wolf-close-admin-notice">&times;</span><span id="' . $id . '" class="wolf-dismiss-admin-notice">'.$dismiss.'</span></p></div></div>';
		}else{
			echo '<div class="wrap"><div class="'.$type.'"><p>'.$message.'<span class="wolf-close-admin-notice">&times;</span><span class="wolf-dismiss-admin-notice">'.$dismiss.'</span></p></div></div>';
		}
	
	}else{
		echo '<div class="wrap"><div class="'.$type.'"><p>'.$message.'</p></div></div>';
	}
}
add_action( 'admin_notices', 'wolf_jpayer_admin_notices'  );


/*-----------------------------------------------------------------------------------*/
/*  Thumbnail
/*-----------------------------------------------------------------------------------*/

/**
* Generate a thumbnail from an uploaded image
* @param string : filename
* @param string : path to the image
* @param string : image name
* @param int : max width in px
* @param int : max height in px
*/
if(!function_exists(('wolf_thumbnail'))):
function wolf_thumbnail($img,$path,$name,$mwidth=100,$mheight=100)
{
    list($imagewidth, $imageheight, $imageType) = getimagesize($img);
    $imageType = image_type_to_mime_type($imageType);
    $dimension=getimagesize($img);

    if($imageType=='image/gif'){
        $source=imagecreatefromgif($img); 
    }elseif( $imageType == 'image/pjpeg' || $imageType == 'image/jpeg' || $imageType == 'image/jpg'){
        $source=imagecreatefromjpeg($img); 
    }elseif( $imageType == 'image/png' || $imageType == 'image/x-png'){
        $source=imagecreatefrompng($img); 
    }       

    $min = imagecreatetruecolor($mwidth,$mheight); 
 
    if($dimension[0]>($mwidth/$mheight)*$dimension[1] ){ 
        $dimY=$mheight; 
        $dimX=$mheight*$dimension[0]/$dimension[1]; 
        $decalX=-($dimX-$mwidth)/2; 
        $decalY=0;
    }
    if($dimension[0]<($mwidth/$mheight)*$dimension[1]){ 
        $dimX=$mwidth; 
        $dimY=$mwidth*$dimension[1]/$dimension[0]; 
        $decalY=-($dimY-$mheight)/2; 
        $decalX=0;
    }
    if($dimension[0]==($mwidth/$mheight)*$dimension[1]){ 
        $dimX=$mwidth; 
        $dimY=$mheight; 
        $decalX=0; 
        $decalY=0;
    }
    

     /* Keep transparency */
     if($imageType=='image/gif' || $imageType == 'image/png' || $imageType == 'image/x-png'){

	imagealphablending($min, false);
	imagesavealpha($min,true);
	$transparent = imagecolorallocatealpha($min, 255, 255, 255, 127);
	imagefilledrectangle($min, 0, 0, $decalX, $decalY, $transparent);
     }


    imagecopyresampled($min,$source,$decalX,$decalY,0,0,$dimX,$dimY,$dimension[0],$dimension[1]);

    if($imageType=='image/gif'){
        imagegif($min,$path.$name); 
    }elseif( $imageType == 'image/pjpeg' || $imageType == 'image/jpeg' || $imageType == 'image/jpg'){
        imagejpeg($min,$path.$name,90); 
    }elseif( $imageType == 'image/png' || $imageType == 'image/x-png'){
        imagepng($min,$path.$name);  
    }

    imagedestroy($source);
    return true;    
}
endif;