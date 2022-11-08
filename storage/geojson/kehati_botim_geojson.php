<?php
/*
 * Title:   MySQL Points to GeoJSON
 * Notes:   Query a MySQL table or view of points with x and y columns and return the results in GeoJSON format, suitable for use in OpenLayers, Leaflet, etc.
 * Author:  Bryan R. McBride, GISP
 * Contact: bryanmcbride.com
 * GitHub:  https://github.com/bmcbride/PHP-Database-GeoJSON
 */

 # Connect to MySQL database
include('awal.php');
# Build SQL SELECT statement including x and y columns
//$sql = 'SELECT *, lng AS x, lat AS y  FROM skp where kategori = "Kehati"';

$sql = "SELECT skp.skp_id, skp.tahun_pendataan, skp.kategori, skp.nama_lokasi, k.kelurahan_id, k.kecamatan_id, k.kelurahan_nama, c.kecamatan_nama, skp.foto, skp.lat, skp.lng, skp.icon_map, skp.`marker-color`, skp.`marker-symbol`, skp.`marker-size`,CONCAT('<b>LOKASI KEHATI</b><br><img src=$path/konservasi/kehati/',skp.foto,' width=250',' <br><br>',skp.nama_lokasi,'<br>',skp.tahun_pendataan,'<br>',c.kecamatan_nama,'<br>',k.kelurahan_nama) As description, skp.lng AS x, skp.lat AS y 
FROM skp skp, kelurahan k, kecamatan c where skp.kategori = 'Kehati' AND skp.kelurahan_id = k.kelurahan_id AND k.kecamatan_id = c.kecamatan_id AND c.kecamatan_id =4";
/*
* If bbox variable is set, only return records that are within the bounding box
* bbox should be a string in the form of 'southwest_lng,southwest_lat,northeast_lng,northeast_lat'
* Leaflet: map.getBounds().pad(0.05).toBBoxString()
*/
if (isset($_GET['bbox']) || isset($_POST['bbox'])) {
    $bbox = explode(',', $_GET['bbox']);
    $sql = $sql . ' WHERE x <= ' . $bbox[2] . ' AND x >= ' . $bbox[0] . ' AND y <= ' . $bbox[3] . ' AND y >= ' . $bbox[1];
}

# Try query or error
$rs = $conn->query($sql);
if (!$rs) {
    echo 'An SQL error occured.\n';
    exit;
}

# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);

# Loop through rows to build feature arrays
while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
    $properties = $row;
    # Remove x and y fields from properties (optional)
    unset($properties['lat']);
    unset($properties['lng']);
    $feature = array(
        'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            'coordinates' => array(
                $row['x'],
                $row['y']
            )
        ),
        'properties' => $properties
    );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}

header('Content-type: application/json');
//echo json_encode($geojson, JSON_NUMERIC_CHECK);
echo json_encode($geojson, JSON_UNESCAPED_SLASHES);
$conn = NULL;
?>
