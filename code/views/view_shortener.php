<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <div class="container">
        <h1>短链接</h1>

        <form id="form" method="POST">
            <div>
                <input id='url' name="url" value="<?php echo $_SESSION['url'] ?? '' ?>" oninput="onInputChange()"
                    type="text" placeholder="请输入你的链接"/>
            </div>
            <button onclick="onSubmit()" id="submitBtn" type="button">生成</button>
        </form>

        <div class="message-container">
            <div id="message"></div>
        </div>


        <div id="modal" class="result-container modal" onclick="onClickModal()" style="display:none;">
            <div id="modal-content" class="modal-content">
                <div class="result" style="display:flex;flex-direction:column;">
                    <h3 style="color: green;">缩短成功</h3>
                    <div class="input-button-group">
                        <input id='shorturl' value="" />
                        <button id='copy' type="submit" onclick="onClickCopy()">复制</button>
                    </div>
                    <a id="preview" href="" target="_blank" style="margin-top:30px">点击预览</a>
                </div>
            </div>
        </div>

    </div>
</body>

<script>
    function onSubmit() {
        const input = document.getElementById('url');

        const valid = /^(((ht|f)tps?):\/\/)?[\w-]+(\.[\w-]+)+([\w\-.,@?^=%&:/~+#]*[\w\-@?^=%&/~+#])?$/.test(input
            .value);

        if (!valid) {
            showMessage('链接不正确')
        } else {
            const data = {};

            const fd = new FormData(document.getElementById('form'))
            for (let entry of fd.entries()) {
                data[entry[0]] = entry[1];
            }

            request('', data, (resp) => {
                if (resp.errmsg) {
                    showMessage(resp.errmsg);
                }

                if (resp.code) {
                   document.getElementById('shorturl').value = resp.shorturl;
                   document.getElementById('preview').href = resp.shorturl;
                   document.getElementById('modal').style.display = 'block';
                }
            });
        }
    }

    function showMessage(message) {
        document.getElementById('message').innerText = message

        setTimeout(() => {
            document.getElementById('message').innerText = ''
        }, 5000)
    }

    function onInputChange() {
        const input = document.getElementById('url');

        const valid = /^(((ht|f)tps?):\/\/)?[\w-]+(\.[\w-]+)+([\w\-.,@?^=%&:/~+#]*[\w\-@?^=%&/~+#])?$/.test(input
            .value);

        document.getElementById('submitBtn').disabled = !valid;
    }

    if (document.getElementById('modal-content')) {
        document.getElementById('modal-content').onclick = function (e) {
            e.stopPropagation();
        }
    }

    function onClickCopy() {

        document.getElementById('shorturl').select();

        const suc = document.execCommand("copy");

        if (suc) {
            const copyBtn = document.getElementById('copy')
            copyBtn.innerText = '复制成功'
            copyBtn.disabled = true;


            setTimeout(() => {
                copyBtn.innerText = '复制'
                copyBtn.disabled = false;
            }, 3000)
        }
    }

    function onClickModal() {
        document.getElementById('modal').style.display = 'none';
    }

    function request(url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true)
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(xhr.responseText)

                callback && callback(JSON.parse(xhr.responseText));
            }
        }

        let params = [];
        for (let key in data) {
            params.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]))
        }

        xhr.send(params.join('&'))
    }
</script>

<style>
    html {
        width: 100%;
        height: 100%;
        overflow-y: scroll;
        background-color: #dce7eb;
        color: rgba(48, 69, 92, 0.8);
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    body {
        position: relative;
        height: 100%;
        background-color: #fefffa;
        margin: 0;

        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;

        position: absolute;
        top: 20%;

        width: 80%;
    }

    h1 {
        /* margin-top: 100px; */
    }

    form {
        margin-top: 34px;
        width: 100%;
        display: flex;
    }

    form div {
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    input {
        outline: 0;
        background: #f2f2f2;
        width: 100%;
        border: 0;
        padding: 22px 16px;
        box-sizing: border-box;
        font-size: 24px;
        border-radius: 4px;
    }

    .input-button-group {
        display: flex;
        flex-direction: row;
    }

    .message-container {
        width: 100%;
        display: flex;
        flex-direction: column;
        color: red;
    }

    .result {
        /* position: absolute;
        top: 20%; */
        padding: 40px;
        width: 80%;
        display: flex;
        flex-direction: column;

        background: white;
    }

    .preview {
        margin-top: 40px;
    }

    .preview iframe {
        width: 100%;
        height: 300px;
    }

    .message {}

    button {
        outline: 0;
        background: #4caf50;
        border: 0;
        border-radius: 4px;
        padding: 15px;
        color: #ffffff;
        font-size: 14px;
        -webkit-transition: all 0.3 ease;
        transition: all 0.3 ease;
        cursor: pointer;
    }

    button:hover,
    button:active,
    button:focus {
        background: #43a047;
    }

    /* button[disabled] {
        background: rgb(218, 218, 218);
    } */

    button {
        width: 200px;
        height: 74px;
        margin-left: 20px;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #00000055;
        z-index: 9999;
    }

    .modal-content {
        padding: 8px;


        height: 50%;
        width: 50%;

        overflow: auto;

        margin: auto;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;

        display: flex;
        align-items: center;
        justify-content: space-around;
    }

    @media (min-width: 550px) {
        .container {
            /* top: 30%; */
        }
    }
</style>

</html>