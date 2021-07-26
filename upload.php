<?php
require('./vendor/autoload.php');


use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\CommandPool;


$s3 = new S3Client(array(
    'credentials' => array(
        'key' => 'xxxxxxxxx',
        'secret' => 'xxxxxxxxxx'
    ),
    'region' => 'ap-northeast-1',
    'version' => 'latest',
));

$spaceExistence = '^(\s|　)+$';

if (!empty($_POST)) {
    if ($_POST['title'] === '' || mb_ereg_match($spaceExistence, $_POST['title'])) {
        $error['title'] = 'blank';
    }

    if ($_POST['genre'] === 'ジャンル') {
        $error['genre'] = 'blank';
    }

    if ($_POST['varidTime'] >= 31) {
        $error['varidTime'] = 'blank';
    }

    if (empty($error)) {
        
        $soundURL = $s3->getObjectUrl('foliojs', 'sound/' . $_POST['duplicateId']);
        $postTitle = $_POST['title'];
        if ($_FILES['uploadImage']['size'] !== 0) {
            try {
                $result = $s3->putObject(array(
                    'Bucket' => 'foliojs',
                    'Key' => 'image/' . $_FILES['uploadImage']['name'],
                    'SourceFile' => $_FILES['uploadImage']['tmp_name']
                ));
            } catch (S3Exception $e) {
                echo $e->getMessage();
            }

            $imageURL = $result['ObjectURL'];

            $posts = $db->prepare('INSERT INTO posts SET member_id=?, title=?, genre=?, comment=?, soundURL=?, imageURL=?, created=NOW()');
            $posts->execute(array($member['id'], $postTitle, $_POST['genre'], $_POST['comment'], $soundURL, $imageURL));
        } else {
            $posts = $db->prepare('INSERT INTO posts SET member_id=?, title=?, genre=?, comment=?, soundURL=?, created=NOW()');
            $posts->execute(array($member['id'], $postTitle, $_POST['genre'], $_POST['comment'], $soundURL));
        }
    }
}
                    





