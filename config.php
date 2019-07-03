<?php

const DEBUG = false;

const CACHE_PATH = '/var/www/cache/';
const TEMP_PATH = '/var/www/temp/';

const LOCAL_WMS = 'https://ohdmcache.f4.htw-berlin.de/';
const REMOTE_WMS = 'http://ohm.f4.htw-berlin.de:8080/geoserver/ohdm_t/wms';
$wmsLayers = [
	'ohdm_t:landuse_brown',
	'ohdm_t:landuse_commercialetc',
	'ohdm_t:landuse_freegreenandwood',
	'ohdm_t:landuse_gardeningandfarm',
	'ohdm_t:landuse_grey',
	'ohdm_t:landuse_industrial',
	'ohdm_t:landuse_military',
	'ohdm_t:landuse_residentaletc',
	'ohdm_t:landuse_transport',
	'ohdm_t:landuse_water',
	'ohdm_t:landuse_brown_label',
	'ohdm_t:landuse_commercialetc_label',
	'ohdm_t:landuse_freegreenandwood_label_label',
	'ohdm_t:landuse_gardeningandfarm_label',
	'ohdm_t:landuse_grey_label',
	'ohdm_t:landuse_industrial_label',
	'ohdm_t:landuse_military_label',
	'ohdm_t:landuse_residentaletc_label',
	'ohdm_t:landuse_transport_label',
	'ohdm_t:landuse_water_label',
	'ohdm_t:building_polygons',
	'ohdm_t:natural_polygons',
	'ohdm_t:military_polygons',
	'ohdm_t:waterway_polygons',
	'ohdm_t:geological_polygons',
	'ohdm_t:aeroway_polygons',
	'ohdm_t:emergency_polygons',
	'ohdm_t:building_polygons_label',
	'ohdm_t:natural_polygons_label',
	'ohdm_t:military_polygons_label',
	'ohdm_t:waterway_polygons_label',
	'ohdm_t:geological_polygons_label',
	'ohdm_t:aeroway_polygons_label',
	'ohdm_t:emergency_polygons_label',
	/*'ohdm_t:boundaries_admin_2',
	'ohdm_t:boundaries_admin_3',
	'ohdm_t:boundaries_admin_4',
	'ohdm_t:boundaries_admin_5',
	'ohdm_t:boundaries_admin_6',
	'ohdm_t:boundaries_admin_7',
	'ohdm_t:boundaries_admin_8',
	'ohdm_t:boundaries_admin_9',
	'ohdm_t:boundaries_admin_10',*/
	'ohdm_t:highway_huge_lines',
	'ohdm_t:highway_primary_lines',
	'ohdm_t:highway_secondary_lines',
	'ohdm_t:highway_small_lines',
	'ohdm_t:highway_tertiary_lines',
	'ohdm_t:highway_path_lines',
	'ohdm_t:railway_lines',
	'ohdm_t:shop_points',
	'ohdm_t:public_transport_points',
	'ohdm_t:natural_points',
	'ohdm_t:aeroway_points',
	'ohdm_t:craft_points'
];

/**
 * Alle relevanten Parameter für das Caching. Lowercase.
 */
$relevantParameters = [
	'date',
	'service',
	'version',
	'request',
	'format',
	'transparent',
	'tiled',
	'layers',
	'styles',
	'width',
	'height',
	'srs',
	'bbox'
];

/*
Helpers
*/
/**
* @author Booteille
*
* @param resource $image
* @param int $font
* @param int $x
* @param int $y
* @param string $string
* @param int $color
*/
function whitespaces_imagestring($image, $font, $x, $y, $string, $color) {
    $font_height = imagefontheight($font);
    $font_width = imagefontwidth($font);
    $image_height = imagesy($image);
    $image_width = imagesx($image);
    $max_characters = (int) ($image_width - $x) / $font_width ;
    $next_offset_y = $y;

    for($i = 0, $exploded_string = explode("\n", $string), $i_count = count($exploded_string); $i < $i_count; $i++) {
        $exploded_wrapped_string = explode("\n", wordwrap(str_replace("\t", "    ", $exploded_string[$i]), $max_characters, "\n"));
        $j_count = count($exploded_wrapped_string);
        for($j = 0; $j < $j_count; $j++) {
            imagestring($image, $font, $x, $next_offset_y, $exploded_wrapped_string[$j], $color);
            $next_offset_y += $font_height;

            if($next_offset_y >= $image_height - $y) {
                return;
            }
        }
    }
}