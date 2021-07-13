<?php
require('./dbconnect.php');
session_start();

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    header('Location:top.php');
    exit();
}

require('./like.php');

$posts = $db->prepare('SELECT me.user, p.* FROM members me, posts p WHERE me.id=p.member_id AND me.user=? ORDER BY p.created DESC');
$posts->execute(array($member['user']));


$favorites = $db->prepare('SELECT me.user, p.*, f.* FROM members me, posts p, favorites f WHERE me.id=p.member_id AND f.account=? AND f.favorite="favorite" AND p.id=f.favorite_id ORDER BY f.id DESC');
$favorites->execute(array($member['user']));


?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <style>
        * {
            list-style: none;
            box-sizing: border-box;
            margin: 0px;
            padding: 0px;
            font-family: 'Poppins', sans-serif;
            white-space: nowrap;


        }

        body {
            background: #f2f2f2
        }

        li .menu_item {
            text-decoration: none;
        }

        li .menu_item:hover {
            color: #f3e5e5;
        }

        .container {
            max-width: 1180px;
        }

        ul {
            list-style: none;
        }

        header {
            background: #333;
            height: 56px;
            width: 100%;
            position: fixed;
            z-index: 1000;
        }



        .menu_item {
            color: #cccccc;
            margin: 0 2rem 0 2rem;
        }


        #top_wrapper {
            background-color: white;

        }

        .heading {
            padding-top: 8rem;
        }

        .list {
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            padding-top: 4rem;

        }

        .item {
            display: inline-block;
            width: 350px;
            height: 200px;
        }

        .comment {
            padding: 0.5em 0em;
            margin: 1em 0;
            color: #5d627b;
            background: #adbdbb1a;
            border-bottom: solid 1px #c0c2cc;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.22);
        }

        .fas {
            color: #d87f87;
            cursor: pointer;
        }

        .like__btn {
            padding: 3px 6px;
            background: #0080ff;
            font-size: 18px;
            font-family: "Open Sans", sans-serif;
            border-radius: 50px;
            color: #e8efff;
            outline: none;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <nav>
                <ul class="d-flex  justify-content-center mt-3">
                    <li><a class="menu_item" href="index.php">ホーム</a></li>
                    <li><a class="menu_item text-light" href="library.php">ライブラリ</a></li>
                    <li><a class="menu_item" href="logout.php">ログアウト</a></li>
                    <li class="menu_item"><?php echo htmlspecialchars($member['user'], ENT_QUOTES) ?></li>
                </ul>
            </nav>
        </div>
    </header>
        
    <div id="wrapper">
        <div class="container">
            <h2 class="heading">Myアップロード</h2>
            <ul class="list" style="background: white;">
                <?php while ($myUpload = $posts->fetch()) : ?>
                    <li class="item">
                        <div id="main" class="col-sm-3 offset-2">
                            <img class="jacket" src="<?php echo htmlspecialchars($myUpload['imageURL'], ENT_QUOTES) ?>" alt="" size="10" style="height: 40px; width: 40px;">
                            <?php switch ($myUpload['genre']):
                                case 'Pops': ?>
                                    <span class="genre" style="background: #6eb7ff; border-bottom: solid 6px #3f87ce;"><?php echo $myUpload['genre'] ?></span>
                                    <?php break; ?>
                                <?php
                                case 'Rock': ?>
                                    <span class="genre" style="background: #cab065; border-bottom: solid 6px #a28e50;"><?php echo $myUpload['genre'] ?></span>
                                    <?php break; ?>
                                <?php
                                case 'EDM': ?>
                                    <span class="genre" style="background: #696464; border-bottom: solid 6px #fff1f1;"><?php echo $myUpload['genre'] ?></span>
                                    <?php break; ?>
                                <?php
                                case 'Voice': ?>
                                    <span class="genre" style="background: #6eb7ff; border-bottom: solid 6px #3f87ce;"><?php echo $myUpload['genre'] ?></span>
                                    <?php break; ?>
                            <?php endswitch ?>
                            <span class="title"><?php echo htmlspecialchars($myUpload['title'], ENT_QUOTES) ?></span>

                        </div>
                        <div class="col-sm-3 offset-2">
                            <audio class="audio" src="<?php echo htmlspecialchars($myUpload['soundURL'], ENT_QUOTES) ?>" controls controlslist="nodownload"></audio>
                        </div>
                        <div class="offset-3 col-sm-6">
                            <span class=><?php echo $myUpload['modified'] ?></span>
                            <?php if ($_SESSION['id'] === $myUpload['member_id']) : ?>
                                <a href="delete.php?id=<?php echo htmlspecialchars($myUpload['id'], ENT_QUOTES)?>L" onclick=" return confirm('データを削除してもよろしいですか？')"><i class="fas fa-trash fa-lg ml-3 fa-border" id="fas"></i></a>
                            <?php endif ?>
                        </div>
                    </li>
                    <?php $checkUpload = '' ?>
                <?php endwhile; ?>
                <?php if (!isset($checkUpload)) : ?>
                    <h3 class="text-center">まだ投稿がありません</h3>
                <?php endif; ?>
            </ul>
        </div>

        <div class="container pt-5">
            <h2>favorite</h2>
            <ul class="list" style="background: white;">
                <?php while ($favorite = $favorites->fetch()) : ?>
                    <li class="item">
                        <div id="main" class="col-sm-3 offset-2">
                            <img class="jacket" src="<?php echo htmlspecialchars($favorite['imageURL'], ENT_QUOTES) ?>" alt="" size="10" style="height: 40px; width: 40px;">
                            <?php switch ($favorite['genre']):
                                case 'Pops': ?>
                                    <span class="genre" style="background: #6eb7ff; border-bottom: solid 6px #3f87ce;"><?php echo $favorite['genre'] ?></span>
                                    <?php break; ?>
                                <?php
                                case 'Rock': ?>
                                    <span class="genre" style="background: #cab065; border-bottom: solid 6px #a28e50;"><?php echo $favorite['genre'] ?></span>
                                    <?php break; ?>
                                <?php
                                case 'EDM': ?>
                                    <span class="genre" style="background: #696464; border-bottom: solid 6px #fff1f1;"><?php echo $favorite['genre'] ?></span>
                                    <?php break; ?>
                                <?php
                                case 'Voice': ?>
                                    <span class="genre" style="background: #6eb7ff; border-bottom: solid 6px #3f87ce;"><?php echo $favorite['genre'] ?></span>
                                    <?php break; ?>
                            <?php endswitch ?>
                            <span class="title"><?php echo htmlspecialchars($favorite['title'], ENT_QUOTES) . ' - ' . htmlspecialchars($favorite['user'], ENT_QUOTES) ?></span>

                        </div>
                        <div class="col-sm-3 offset-2">
                            <audio class="audio" src="<?php echo htmlspecialchars($favorite['soundURL'], ENT_QUOTES) ?>" controls controlslist="nodownload"></audio>
                        </div>
                        <div class="col-sm-3 offset-2">
                            <?php if ($favorite['user'] !== $member['user']) : ?>
                                <?php for ($i = 0; $i < count($outLike); $i++) : ?>
                                    <?php if ($outLike[$i]['favorite_id'] === $favorite['favorite_id'] && $outLike[$i]['favorite'] === 'favorite') : ?>
                                        <?php $icon = 'fas fa-thumbs-up' ?>
                                        <?php $boolean = 'hoge' ?>
                                        <?php break ?>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <form id="like_form" action="" method="post">
                                    <span class="hidden_id" style="display: none;"><?php echo htmlspecialchars($favorite['favorite_id'], ENT_QUOTES) ?></span>
                                    <input type="hidden" name="user" value="<?php echo htmlspecialchars($member['user'], ENT_QUOTES) ?>">
                                    <?php if (isset($boolean)) : ?>
                                        <span class="hidden_boolean" style="display: none;">true</span>
                                    <?php else : ?>
                                        <span class="hidden_boolean" style="display: none;">false</span>
                                    <?php endif; ?>
                                </form>
                                <?php if (isset($icon)) : ?>
                                    <button class="like__btn">
                                        <span class="icon"><i class="<?php echo $icon ?>"></i></span>
                                        Like
                                    </button>
                                <?php else : ?>
                                    <button class="like__btn">
                                        <span class="icon"><i class="far fa-thumbs-up"></i></span>
                                        Like
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php unset($icon);
                            unset($boolean) ?>
                            <span class="comment"><?php echo htmlspecialchars($favorite['comment'], ENT_QUOTES) ?></span>
                        </div>
                    </li>
                    <?php $checkFavorite = '' ?>
                <?php endwhile; ?>
                <?php if (!isset($checkFavorite)) : ?>
                    <h3 class="text-center">Likeした投稿がありません</h3>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <script src="like.js"></script>
    <script>
        let audios = document.querySelectorAll('audio');
        for (let i = 0; i < audios.length; i++) {
            audios[i].addEventListener('play', function() {
                for (let j = 0; j < audios.length; j++) {
                    if (audios[j] != this) {
                        audios[j].pause()
                    }
                }
            }, false);
        }
    </script>
</body>

</html>