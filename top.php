<?php
require('./dbconnect.php');
session_start();

if ($_POST['user'] !== null && $_POST['password'] !== null) {
    $login = $db->prepare('SELECT * FROM members WHERE user=? AND password=? OR mail=? AND password=?');
    $reg_str = '/^[^ ]+@[^ ]+\.[a-z]{2,3}$/';
    //メールアドレスかユーザ名かでDBに渡す値を分岐する
    if (preg_match($reg_str, rtrim($_POST['user']))) {
        $login->execute(array('', sha1($_POST['password']), $_POST['user'], sha1($_POST['password'])));
    } else {
        $login->execute(array($_POST['user'], sha1($_POST['password']), '', sha1($_POST['password'])));
    }
    $user = $login->fetch();

    if ($user) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['user'] = $user['user'];
        $_SESSION['time'] = time();
        header('location:index.php');
        exit();
    } else {
        $error['login'] = 'failed';
    }
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
            max-width: 432px;
        }


        body {
            height: 100%;

            background: url(image/daniel-schludi-mbGxz7pt0jM-unsplash.jpg);
            backdrop-filter: sepia(100%);
            -webkit-backdrop-filter: sepia(100%);
        }

        .heading {
            color: #f4f4f4;
            margin-top: 16rem;
            font-size: 20px;
        }

        .acount {
            background-color: #d39e00;
            color: #f3e5e5;
        }

        .btn {
            border-radius: 20px;
        }

        .input_area {
            padding: 35px 16px 8px;
        }

        .input_area input,
        a {
            width: 392px;
            padding: 0.5rem;
            transition: all .6s ease;
            border: none;
            outline: none;
        }

        #login {
            background-color: #d39e00;
            color: #f3e5e5;
        }

    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <p class="heading text-center">アーティスト、クリエイターが一番に聞かせたい<br>瞬間を投稿するサウンドアプリです。</p>
                <a href="regist.php" class="btn col-lg-12 mb-4 acount">アカウント作成</a>
                <button type="button" class="btn btn-light col-lg-12" data-toggle="modal" data-target="#Modal">ログイン</button>
                <form action="" id="login_form" method="post">
                    <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title col-lg-12 text-center" id="Modal">ログインする</h5>
                                </div>
                                <div class="modal-body">
                                    <?php if ($error['login'] === 'failed') : ?>
                                        <span class="col-lg-12 text-center" id="valid">入力されたユーザー名やパスワードが正しくありません。</span>
                                    <?php endif; ?>
                                    <div class="input_area col-lg-12 text-center">
                                        <input type="text" class="border border-primary" name="user" placeholder="メールアドレスまたはユーザーネーム" maxlength="50">
                                    </div>
                                    <div class="input_area col-lg-12 text-center">
                                        <input type="password" class="border border-primary" name="password" placeholder="パスワード">
                                    </div>
                                </div>
                                <div class="modal-footer ">
                                    <button type="submit" class="btn" id="login">ログインする</button>
                                </div>
                            </div>
                        </div>
                </form>

            </div>
        </div>
    </div>









    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        // const form = document.getElementById('login_form');

        // form.addEventListener('submit', (e) => {
           
        //     const postData = new FormData(document.forms.login_form);
        //     for (let setPost of postData) {
        //         postData.set(setPost[0], setPost[1]);
        //         console.log(setPost);
        //     }

        //     const data = {
        //         method: 'post',
        //         body: postData
        //     };

        //     fetch('top.php', data)
        //         .then((res) => {
        //             if (res.status !== 200) {
        //                 throw new Error("system error.");
        //             }
        //             return res.text();
        //         }).then((text) => {
        //             console.log(text);
        //             // location.href = 'main.php'
        //         }).catch((e) => {
        //             console.log(e.message);
        //         }).finally(() => {
        //             console.log('done!')

        //         });
        // });
    </script>

</html>
</body>