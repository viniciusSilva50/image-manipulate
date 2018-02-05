<?php 


function itm_optimizeImage($file, $compression = 70, $maxDimensions = ['width' => null, 'height' => null]) {
    $save = false;
    $fi = new finfo(FILEINFO_MIME);
    $mime = explode(';', $fi->file($file));	
    switch ($mime[0]) {        
        case 'image/jpeg':
            try {
                $iMagick = new Imagick($file);				
                if ($iMagick->getImageCompressionQuality() > $compression) {
                    $file = !itm_compressJPEG($file, $compression, $maxDimensions, $iMagick);
                }
            }catch (Exception $e) {
                error_log(__FUNCTION__ . " $path/$file failed: " . $e->getMessage());
                return false;
            }
            if ($file) {
                $pathParts = pathinfo($file);
                rename($file, $pathParts['dirname'] . '/' . $pathParts['filename'] . '.large.' . $pathParts['extension']);
                $iMagick->writeImage($file);
            }
            $iMagick->clear();
            break;
		case 'image/png':
			try {
                $iMagick = new Imagick($file);				
                if ($iMagick->getImageCompressionQuality() > $compression) {
                    $file = !itm_compressPNG($file, $compression, $maxDimensions, $iMagick);
                }
            }catch (Exception $e) {
                error_log(__FUNCTION__ . " $path/$file failed: " . $e->getMessage());
                return false;
            }
            
			if ($file) {
                $pathParts = pathinfo($file);
                rename($file, $pathParts['dirname'] . '/' . $pathParts['filename'] . '.large.' . $pathParts['extension']);
                $iMagick->writeImage($file);
            }
            $iMagick->clear();
			break;
    }

    return $file;
}

function itm_compressJPEG($file, $compression = 70, $maxDimensions = ['width' => null, 'height' => null], &$iMagick = null) {
    try {
		$iMagick = new Imagick($file);
        
        $iMagick->setImageResolution(72,72);
        $iMagick->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);
        $geometry = $iMagick->getImageGeometry();
			
        $iMagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $iMagick->setImageCompressionQuality($compression);
        $iMagick->setImageFormat('jpg');
        $iMagick->stripImage();
		
        		
		$pathParts = pathinfo($file);
		rename($file, $pathParts['dirname'] . '/' . $pathParts['filename'] . '.large.' . $pathParts['extension']);
		$iMagick->writeImage($file);
		$iMagick->clear();
       
        return $file;
    }
    catch (Exception $e) {
        error_log(__FUNCTION__ . " $path/$file failed: " . $e->getMessage());
        return false;
    }
}

function itm_compressPNG($file, $compression = 70, $maxDimensions = ['width' => null, 'height' => null], &$iMagick = null) {
    try {
		$iMagick = new Imagick($file);
        
        $iMagick->setImageResolution(72,72);
        $iMagick->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);
        $geometry = $iMagick->getImageGeometry();
		
        $iMagick->setImageCompressionQuality($compression);
        $iMagick->setImageFormat('png');
        $iMagick->stripImage();
		        		
		$pathParts = pathinfo($file);
		rename($file, $pathParts['dirname'] . '/' . $pathParts['filename'] . '.large.' . $pathParts['extension']);
		$iMagick->writeImage($file);
		$iMagick->clear();
       
        return $file;
    }
    catch (Exception $e) {
        error_log(__FUNCTION__ . " $path/$file failed: " . $e->getMessage());
        return false;
    }
}


$dir   = getcwd();
if ($handle = opendir($dir)) {
    while (false !== ($entry = readdir($handle))) {
		if($entry != 'FrutosBrocados_01.jpg' && ( (strpos($entry, '.jpg') !== false && strpos($entry, '.large.') == false) || (strpos($entry, '.png') !== false && strpos($entry, '.large.') == false)) ) {		
			$pathParts = pathinfo($dir. "\\".$entry);
			$fileLarge = $pathParts['dirname'] . '/' . $pathParts['filename'] . '.large.' . $pathParts['extension'];
		
			if(!file_exists($fileLarge)){	
				echo $entry.PHP_EOL; 	
				itm_optimizeImage($dir. "\\".$entry);		
			}
		}
    }
    closedir($handle);
}
?>
