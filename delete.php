<?php
require('dbconnect.php');
session_start();
//$_SESSION['id'] = ログインしたユーザのID情報 *DBのmembersテーブルのidカラム
//$_REQUEST['id'] = 投稿したツイートの番号をID化した情報　*DBのpostsテーブルのidカラム

if (isset($_SESSION['id'])) {
    $id = str_replace('L', '', $_REQUEST['id']);
                                                    
    $audios = $db->prepare('SELECT * FROM posts WHERE id=?');
    $audios->execute(array($id));
    $audio = $audios->fetch();
    //$audio['member_id'] = ツイートした番号をID化した情報からそのログインしたユーザのID情報を取得

    if ($audio['member_id'] === $_SESSION['id']) {
        $del = $db->prepare('DELETE FROM posts WHERE id=?');
        $del->execute(array($id));
    }
}

if (strpos($_REQUEST['id'], 'L') === false) {
    header('Location: index.php');
} else {
    header('Location: library.php');
}
    
exit();
