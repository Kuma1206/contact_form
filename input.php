<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>問合せフォーム</title>
    <link rel="stylesheet" type="text/css" href="css/input.css" />
    <style>
        .error-message {
            color: red;
            display: none;
            margin-top: 10px;
        }
        #confirmation-screen {
            display: none;
        }
    </style>
</head>

<body>

<main>

    <form id="inquiry-form" action="create.php" method="POST">
        <h2>お問合せはこちら</h2>

        <div id="" class="t-box">
            <p class="case">お名前</p>
            <div>
                <input type="text" class="info-box" name="shimei" required>*
            </div>
        </div>

        <div id="" class="t-box">
            <p class="case">メールアドレス</p>
            <div>
                <input type="email" class="info-box" name="mail" required>*
            </div>
        </div>

        <div id="" class="t-box">
            <p class="case">ご意見</p>
            <div>
                <select name="goiken" class="select-box" required>
                    <option value="">選択してください</option>
                    <option value="サービス全般">サービス全般</option>
                    <option value="機能について">機能について</option>
                    <option value="プランについて">プランについて</option>
                    <option value="その他ご要望">その他ご要望</option>
                </select>*
            </div>
        </div>

        <div id="" class="t-box">
            <p class="case">お問合せ内容</p>
            <div>
                <textarea id="text-box" name="naiyou" required></textarea>*
            </div>
        </div>

        <div id="checkbox">
            <label>
                <input type="checkbox" required>個人情報の取扱いに関する内容に同意する</label>
        </div>
        <div id="kakunin">
            <button type="button" id="confirm-button">確 認</button>
        </div>
        <div id="error-message" class="error-message">
            ※必須項目が入力されていません
        </div>  
    </form>

    <div id="confirmation-screen">
        <h3>確認画面</h3>
        <p>お名前: <span id="confirm-shimei" class="kauningamen"></span></p>
        <p>メールアドレス: <span id="confirm-mail" class="kauningamen"></span></p>
        <p>ご意見: <span id="confirm-goiken" class="kauningamen"></span></p>
        <p>お問合せ内容: <div id="confirm-naiyou" class="naiyou"></div></p>
        <div id="b-button">
            <button type="button" id="edit-button" class="h-button">編 集</button>
            <button type="submit" id="submit-button" class="h-button">送 信</button>
        </div>
    </div>

</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

const textBox = document.getElementById('text-box');

// エラーメッセージを初期状態で非表示にする
$('#error-message').hide();

$('#confirm-button').click(function() {
    // フォームのバリデーションを行う
    if (validateForm()) {
        // 確認画面を表示する
        showConfirmation();
        // エラーメッセージを非表示にする
        $('#error-message').hide();
    } else {
        // エラーメッセージを表示する
        $('#error-message').show();
    }
});

$('#edit-button').click(function() {
    $('#confirmation-screen').hide();
    $('#inquiry-form').show();
});

$('#submit-button').click(function() {
    $('#inquiry-form').submit();
});

// カーソルを一番上に移動する関数
function moveCursorToTop() {
    textBox.focus(); // フォーカスをテキストボックスに移動
    textBox.setSelectionRange(0, 0); // カーソル位置を先頭に設定
}

// テキストボックスをクリックしたらカーソルを一番上に移動する
textBox.addEventListener('click', moveCursorToTop);

// 初期状態でもカーソルを一番上に移動する
moveCursorToTop();


// テキストボックスで入力があったときの処理
textBox.addEventListener('input', function(event) {
    const maxCharacters = 21; // 各行の最大文字数
    const key = event.data;

    let lines = textBox.value.split('\n');
    for (let i = 0; i < lines.length; i++) {
        if (lines[i].length > maxCharacters) {
            let exceededCharacters = lines[i].length - maxCharacters;
            lines[i] = lines[i].substring(0, maxCharacters);
            lines.splice(i + 1, 0, lines[i].substring(maxCharacters));
            textBox.value = lines.join('\n');
            let cursorPosition = textBox.selectionStart + exceededCharacters;
            textBox.setSelectionRange(cursorPosition, cursorPosition);
        }
    }

    // Enter キーを押した場合、または自動的に改行された場合
    if (key === '\n' || event.inputType === 'insertLineBreak') {
        let cursorPosition = textBox.selectionStart;
        let textBeforeCursor = textBox.value.substring(0, cursorPosition);
        let textAfterCursor = textBox.value.substring(cursorPosition);
        textBox.value = textBeforeCursor + '\n' + textAfterCursor;
        textBox.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
    }
});

function validateForm() {
    // 必須項目の入力チェック
    let shimei = $('#inquiry-form input[name="shimei"]').val().trim();
    let mail = $('#inquiry-form input[name="mail"]').val().trim();
    let goiken = $('#inquiry-form select[name="goiken"]').val();
    let naiyou = $('#inquiry-form textarea[name="naiyou"]').val().trim();
    let checkbox = $('#checkbox input[type="checkbox"]').prop('checked');

    let isValid = true;

    // 氏名の入力チェック
    if (shimei === '') {
        $('#error-message').text('※氏名が入力されていません。');
        isValid = false;
    }
    // メールアドレスの入力チェック
    if (mail === '') {
        $('#error-message').text('※メールアドレスが入力されていません。');
        isValid = false;
    }
    // ご意見の入力チェック
    if (goiken === '') {
        $('#error-message').text('※ご意見を選択してください。');
        isValid = false;
    }
    // お問合せ内容の入力チェック
    if (naiyou === '') {
        $('#error-message').text('※お問合せ内容が入力されていません。');
        isValid = false;
    }
    // チェックボックスのチェック
    if (!checkbox) {
        $('#error-message').text('※個人情報の取扱いについて確認してください。');
        isValid = false;
    }

    return isValid;
}

function showConfirmation() {
    // 入力内容を確認画面に反映
    $('#confirm-shimei').text($('#inquiry-form input[name="shimei"]').val());
    $('#confirm-mail').text($('#inquiry-form input[name="mail"]').val());
    $('#confirm-goiken').text($('#inquiry-form select[name="goiken"] option:selected').text());
    $('#confirm-naiyou').text($('#inquiry-form textarea[name="naiyou"]').val());

    // フォームを隠して確認画面を表示
    $('#inquiry-form').hide();
    $('#confirmation-screen').show();
}

</script>

</body>
