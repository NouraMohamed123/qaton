<?php

use Illuminate\Support\Facades\Storage;
if (!function_exists('upload')) {
function upload($avatar, $directory)
{
        $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
        $avatar->move($directory, $avatarName);
        return $avatarName;

}
}
//////////////////////////search in area
function pointInPolygon($point, $polygon)
{
    // pointOnVertex = true;

    // Transform string coordinates into arrays with x and y values
    $point = pointStringToCoordinates($point);
    $vertices = array();
    foreach ($polygon as $vertex) {
        $vertices[] = pointStringToCoordinates($vertex);
    }

    // Check if the point sits exactly on a vertex
    if (pointOnVertex($point, $vertices) == true) {
        return true;
    }

    // Check if the point is inside the polygon or on the boundary
    $intersections = 0;

    for ($i = 1; $i < count($vertices); $i++) {
        $vertex1 = $vertices[$i - 1];
        $vertex2 = $vertices[$i];
        if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
            return true;
        }
        if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
            $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
            if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                return true;
            }
            if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                $intersections++;
            }
        }
    }
    // If the number of edges we passed through is odd, then it's in the polygon.
    if ($intersections % 2 != 0) {
        return true;
    } else {
        return false;
    }
}
function pointStringToCoordinates($pointString)
{
    $coordinates = explode(" ", $pointString);
    return array("x" => $coordinates[0], "y" => $coordinates[1]);
}
function pointOnVertex($point, $vertices)
{
    foreach ($vertices as $vertex) {
        if ($point == $vertex) {
            return true;
        }
    }
}
if (!function_exists('checkPoints')) {
    function checkPoints($lat, $lon)
    {
        ini_set('memory_limit', -1);
        ini_set('set_time_limit', -1);
        ini_set('max_execution_time', -1);
        ini_set('post_max_size', -1);
        ini_set('upload_max_filesize', -1);
        $point = $lat . " " . $lon;

        $check = false;
        $area_id = 0;
        $areas = \App\Models\Area::all();
        foreach ($areas as $one) {
            $polygon = [];
            $boundaries = json_decode($one->boundaries);
            foreach ($boundaries as $x) {
                foreach ($x as $o) {
                    $polygon[] = $o[0] . " " . $o[1];
                }
            }
            if (isset($boundaries[0][0][0])) {
                $polygon[] = $boundaries[0][0][0] . " " . $boundaries[0][0][1];

                $check = pointInPolygon($point, $polygon);

                if ($check) {
                    $area_id = $one->id;
                    break;
                }
            }
        }

        if ($check && $area_id > 0) {
            $area = \App\Models\Area::find($area_id);

            if ($area) {
                return $area->id;
            }
            return -1;
        }
        return -1;
    }
}

if (!function_exists('getApartment')) {
    function  getApartment( $lat, $lon)
    {
        $area_id = checkPoints($lat, $lon);
       $apartments = \App\Models\Apartment::where('status', 1);
        if ($area_id > 0) {
            $apartments_area =  $apartments->where('area_id', $area_id)->get();
            if ($apartments_area) {
            return  $apartments_area;
            }

            return $apartments->get();
        }
        return -1;
    }
}


