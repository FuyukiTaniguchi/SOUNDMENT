<?php
require('./dbconnect.php');
session_start();
//セッションに値が入って、かつログインiDをDBから取得する。
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    header('Location: top.php');
    exit();
}

require('./upload.php');
require('./like.php');
//DB投稿取得　searchに値があるかで分岐
if (empty($_GET) || $_GET['search'] === '') {
    $user = $db->query('SELECT me.user, p.* FROM members me, posts p WHERE me.id=p.member_id ORDER BY p.created DESC');
    $posts = $user;
} elseif ($_GET['search'] !== '') {
    $searchs = $db->prepare('SELECT me.user, p.* FROM members me, posts p WHERE me.id=p.member_id AND title LIKE ? OR user LIKE ?  ORDER BY p.created DESC');
    $searchs->execute(array($_GET['search'] . '%', $_GET['search'] . '%'));
    $posts = $searchs;
}
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

        .sc-input {
            width: 200px;
        }

        .menu_item {
            color: #cccccc;
            margin: 0 2rem 0 2rem;
        }

        .upload {
            position: relative;

        }

        .upload_hidden {
            opacity: 0;
            height: 40px;
            position: absolute;
            width: 130px;
            cursor: pointer !important;
            font-size: 0;
        }

        .file_load {

            background-color: #666479;
            color: #f3e5e5;
            border-radius: 50px;
            border: none;
            height: 40px;
        }

        #change {
            border-radius: 50px;
            height: 40px;
        }

        .upload_image_hidden {

            position: absolute;
            top: 200px;
            left: -130px;
            opacity: 0;
            z-index: 1;
            cursor: pointer !important;
        }

        .image_button {
            width: 210px;
            position: absolute;
            top: 200px;
            left: 0px;
            border-radius: 50px;
            height: 40px;
        }

        @media (max-width:996px) {
            .upload_image_hidden {

                position: absolute;
                top: 100px;
                left: -130px;
                opacity: 0;
                z-index: 1;
                cursor: pointer !important;
            }

            .image_button {
                width: 210px;
                position: absolute;
                top: 100px;
                left: 0px;
                border-radius: 50px;
                height: 40px;
            }
        }

        #submit {
            padding: 0 10px 0 10px;
            background-color: #d39e00;
            color: #f3e5e5;
            border-radius: 5px;
            border: none;
            height: 40px;
        }

        .edit {
            margin-bottom: 20px;
            width: 400px;
            border-radius: 5%;
            border: solid thin #cccccc;

        }

        #top_wrapper {
            background-color: white;

        }

        #reference {
            display: inline-block;
            height: 180px;
            width: 160px;
            object-fit: fill;
        }

        .jacket {
            object-fit: cover;
            width: 60px;
            height: 60px;
        }

        .genre {
            padding: 0.5em 1em;
            color: #FFF;
            display: inline-block;
            width: 76px;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25);
            border-radius: 3px;
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
            margin-top: 3px;
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
                    <li><a class="menu_item text-light" href="index.php">ホーム</a></li>
                    <li><a class="menu_item" href="library.php">ライブラリ</a></li>
                    <form class="headerSearch" method="get">
                        <input class="sc-input" id="search" placeholder="Title User" type="search" name="search">
                        <button type="submit">Search</button>
                    </form>


                    <li><a class="menu_item" href="logout.php">ログアウト</a></li>

                    <li class="menu_item"><?php echo htmlspecialchars($member['user'], ENT_QUOTES) ?></li>
                </ul>
            </nav>
        </div>
    </header>

    <div id="top_wrapper" class="container">
        <div class="pt-4">
            <form action="" id="upload_form" class="pt-5 " method="post" enctype="multipart/form-data">
                <div class="upload mt-4">
                    <input class="upload_hidden offset-9" type="file" id="sound" accept="audio/*" name="sound">
                </div>
                <div id="reUpload"><button id="upload" type="button" class="d-block file_load offset-9" style="width: 128px;">Upload</button></div>
                <h4 id="loading" class="text-center" style="display: none;">読み込み中...</h2>
                    <div id="edit" style="display: none;">
                        <div class="row">
                            <div class="offset-2 col-lg-4 ">
                                <div id="reference" class="ml-2"></div>
                                <span id="varidImage" class="d-block visibility_hidden mr-3"></span>
                                <input class="upload_image_hidden mt-5" id="image" type="file" accept="image/jpeg,image/pjpeg,image/gif,image/png" name="uploadImage" size="45">
                                <button type="button" class="image_button mt-5">UploadImage *</button>
                            </div>
                            <!-- $_POSTでうけとる値 -->
                            <div class="mt-5 col-lg-6">
                                <div></div>
                                <div id="currentTime"></div>
                                <div id="audition"></div>
                                <div id="resize"></div>
                                <span id="varidSlice" class="d-block visibility_hidden"></span>
                                <div id="varidTime"></div>
                                <div id="duration"></div>
                                <div id="duplicateId"></div>
                                <span class="d-block">Title *</span>
                                <input type="text" class="title edit" id="title" name="title" placeholder=" タイトル" maxlength="30">
                                <span id="validTitle" class="d-block visibility_hidden"></span>
                                <span class="d-block">Genre *</span>
                                <select class="edit d-block" name="genre">
                                    <option hidden>ジャンル</option>
                                    <option disabled="disabled">---Music---</option>
                                    <option>Pops</option>
                                    <option>Rock</option>
                                    <option>EDM</option>
                                    <option>Voice</option>
                                </select>
                                <span id="varidGenre" class="d-block visibility_hidden"></span>
                                <input type="text" class="edit  pb-2 mt-4" name="comment" placeholder=" コメント                                                               20" maxlength="20">
                                <button type="submit" id="submit" class="offset-2 mt-3 d-block">公開する</button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>

        <!-- 投稿一覧 -->
        <?php while ($post = $posts->fetch()) : ?>
            <div>
                <div class="row mt-5">
                    <div class="col-sm-3 offset-2">
                        <img class="jacket" src="<?php echo htmlspecialchars($post['imageURL'], ENT_QUOTES) ?>" alt="" size="20">
                        <?php switch ($post['genre']):
                            case 'Pops': ?>
                                <span class="genre" style="background: #6eb7ff; border-bottom: solid 6px #3f87ce;"><?php echo htmlspecialchars($post['genre'], ENT_QUOTES) ?></span>
                                <?php break; ?>
                            <?php
                            case 'Rock': ?>
                                <span class="genre" style="background: #cab065; border-bottom: solid 6px #a28e50;"><?php echo htmlspecialchars($post['genre'], ENT_QUOTES) ?></span>
                                <?php break; ?>
                            <?php
                            case 'EDM': ?>
                                <span class="genre" style="background: #696464; border-bottom: solid 6px #fff1f1;"><?php echo htmlspecialchars($post['genre'], ENT_QUOTES) ?></span>
                                <?php break; ?>
                            <?php
                            case 'Voice': ?>
                                <span class="genre" style="background: #87bd89; border-bottom: solid 6px #77846a;"><?php echo htmlspecialchars($post['genre'], ENT_QUOTES) ?></span>
                                <?php break; ?>
                        <?php endswitch ?>
                        <span class="title"><?php echo htmlspecialchars($post['title'], ENT_QUOTES) . ' - ' . htmlspecialchars($post['user'], ENT_QUOTES) ?></span>

                        <!-- like表示 -->
                        <?php if ($post['user'] !== $member['user']) : ?>
                            <?php for ($i = 0; $i < count($outLike); $i++) : ?>
                                <?php if ($outLike[$i]['favorite_id'] === $post['id'] && $outLike[$i]['favorite'] === 'favorite') : ?>
                                    <?php $icon = 'fas fa-thumbs-up' ?>
                                    <?php $boolean = 'hoge' ?>
                                    <?php break ?>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <form id="like_form" action="" method="post">
                                <span class="hidden_id" style="display: none;"><?php echo htmlspecialchars($post['id'], ENT_QUOTES) ?></span>
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
                    </div>
                    <div class="col-sm-3 offset-2 mt-5" style="filter: drop-shadow(1px 1px 0.1px rgba(1,1,1,.2));">
                        <audio class="audio" src="<?php echo htmlspecialchars($post['soundURL'], ENT_QUOTES) ?>" controls controlslist="nodownload"></audio>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-3 col-sm-6 ">
                        <span class=><?php echo $post['modified'] ?></span>
                        <?php if ($_SESSION['id'] === $post['member_id']) : ?>
                            <a href="delete.php?id=<?php echo htmlspecialchars($post['id'], ENT_QUOTES)?>" onclick=" return confirm('データを削除してもよろしいですか？')"><i class="fas fa-trash fa-lg ml-3 fa-border" id="fas"></i></a>
                        <?php endif ?>
                        <p class="comment"><?php echo htmlspecialchars($post['comment'], ENT_QUOTES) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>



    </div>

    </div>
    <script src="https://unpkg.com/@ffmpeg/ffmpeg@0.9.4/dist/ffmpeg.min.js"></script>
    <script src="https://sdk.amazonaws.com/js/aws-sdk-2.881.0.min.js"></script>
    <script src="function.js"></script>
    <script src="like.js"></script>



    <script>
        const form = document.getElementById('upload_form');
        const sound = document.getElementById('sound');
        const upload = document.getElementById('upload');
        const loading = document.getElementById('loading')
        const reUpload = document.getElementById('reUpload');
        const title = document.getElementById('title');
        const resize = document.getElementById('resize');
        const varidTime = document.getElementById('varidTime');
        const varidSlice = document.getElementById('varidSlice')
        const duplicateId = document.getElementById('duplicateId');
        const audition = document.getElementById('audition');
        const currentTime = document.getElementById('currentTime');

        //fmpegライブラリ　オーディオ形式変換
        const {
            createFFmpeg,
            fetchFile
        } = FFmpeg;
        const ffmpeg = createFFmpeg({
            log: true
        });
        const transcode = async ({
            target: {
                files
            }
        }) => {
            loading.style.display = '';
            await ffmpeg.load();
            ffmpeg.FS('writeFile', 'music', await fetchFile(files[0]));

            await ffmpeg.run('-i', 'music', '-vn', '-ac', '2', '-ar', '44100', '-ab', '256k', '-acodec', 'libmp3lame', '-f', 'mp3', 'output.mp3');
            const data = ffmpeg.FS('readFile', 'output.mp3');

            dataUrl = URL.createObjectURL(new Blob([data.buffer], {
                type: 'audio/wav'
            }));
            audition.innerHTML = '<audio src="' + dataUrl + '"controls id="sample"></audio>';

            const fileTitle = (() => {
                document.getElementById('edit').style.display = ''; //イベントリスナー発火時にエディット画面表示
                const files = sound.files;
                const reader = new FileReader();
                reader.readAsDataURL(files[0]);
                const str = files[0].name.slice(0, -4);
                title.value = str;

                //files[0]読み込んだらedit画面表示
                reader.onload = () => {
                    loading.remove();
                    upload.remove();
                    sound.style.pointerEvents = 'none';
                    reUpload.innerHTML = '<button type="button" id="reload" class="d-block file_load offset-9" style="width: 128px;">再アップロード</button>'
                    document.getElementById('reload').addEventListener('click', () => {
                        location.reload();
                    });

                    const elementReference = document.getElementById('sample');
                    let sliceData = null;


                    const timer = setInterval(() => {
                        if (elementReference.readyState > 0) {

                            if (elementReference.duration <= 30) {
                                sliceData = new Blob([data.buffer], {
                                    type: "audio/mp3"
                                });
                                clearInterval(timer);

                            } else {
                                currentTime.innerHTML = '<input type="range" value="0" id="sampleCurrentTime" min="0"   step="1"  onchange="setCurrentTime(this.value)">';
                                varidSlice.innerHTML = '* ピックアップする30秒を選択してください';
                                const sampleCurrentTime = document.getElementById('sampleCurrentTime');
                                sampleCurrentTime.max = elementReference.duration; //デフォルトでrangeの総再生時間が反映されないのでそれを処理

                                varidTime.innerHTML = '<input type="hidden" value="' + elementReference.duration + '" name="varidTime">'
                                clearInterval(timer);
                                resize.innerHTML = '<button type="button" id=change>30秒ピックアップ</button>'
                                const change = document.getElementById('change');


                                change.addEventListener('click', () => {
                                    varidTime.innerHTML = '<input type="hidden" value="30" name="varidTime">'
                                    varidSlice.remove();
                                    const byte = {
                                        'start': Math.round((elementReference.currentTime + 0.1) * 0.03 * 1024 * 1024),
                                        'end': Math.round((elementReference.currentTime + 31) * 0.03 * 1024 * 1024)
                                    };

                                    sliceData = new Blob([data.buffer], {
                                        type: "audio/mp3"
                                    }).slice(byte.start, byte.end, 'audio/mp3');

                                    const reader = new FileReader();
                                    reader.readAsDataURL(sliceData);
                                    reader.onload = () => {
                                        const dataURI = reader.result;
                                        audition.innerHTML = '<audio src="' + dataURI + '"controls id="sample"></audio>';
                                        sampleCurrentTime.remove();
                                        change.remove();
                                    }
                                });
                            }
                        }
                    }, 300);

                    //アップロードbuttonサブミット時
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const postForm = new FormData(document.forms.upload_form);
                        let varidCheck = null;
                        const checkImage = document.getElementById('checkImage');

                        if (checkImage === null) {
                            document.getElementById('varidImage').innerHTML = '* イメージ画像を選択してください'
                            varidCheck = false;
                        }

                        if (postForm.get('title').trim() === '') {
                            document.getElementById('validTitle').innerHTML = '* タイトルは必須項目です';
                            varidCheck = false;
                        }

                        if (postForm.get('genre') === 'ジャンル') {
                            document.getElementById('varidGenre').innerHTML = '* ジャンルは必須項目です';
                            varidCheck = false;
                        }

                        if (sliceData === null) {

                            varidCheck = false;
                        }

                        if (varidCheck === null) {
                            const s3_client = () => {
                                AWS.config.region = 'ap-northeast-1';
                                AWS.config.credentials = new AWS.CognitoIdentityCredentials({
                                    IdentityPoolId: 'ap-northeast-1:6a49d588-bfb2-4341-9776-724224e30e6d'

                                });
                                AWS.config.credentials.get((err) => {
                                    if (!err) {
                                        console.log('Cognito Identify Id: ' + AWS.config.credentials.identityId);
                                    }
                                });
                                return new AWS.S3({
                                    params: {
                                        Bucket: 'foliojs'
                                    }
                                });
                            };

                            const filename = duplicate(); //乱数生成
                            duplicateId.innerHTML = '<input type="hidden" name="duplicateId" value="' + filename + '">'

                            //S3にBlobデータ渡す
                            s3_client().putObject({
                                    Key: 'sound/' + filename,
                                    ContentType: 'audio/mp3',
                                    Body: sliceData,
                                    ACL: "public-read"
                                },
                                (err, data) => {
                                    console.log(data + 'helloworld');
                                    if (data !== null) {
                                        alert("アップロード成功!");
                                    } else {
                                        alert("アップロード失敗.");
                                        exit();
                                    }
                                });

                            postForm.set('duplicateId', filename);

                            const form = {
                                method: 'post',
                                body: postForm
                            }

                            fetch('index.php', form)
                                .then((res) => {
                                    if (res.status !== 200) {
                                        throw new Error("system error.");
                                    }
                                    return res.text();
                                }).then((text) => {
                                    console.log(text);
                                }).catch((e) => {
                                    console.log(e.message);
                                }).finally(() => {
                                    location.reload();
                                });
                        }
                    });
                };
            })
            fileTitle();
        }

        sound.addEventListener('change', transcode);

        //カーソルバーを動かした時、時間位置をaudioタグに代入
        const setCurrentTime = (currentTime) => {
            const elementReference = document.getElementById('sample');
            console.log(elementReference.currentTime = currentTime);

        }

        //イメージ画像参照
        const image = document.getElementById('image');
        let dataUrl;
        const attachedImage = () => {
            const reference = image.files;
            const reader = new FileReader();

            reader.readAsDataURL(reference[0]);
            reader.onload = () => {
                dataUrl = reader.result;
                console.log(dataUrl);
                document.getElementById('reference').innerHTML = "<img src='" + dataUrl + "' id='checkImage'  alt='none'>";
            };

        }
        image.addEventListener('change', attachedImage);

        //audioタグ同時再生させない
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