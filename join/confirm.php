<?php
require('../dbconnect.php');
session_start();

if (!isset($_SESSION['join'])) {
    header('location: regist.php');
    exit();
}

if (!empty($_POST)) {
    $join = $db->prepare('INSERT INTO members SET user=?, mail=?, password=?, created=NOW()');
    $join->execute(array(
        $_SESSION['join']['user'],
        $_SESSION['join']['mail'],
        sha1($_SESSION['join']['password']),
        
    ));
}

$passLength = strlen($_SESSION['join']['password']);

    $login = $db->prepare('SELECT * FROM members WHERE user=? AND mail=? AND password=?');
    
        $login->execute(array($_SESSION['join']['user'], $_SESSION['join']['mail'], sha1($_SESSION['join']['password'])));

    $user = $login->fetch();

    if ($user) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['user'] = $user['user'];
        $_SESSION['time'] = time();
        header('location: ../index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        .container {
            max-width: 600px;
        }


        body {
            height: 100%;
            background: url(image/daniel-schludi-mbGxz7pt0jM-unsplash.jpg);
            backdrop-filter: sepia(100%);
            -webkit-backdrop-filter: sepia(100%);
        }

        .confirm {
            padding-top: 10rem;
        }

        form {
            background: rgba(255, 255, 255, 0.5);
            height: 460px;
        }

        .submit {
            background-color: #d39e00;
            color: #f3e5e5;
        }

        .return {
            background-color: #a5a992;
            color: #f3e5e5;
        }

        
    </style>
</head>

<body>
    <div class="container confirm">
        <form action="" method="post">
            <input type="hidden" name="submit">
            <h3 class="p-4 text-center">新規登録してもよろしいですか？</h3>
            <dl class="text-center">
                <dt>ユーザーネーム</dt>
                <dd>
                    <?php echo htmlspecialchars($_SESSION['join']['user'], ENT_QUOTES) ?>
                </dd>
                <dt class="pt-3">メールアドレス</dt>
                <dd>
                    <?php echo htmlspecialchars($_SESSION['join']['mail'], ENT_QUOTES) ?>
                </dd>
                <dt class="pt-3">パスワード</dt>
                <dd>
                    <?php for ($i = 1; $i <= $passLength; $i++) : ?>
                        <span>*</span>
                    <?php endfor; ?>
                </dd>
            </dl>
            <div class="row">

                <div class="mt-5 col-lg-12 text-center">
                    <button　type="button" class="btn mr-3"><a href="regist.php?action=rewrite" class="btn return p-3">書き直す</a></button>

                    
                    <input type="submit" class="btn submit p-3 ml-3" value="登録する">
                </div>
            </div>
        </form>
    </div>
</body>
</html>
                


