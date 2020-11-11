<!DOCTYPE html>
<html>
<body>
    <div id="main">
        <div class="fof">
            <h1>Invalid Code</h1>
        </div>
    </div>
</body>

<?php if (config('invalid_code_action') === 'redirect_in_error_page'): ?>

<script>
    setTimeout(function () {
        location.href = "<?php echo config('redirect_url');  ?>"
    }, 5000)
</script>

<?php endif; ?>

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

    .fof {
        display: table-cell;
        vertical-align: middle;
    }

    .fof h1 {
        font-size: 50px;
        display: inline-block;
        padding-right: 12px;
    }
</style>

</html>