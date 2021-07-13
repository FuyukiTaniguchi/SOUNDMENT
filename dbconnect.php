<?php
try {
    $db = new PDO('mysql:dbname=heroku_4accf9452106b52;host=us-cdbr-east-04.cleardb.com;charset=utf8', 'b248fbbdd08c1d', '401ae39e');
} catch(PDOException $e) {
    echo 'DB接続エラー:' . $e->getMessage();
}

// try {
//     $db = new PDO('mysql:dbname=portfolio;host=127.0.0.1;charset=utf8', 'root', 'root');
// } catch(PDOException $e) {
//     echo 'DB接続エラー:' . $e->getMessage();
// }