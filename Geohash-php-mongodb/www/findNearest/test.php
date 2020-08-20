<?php

echo "hello";
echo "<br>";

if($socket=fsockopen("0.0.0.0", 27017, $errno, $errstr,30))
{
  echo "Online!";
  fclose($socket);
}
else {
  echo "Offline!";
  fclose($socket);
}

$manager = new \MongoDB\Driver\Manager("mongodb://127.0.0.1:27017/CourierGeoHash");

var_dump($manager);
echo "<br>";

$bulk = new \MongoDB\Driver\BulkWrite;

$doc = [
  'test' => 'test data'
];

$bulk->insert($doc);

$result = $manager->executeBulkWrite('db.collection', $bulk);

echo $result;
echo "<br>";

?>
