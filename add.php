<?php
// Copyright (c) 2017 YA-androidapp(https://github.com/YA-androidapp) All rights reserved.
header('Content-Type: text/javascript; name="add.js"');
error_reporting(0);
$pw = filter_input(INPUT_POST, 'pw');
require('auth.php');
if( !is_string($pw) || (md5($pw) != $PASSWORD) ){
  die('不正な引数です。1');
}

$name = filter_input(INPUT_POST, 'name');
$lat  = filter_input(INPUT_POST, 'lat');
$lng = filter_input(INPUT_POST, 'lng');
if( !is_string($name) || !is_numeric($lat) || !is_numeric($lng) ){
  die('不正な引数です。2');
}
$name = SQLite3::escapeString($name);

try {
  $db = new SQLite3('places.db');
} catch (Exception $e) {
  print 'DBへの接続でエラーが発生しました。<br>';
  print $e->getTraceAsString();
  $db->close();
  die();
}

$sql = "create table if not exists places (id int, name text, lat real, lng real)";
$result = $db->query($sql);
if (!$result) {
  $db->close();
  die('クエリーが失敗しました。'.$sqliteerror);
}

$sql = "insert into places(name, lat, lng) values( ? , ? , ? )";
$stmt = $db->prepare($sql);
$stmt->bindValue(1,        $name, SQLITE3_TEXT);
$stmt->bindValue(2, (float)$lat , SQLITE3_FLOAT);
$stmt->bindValue(3, (float)$lng , SQLITE3_FLOAT);
$result = $stmt->execute();
if (!$result) {
  $db->close();
  die('クエリーが失敗しました。'.$sqliteerror);
}

// 切断
$db->close();
