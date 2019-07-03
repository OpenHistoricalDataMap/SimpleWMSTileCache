<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 18.11.2018
 * Time: 19:59
 */

$time = microtime(true);

ini_set('precision', 10);
include('config.php');

/**
 * Lowercase all Parameters
 */
$get = [];
foreach($_GET as $key => $val){
	$get[strtolower($key)] = $val;
}

if (($get['request'] == 'GetMap') && ($get['layers'] == 'cache')) {
	
	//Filter relevant Map parameters
	$requestData = [];
	foreach($relevantParameters as $rp){
		if(array_key_exists($rp, $get))
			$requestData[$rp] = $get[$rp];
	}
	
	//calculate Cache Keys and Path
	ksort($requestData);
	$cacheKey = md5(implode($requestData));
	$cacheSubKey = substr($cacheKey, 0, 2);
	$cachePath = CACHE_PATH.$cacheSubKey.'/';
	$cacheFile = $cacheKey . '.png';
	$cacheFilePath = $cachePath.$cacheFile;
	if(!is_dir($cachePath))
		mkdir($cachePath);
	$cacheHit = true;
	
	//Check if File is cached, otherwise Load from Backend
	if (!file_exists($cacheFilePath)) {
		$cacheHit = false;
		//Setup the combined image
		$dest_image = imagecreatetruecolor($_GET['WIDTH'], $_GET['HEIGHT']);
		imagesavealpha($dest_image, true);
		$trans_background = imagecolorallocatealpha($dest_image, 0, 0, 0, 127);
		imagefill($dest_image, 0, 0, $trans_background);
		
		//Initialize multiple CURL Instances to the Backend
		$mh = curl_multi_init();
		$handles = [];
		$fhandles = [];
		foreach ($wmsLayers as $wl) {
			$requestData['LAYERS'] = $wl;
			$cacheTmpKey = md5(implode($requestData));
			$cacheTmpPath = TEMP_PATH . $cacheTmpKey . '.png';
			$fhandles[$cacheTmpKey] = fopen ($cacheTmpPath, 'w+');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, REMOTE_WMS.'?' . http_build_query($requestData));
			curl_setopt($ch, CURLOPT_FILE, $fhandles[$cacheTmpKey]);
			curl_multi_add_handle($mh, $ch);
			$handles[] = $ch;
		}
		//run all the Curl & wait for them to finish
		$running = 0;
		do
			curl_multi_exec($mh, $running);
		while ($running > 0);
		foreach($handles as $h)
			curl_multi_remove_handle($mh, $h);
		curl_multi_close($mh);
		
		//Combine all images 
		foreach ($wmsLayers as $wl) {
			$requestData['LAYERS'] = $wl;
			$cacheTmpKey = md5(implode($requestData));
			$cacheTmpPath = TEMP_PATH . $cacheTmpKey . '.png';
			fclose($fhandles[$cacheTmpKey]);
			$img = imagecreatefrompng($cacheTmpPath);
			imagecopy($dest_image, $img, 0, 0, 0, 0, $_GET['WIDTH'], $_GET['HEIGHT']);
			unlink($cacheTmpPath);
		}
		//Render Image to Disk
		imagepng($dest_image, $cacheFilePath);
	}
	
	//PreCache
	if(!($get['cachemode'] === 'prefetch')){
		bcscale(10);
		$preFetchGet = $get;
		$preFetchGet['cachemode'] = 'prefetch';
		list($x1,$y1,$x2,$y2) = explode(",",$get['bbox']);
		
		//Left BBOX
		$x1n = str_replace(",",".",bcsub($x1, bcsub($x2,$x1)));
		$y1n = str_replace(",",".",$y1);
		$x2n = str_replace(",",".",bcsub($x2, bcsub($x2,$x1)));
		$y2n = str_replace(",",".",$y2);
		
		$preFetchGet['bbox'] = $x1n.",".$y1n.",".$x2n.",".$y2n;
		
		$preCacheURL = LOCAL_WMS . '?' . http_build_query($preFetchGet);
		//file_get_contents($preCacheURL);
	}
	
	
	if(!DEBUG){
		header('Content-Type: image/png');
		echo file_get_contents($cacheFilePath);
	}else{
		header('Content-Type: image/png');
		$dbgImage = imagecreatefrompng($cacheFilePath);
		imageAlphaBlending($dbgImage, true);
		imageSaveAlpha($dbgImage, true);
		$black = imagecolorallocate($dbgImage, 0, 0, 0);
		$text = "DEBUG MODE ENABLED\n";
		$text .= "Cache.....: ". PHP_EOL . (($cacheHit)?"HIT":"MISS") . PHP_EOL;
		$text .= "CacheKey..: ". PHP_EOL . $cacheKey . PHP_EOL;
		$text .= "LoadTime..: ". PHP_EOL . ((microtime(true) - $time) * 1000) . 'ms' . PHP_EOL;
		$text .= "BBOX......: " . PHP_EOL . str_replace(",",PHP_EOL,$get['bbox']) . PHP_EOL;
		$text .= "BBOX Left.: " . PHP_EOL . implode(PHP_EOL, [$x1n,$y1n,$x2n,$y2n]) . PHP_EOL;
		whitespaces_imagestring($dbgImage, 2, 10, 10, $text, $black);
		imagepng($dbgImage);
		imagedestroy($dbgImage);
	}

	
} elseif ($_GET['REQUEST'] !== 'GetMap') {
	header('Content-Type: image/png');
	echo file_get_contents(REMOTE_WMS.'?' . http_build_query($_GET));
}
