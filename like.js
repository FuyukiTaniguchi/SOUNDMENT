const likeBtn = document.querySelectorAll('.like__btn');
const hidden_id = document.querySelectorAll('.hidden_id');
const hidden_boolean = document.querySelectorAll('.hidden_boolean');
let likeIcon = document.querySelectorAll('.icon');

for (let i = 0; i < likeBtn.length; i++) {
    likeBtn[i].addEventListener('click', () => {
        let clicked = false;
        const postForm = new FormData(document.forms.like_form);
        //カラムfavoriteだった場合true
        if (hidden_boolean[i].textContent === 'true') {
            clicked = true;
        }

        console.log(clicked);
        //likeおされてない場合
        if (clicked === false) {
            clicked = true;
            likeIcon[i].innerHTML = `<i class="fas fa-thumbs-up"></i>`;
            hidden_boolean[i].innerHTML = 'true';
            postForm.set('favorite_id', hidden_id[i].textContent);
            postForm.set('favorite', 'favorite');
            console.log(hidden_id[i].textContent);
            const form = {
                method: 'post',
                body: postForm
            };

            fetch('index.php', form)
                .then((res) => {
                    if (res.status !== 200) {
                        throw new Error("system error.");
                    }
                    return res.text();
                }).then((text) => {
                    // console.log(text);
                }).catch((e) => {
                    console.log(e.message);
                }).finally(() => {
                    console.log('done!')

                });
        } else {
            clicked = false;
            likeIcon[i].innerHTML = `<i class="far fa-thumbs-up"></i>`;
            hidden_boolean[i].innerHTML = 'false';
            postForm.set('favorite_id', hidden_id[i].textContent);
            postForm.set('favorite', 'notFavorite');
            const form = {
                method: 'post',
                body: postForm
            };

            fetch('index.php', form)
                .then((res) => {
                    if (res.status !== 200) {
                        throw new Error("system error.");
                    }
                    return res.text();
                }).then((text) => {
                    // console.log(text);
                }).catch((e) => {
                    console.log(e.message);
                }).finally(() => {
                    console.log('done!')
                });
        }
    });
}
