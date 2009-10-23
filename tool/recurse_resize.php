<?php

/*
	resize($img, $w, $h, $newfilename) 
	$img : nom fichier
	$w, $h : taille image
	$newfilename : nom fichier image resultat

	return : 
	- nom de fichier resultat si ok
	- "" si $img n'est pas un fichier image
*/
function resize($img, $w, $h, $newfilename) 
{
   //Check if GD extension is loaded
   if (!extension_loaded('gd') && !extension_loaded('gd2')) {
	trigger_error("GD is not loaded", E_USER_WARNING);
	return false;
   }
   
   //Get Image size info
   $imgInfo = getimagesize($img);
   switch ($imgInfo[2]) {
	case 1: $im = imagecreatefromgif($img); break;
	case 2: $im = imagecreatefromjpeg($img);  break;
	case 3: $im = imagecreatefrompng($img); break;
	//default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
	default:  return ""; break;
   }
	//yeah, resize it, but keep it proportional
	if ($w/$imgInfo[0] > $h/$imgInfo[1])
	{
	 $nWidth = $w;
	 $nHeight = $imgInfo[1]*($w/$imgInfo[0]);
	}else{
	 $nWidth = $imgInfo[0]*($h/$imgInfo[1]);
	 $nHeight = $h;
	}
   $nWidth = round($nWidth);
   $nHeight = round($nHeight);
   
   $newImg = imagecreatetruecolor($nWidth, $nHeight);
   
   /* Check if this image is PNG or GIF, then set if Transparent*/  
   if(($imgInfo[2] == 1) OR ($imgInfo[2]==3)){
	imagealphablending($newImg, false);
	imagesavealpha($newImg,true);
	$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
	imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
   }
   imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
   
   //Generate the file, and rename it to $newfilename
   switch ($imgInfo[2]) {
	case 1: imagegif($newImg,$newfilename); break;
	case 2: imagejpeg($newImg,$newfilename);  break;
	case 3: imagepng($newImg,$newfilename); break;
	default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
   }
	 return $newfilename;
}


/*
	recurse_createResize($src,$dst)
	$src : repertoire source
	$dst : repertoire destination
	
	Resize chaque image png, jpeg, gif 
	du repertoire $src (recursivement)
	a la taille $G_WIDTH, $G_HEIGHT
	dans le repertoire $dst
*/
function recurse_createResize($src,$dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) 
	{
        if (( $file != '.' ) && ( $file != '..' )) 
		{
            if ( is_dir($src . '/' . $file) ) 
			{
                recurse_createResize($src . '/' . $file, $dst . '/' . $file);
            }
            else 
			{
				$filein = $src . '/' . $file;
				$fileout = $dst . '/' . $file;
				$G_WIDTH = 30; //resultat de 80 pixels
				$G_HEIGHT = 30; //resultat de 80 pixels 
				$thumbnail = resize($filein, $G_WIDTH, $G_HEIGHT, $fileout);
				echo $filein ." => " ;
				if ($thumbnail != "")
					echo $fileout ."<img src='".$thumbnail."'></br>";
				else
					echo "invalide</br>";
			}
        }
    }
    closedir($dir);
} 

/* utilisation : */
recurse_createResize('../../image/', '../../image/');

?>