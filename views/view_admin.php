<!DOCTYPE html>
<html>

<body>
    <div id="main">
        <nav>Nav bar</nav>

        <div id="content">

            <div style="display:flex;">
                <div class="">
                    <span>urls:</span>
                    <span></span>
                </div>

                <div>
                    <span>pv:</span>
                    <span></span>
                </div>

                <div>
                    <span>uv:</span>
                    <span></span>
                </div>

                <div>
                    <span>urls:</span>
                    <span></span>
                </div>
            </div>


            <div class="urls">
                

            </div>


            <div class="traffic">

            </div>
        </div>
    </div>
</body>

<script>
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
        height: 100%;
    }

    body {
        font-family: 'Lato', sans-serif;
        color: #888;
        margin: 0;
    }

    #main {
        display: table;
        width: 100%;
        height: 100vh;
        text-align: center;
    }

    nav {
        height: 60px;
        width: 100%;
        background: black;
    }

    #content {
        min-height: calc(100vh - 60px);
        width: 100%;
        background: #f2f2f2;

        display: flex;
        flex-direction: column;

        align-items: center;
    }
</style>

</html>