<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vietnamese Text Correction Website - Logged In</title>
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <link rel="stylesheet" href="../css/userlogined.css">
    <style>
        /* Style cho các từ sai chính tả */
        .incorrect {
            color: red;
            text-decoration: underline;
        }

        /* Style cho khung hiển thị lỗi chính tả */
        .error-box {
            width: 30%;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            overflow-y: auto;
            max-height: 400px;
        }

        .no-errors {
            color: green;
        }

        .has-errors {
            color: red;
        }

        /* Loading screen overlay */
        #loading-screen {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            text-align: center;
        }

        #loading-screen div {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -60%);
            color: white;
        }

        #loading-screen h2 {
            margin-bottom: 70px;
        }

        /* Loading spinner */
        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="header-top">
        <h1>Vietnamese Text Correction Website</h1>
        <div class="user-info">
            <span id="username-display" style="margin-right: 10px; font-weight: bold;">
                Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>

    <!-- Loading screen -->
    <div id="loading-screen">
        <div>
            <h2>Processing, please wait...</h2>
            <div class="spinner"></div>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="input-section">
                <p>Text input:</p>
                <div id="editor-container"></div>
                <div class="btn-section">
                    <input type="file" id="file-input" accept=".txt" />
                    <button id="upload-btn">Upload</button>
                    <button id="submit-btn">Submit</button>
                    <button id="download-btn" class="download-btn">Download</button>
                </div>
            </div>
            <div class="output-section">
                <textarea readonly id="corrected-text" placeholder="Corrected Text"></textarea>
            </div>
            <div class="footer">
                <p>Group 7 Text Correction © 2024</p>
            </div>
        </div>
        <div class="error-box" id="error-box">
            <!-- Display spelling errors here -->
        </div>
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Enter text...',
            modules: {
                toolbar: [
                    [{ 'font': [] }, { 'size': [] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['clean']
                ]
            }
        });

        document.getElementById('submit-btn').addEventListener('click', function () {
            submitText();
        });

        function highlightErrors(errors) {
            var errorBox = document.getElementById('error-box');
            var errorList = '';
            if (errors.length === 0) {
                errorBox.innerHTML = '<div class="no-errors">There are no spelling errors</div>';
            } else {
                errors.forEach((error) => {
                    errorList += `<div>Spelling error: ${error}</div>`;
                });
                errorBox.innerHTML = '<div class="has-errors">' + errorList + '</div>';
            }
        }

        function submitText() {
            var text = quill.getText().trim();
            document.getElementById('loading-screen').style.display = 'block'; // Show loading screen

            fetch('../php/openai_correction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'text': text
                })
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading-screen').style.display = 'none'; // Hide loading screen

                    if (data.choices) {
                        var correctedText = data.choices.trim();
                        document.getElementById('corrected-text').value = correctedText;

                        var originalWords = text.split(/\s+/);
                        var correctedWords = correctedText.split(/\s+/);
                        var errors = [];

                        for (var i = 0; i < originalWords.length; i++) {
                            if (originalWords[i] !== correctedWords[i]) {
                                errors.push(originalWords[i]);
                            }
                        }

                        highlightErrors(errors);

                        if (errors.length === 0) {
                            document.getElementById('corrected-text').value = 'Exact text';
                        }
                    } else {
                        alert('There was an error with the correction.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loading-screen').style.display = 'none'; // Hide loading screen
                    alert('An error occurred while trying to connect to the API.');
                });
        }

        document.getElementById('upload-btn').addEventListener('click', function () {
            var fileInput = document.getElementById('file-input');
            var file = fileInput.files[0];

            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    quill.root.innerText = e.target.result;
                };
                reader.readAsText(file);
            } else {
                alert("No file selected.");
            }
        });

        document.getElementById('download-btn').addEventListener('click', function () {
            var correctedText = document.getElementById('corrected-text').value;
            if (correctedText.trim() === "") {
                alert("No corrected text to download.");
                return;
            }

            var blob = new Blob([correctedText], { type: "text/plain;charset=utf-8" });
            var link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "corrected_text.txt";
            link.style.display = "none";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</body>

</html>
