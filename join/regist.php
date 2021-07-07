<?php
require('../dbconnect.php');
session_start();

$spaceExistence = '^(\s|　)+$';
$reg_str = '/^[^ ]+@[^ ]+\.[a-z]{2,3}$/';




//submit入力時コードを走らせる
if (!empty($_POST)) {
    $rewrite = $_POST;

    if ($_POST['user'] == '' || mb_ereg_match($spaceExistence, $_POST['user'])) {
        $error['user'] = 'blank';
    }

    if ($_POST['mail'] == '' || mb_ereg_match($spaceExistence, $_POST['mail']) || !preg_match($reg_str, rtrim($_POST['mail']))) {
        $error['mail'] = 'blank';
    }

    if (mb_strlen($_POST['password']) < 4 || mb_ereg_match($spaceExistence, $_POST['password'])) {
        $error['password'] = 'blank';
    }

    if (empty($error)) {
        $member = $db->query('SELECT user , mail FROM members');
        while ($members = $member->fetch()) {
            if ($members['user'] === $_POST['user']) {
                $error['same_user'] = 'same';
            }

            if ($members['mail'] === $_POST['mail']) {
                $error['same_mail'] = 'same';
            }
        }
    }

    if (empty($error)) {
        $_SESSION['join'] = $_POST;
        header('location: confirm.php');
        exit();
    }
}


//書き直し時に値をvalue属性に送る
if ($_REQUEST['action'] == 'rewrite') {
    $rewrite = $_SESSION['join'];
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
            max-width: 392px;
        }


        body {
            height: 100%;
            background: url(image/daniel-schludi-mbGxz7pt0jM-unsplash.jpg);
            backdrop-filter: sepia(90%);
            -webkit-backdrop-filter: sepia(100%);
        }

        .login {
            padding-top: 12rem;
        }

        .visibility_hidden {
            height: 24px;
            color: #00f915;
        }

        .input_area {
            padding: 35px 16px 8px;
            background-color: #a78b8b;
            opacity: 0.8;
        }

        form {
            position: relative;
        }

        .input_area input,
        a {
            width: 392px;
            padding: 0.5rem;
            transition: all .6s ease;

            border: none;
            outline: none;
        }

        .regist {
            /* background-color: #d86826; */
            background-color: #d39e00;
            color: #f3e5e5;
            border-radius: 50px;

        }

        .return {
            background-color: #a5a992;
            color: #f3e5e5;
            border-radius: 50px;
        }

        #form.mail_valid .mail_area:before {
            content: '';
            position: absolute;
            right: 0.9px;
            top: 9.4em;
            width: 24px;
            height: 24px;
            background: url(image/valid.png);
            background-size: cover;

        }

        #form.pass_valid .pass_area:before {
            content: '';
            position: absolute;
            right: 1px;
            top: 16em;
            width: 24px;
            height: 24px;
            background: url(image/valid.png);
            background-size: cover;

        }
    </style>
</head>

<body class="bg-light">
    <div id="wrapper ">
        <div class="container login">
            <form id="form" action="" method="post" enctype="multipart/form-data">
                <div class="row justify-content-center">
                    <div class="input_area ">
                        <input type="text" name="user" placeholder="ユーザーネーム" maxlength="20" value="<?php if ($rewrite) echo htmlspecialchars($rewrite['user'], ENT_QUOTES) ?>">
                        <span class="visibility_hidden d-block"><?php if ($error['same_user'] === 'same') : echo 'そのユーザ名は存在してます';
                                                                endif; ?><?php if ($error['user'] === 'blank') : echo 'ユーザ名を入力してください';
                                                                            endif; ?></span>
                    </div>
                    <div class="input_area mail_area">
                        <input type="text" id="mail" name="mail" placeholder="メールアドレス" maxlength="50" value="<?php echo htmlspecialchars($rewrite['mail'], ENT_QUOTES) ?>" onkeyup="checkMail()">
                        <span id="check_email" class="d-block visibility_hidden"><?php if ($error['same_mail'] === 'same') : echo 'そのメールアドレスは存在してます';
                                                                                    endif;
                                                                                    if ($error['mail'] === 'blank') : echo 'メールアドレスを入力してください';
                                                                                    endif; ?></span>
                    </div>
                    <div class="input_area pass_area">
                        <input type="password" id="password" name="password" placeholder="パスワード" onkeyup="checkPassword()">
                        <span id="check_password" class="d-block visibility_hidden"><?php if ($error['password'] === 'blank') : echo 'パスワードを入力してください';
                                                                                    endif; ?></span>
                    </div>
                    <div class="input_area ">
                        <input type="submit" class="btn  text-center p-3 regist" value="新規登録"></button>
                    </div>
                    <div class="input_area ">
                        <a href="top.php" class="btn  text-center p-3 return">戻る</a>
                    </div>
            </form>
        </div>
    </div>
    </div>

    <script>
        const form = document.getElementById('form');

        const checkMail = () => {
            const email = document.getElementById('mail').value.trim();
            const check_email = document.getElementById('check_email');
            const pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

            if (email.match(pattern)) {
                form.classList.add('mail_valid');
                check_email.innerHTML = '';
            } else {
                form.classList.remove('mail_valid');
                check_email.innerHTML = '正しいメールアドレスを入力してください';
            }

            if (email == '') {
                form.classList.remove('mail_valid');
                check_email.innerHTML = '';
            }
        }

        const checkPassword = () => {
            let check_password = document.getElementById('check_password');
            let password = document.getElementById('password').value.trim().length;

            if (password >= 4) {
                form.classList.add('pass_valid');
                check_password.innerHTML = '';
            } else {
                form.classList.remove('pass_valid');
                check_password.innerHTML = '4文字以上で入力してください';

            }

            if (password == 0) {
                check_password.innerHTML = '';
            }
        }
    </script>
</body>

</html>


