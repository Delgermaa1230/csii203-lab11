<?php
// Get the temporary GitHub code from the request query parameters

$CLIENT_ID = 'febe1cf8420d5e54ed43';
$CLIENT_SECRET = '708e71b0cef50b50dda6f034003e8f113b17b79f';

$sessionCode = $_GET['code'];

// Use cURL to make a POST request to GitHub to get an access token
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, 'https://github.com/login/oauth/access_token');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
    'client_id' => $CLIENT_ID,
    'client_secret' => $CLIENT_SECRET,
    'code' => $sessionCode,
]));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($curl);
curl_close($curl);

// Extract the access token and granted scopes from the JSON response
$jresult = json_decode($result, true);
$accessToken = $jresult['access_token'];

setcookie('access_token', $accessToken, 0, '/');

if (!isset($_COOKIE['access_token'])) {
    header("Location: http://localhost:3000/index.php"); 
    exit();
}

$accessToken = $_COOKIE['access_token'];
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, 'https://api.github.com/user/emails');
$headers = [
    'Accept: application/json',
    'Authorization: Bearer ' . $accessToken,
    'X-GitHub-Api-Version: 2022-11-28',
    'User-Agent: mybrowser'
];
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($curl);
curl_close($curl);

$emails_jarr = json_decode($result, true);

$allowedEmails = ["Galbayardelgermaa@gmail.com"];
$userEmails = [];

foreach ($emails_jarr as $item) {
    if (isset($item['email'])) {
        $userEmails[] = $item['email'];
    }
}

if (!array_intersect($userEmails, $allowedEmails)) {
    die("Access Denied"); 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demi's</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            margin: 20px;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #9f673b;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #9f673b;
            font-size: 14px;
        }


        .input-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        input {
            padding: 12px;
            margin-bottom: 16px;
            width: 220px;
            border: 2px solid #9f673b;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        button {
            padding: 14px;
            background-color: #9f673b;
            color: #fafafa;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #9f673b;
        }

        #displayArea {
            margin-top: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #9f673b;
        }

        th {
            background-color: #9f673b;
            color: #fff;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>

    <h1>Demi-гийн Эд зүйлсийн мэдээллийн сан</h1>

    <label for="stuffIdInput">Эд зүйлийн дугаарыг оруулна уу :</label>
    <input type="number" id="stuffIdInput" min="1" max="101" />
    <button onclick="fetchData()">Сонгосон эд зүйлийг харах</button>
    <button onclick="fetchAllData()">Бүх эд зүйлсийг харах</button>
    <button onclick="fetchBookData()">Бүх номнуудыг харах</button>


    <div id="displayArea"></div>

    <script>
        function fetchData() {
            var selectedStuffId = $("#stuffIdInput").val();


            if (isNaN(selectedStuffId)) {
                alert("Эд зүйлийн дугаар алдаатай байна!");
                return;
            }

            if (selectedStuffId < 1 || selectedStuffId > 101) {
                alert("Эд зүйлийн дугаар 1 ээс 100 гийн хооронд байх ёстой шүү!");
                return;
            }

            $.ajax({
                url: 'details.php',
                type: 'GET',
                data: { stuffid: selectedStuffId },
                dataType: 'json',
                success: function (data) {
                    displayData(data);
                },
                error: function (error) {
                    console.error('Өгөгдөл авахад алдаа гарлаа:', error);
                }
            });
        }

        function fetchAllData() {
            $.ajax({
                url: 'fetchalldata.php', 
                success: function (data) {
                    displayData(data);
                },
                error: function (error) {
                    console.error('Бүх өгөгдөл авахад алдаа гарлаа:', error);
                }
            });
        }

        function fetchBookData() {
            $.ajax({
                url: 'fetchbookdata.php', 
                success: function (data) {
                    displayData(data);
                },
                error: function (error) {
                    console.error('Номын өгөгдөл авахад алдаа гарлаа:', error);
                }
            });
        }


        function displayData(data) {
            var displayArea = $('#displayArea');
            displayArea.empty();

            var table = '<table border="1"><tr><th>Эд зүйлийн дугаар</th><th>Эд зүйлийн нэр</th><th>Худалдаж авсан өдөр</th><th>Зориулалт</th><th>Үнэ $</th><th>Ангилал</th></tr>';

            for (var i = 0; i < data.length; i++) {
                table += '<tr>';
                table += '<td>' + data[i].stuffId + '</td>';
                table += '<td>' + data[i].stuffName + '</td>';
                table += '<td>' + data[i].stuffBdate + '</td>';
                table += '<td>' + data[i].stuffusage + '</td>';
                table += '<td>' + data[i].stuffPrice + '</td>';
                table += '<td>' + data[i].categoryName + '</td>';
                table += '</tr>';
            }

            table += '</table>';
            displayArea.html(table);
        }

    </script>

</body>

</html>
