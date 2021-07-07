
<?php
$outLikes = $db->prepare('SELECT * FROM favorites WHERE account=? AND favorite="favorite"');
$outLikes->execute(array($member['user']));
$outLike = $outLikes->fetchAll();

$records = $db->query('SELECT * FROM favorites');
$records->execute();
$record = $records->fetchAll();

$counts = $db->query('SELECT COUNT(*) FROM favorites');
$counts->execute();
$count = $counts->fetch();

if (isset($_POST['favorite'])) {
    for ($i = 0; $i < intval($count[0]); $i++) {
        //タップしたbuttonがすでに存在するfavorite_idか
        if ($record[$i]['favorite_id'] === $_POST['favorite_id'] && $record[$i]['account'] === $_POST['user']) {
            $duplicateFa = true;
        }
    }
    
    if ($duplicateFa) {
        $postLikes = $db->prepare('UPDATE favorites SET favorite=? WHERE favorite_id=? AND account=?');
        $postLikes->bindParam(1, $_POST['favorite'], PDO::PARAM_STR);
        $postLikes->bindParam(2, $_POST['favorite_id'], PDO::PARAM_INT);
        $postLikes->bindParam(3, $_POST['user'], PDO::PARAM_STR);
        $postLikes->execute();
    } else {
        $postLikes = $db->prepare('INSERT INTO favorites SET favorite_id=?, account=?, favorite=?');
        $postLikes->bindParam(1, $_POST['favorite_id'], PDO::PARAM_INT);
        $postLikes->bindParam(2, $_POST['user'], PDO::PARAM_STR);
        $postLikes->bindParam(3, $_POST['favorite'], PDO::PARAM_STR);
        $postLikes->execute();
    }
}

?>