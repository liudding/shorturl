<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <div class="container">
        <h1>XXXXX</h1>

        <form method="POST">
            <div>
                <input type="text" />
            </div>

            <button>Submit</button>
        </form>
    </div>
</body>

<style>
    html {
        width: 100%;
        height: 100%;
        overflow-y: scroll;
        background-color: #dce7eb;
        color: rgba(48, 69, 92, 0.8);
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

    button {
      outline: 0;
      background: #4caf50;
      width: 100%;
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

    button {
        margin-top: 24px;
    }

    @media (min-width: 550px) {
        .container {
            /* top: 30%; */
        }
    }
</style>

</html>