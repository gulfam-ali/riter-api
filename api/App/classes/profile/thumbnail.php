<?php
$f=0;

$path = $image_dir."/media/images/".$newfilename;

if($ext == "jpg" || $ext == "jpeg")
{
	$image = imagecreatefromjpeg($path);
	$mime = "image/jpeg";
}
else if($ext =="png")
{
	$image = imagecreatefrompng($path);
	$mime = "image/png";
}
else if($ext == "gif")
{
	$image = imagecreatefromgif($path);
	$mime = "image/gif";
}
else
{
	$res['validate'] = "invalid";
	return $res;
	die;
}

//$image = imagecreatefromjpeg($path); // Source

$filename2 = $image_dir."/media/images/$newfilename"; // Destination

$thumb_width = 320;
$thumb_height = 320;

$width = imagesx($image);
$height = imagesy($image);

$original_aspect = $width / $height;
$thumb_aspect = $thumb_width / $thumb_height;

if ( $original_aspect >= $thumb_aspect )
{
   // If image is wider than thumbnail (in aspect ratio sense)
   $new_height = $thumb_height;
   $new_width = $width / ($height / $thumb_height);
}
else
{
   // If the thumbnail is wider than the image
   $new_width = $thumb_width;
   $new_height = $height / ($width / $thumb_width);
}

$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

// Resize and crop
imagecopyresampled($thumb,
                   $image,
                   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                   0, 0,
                   $new_width, $new_height,
                   $width, $height);
imagejpeg($thumb, $filename2, 80);