<?php
// Copyright (c) 2017 YA-androidapp(https://github.com/YA-androidapp) All rights reserved.
header('Content-Type: text/javascript; name="data.js"');
error_reporting(0);
$pw = filter_input(INPUT_POST, 'pw');
require('auth.php');
if( !is_string($pw) || (md5($pw) != $PASSWORD) ){
  die('不正な引数です。1');
}
echo '[';
try {
  $db = new SQLite3('places.db');
} catch (Exception $e) {
  print 'DBへの接続でエラーが発生しました。<br>';
  print $e->getTraceAsString();
}

$sql = "create table if not exists places (id int, name text, lat real, lng real)";
$result = $db->query($sql);
if (!$result) {
  $db->close();
  die('クエリーが失敗しました。'.$sqliteerror);
}

$sql = "select name, lat, lng from places where id in (select max(id) from places group by name)";
$results = $db->query($sql);
if (!$results) {
  $db->close();
  die('クエリーが失敗しました。'.$sqliteerror);
}
$count = 0;
while ($row = $results->fetchArray()) {
  if($count > 0){
    echo ',';
  }

  echo json_encode(
    array(
      'name' => $row['name'],
      'lat' => $row['lat'],
      'lng' => $row['lng']
      ),
    JSON_UNESCAPED_UNICODE
    );

  $count++;
}

// 切断
$db->close();

echo ']';
