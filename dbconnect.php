<?php
// try {
//     $db = new PDO('mysql:dbname=heroku_4e2474bbf4af012;host=us-cdbr-east-04.cleardb.com;charset=utf8', 'b436e1a741b1a3', '99a5858f');
// } catch(PDOException $e) {
//     echo 'DB接続エラー:' . $e->getMessage();
// }

try {
    $db = new PDO('mysql:dbname=portfolio;host=127.0.0.1;charset=utf8', 'root', 'root');
} catch(PDOException $e) {
    echo 'DB接続エラー:' . $e->getMessage();
}