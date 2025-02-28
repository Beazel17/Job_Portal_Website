<?php
// Database connection
require 'config.php'; // Ensure this file contains a valid PDO connection ($pdo)

// Fetch uploaded files from the database
$query = "SELECT * FROM shared_files";
$stmt = $pdo->prepare($query);
$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Scanner</title>
    <style>
        /* Global body styles */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom right, #4CAF50, #2c3e50);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 80px;
        }

        /* Navbar styles */
        .navbar {
            background: linear-gradient(to right, #4CAF50, #2c3e50);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .title {
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .navbar .logout {
            color: #fff;
            font-size: 1rem;
            font-weight: bold;
            padding: 8px 16px;
            border: 2px solid #fff;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar .logout:hover {
            background-color: #fff;
            color: #2c3e50;
            transform: scale(1.1);
        }

        /* Container and box styles */
        h2 {
            font-size: 2rem;
            color: #fff;
            margin-top: 120px;
        }

        .container {
            display: flex;
            gap: 30px;
            width: 80%;
            margin-top: 30px;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .box {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #fff;
            min-height: 300px;
            transition: 0.3s;
            max-width: 450px;
            box-sizing: border-box;
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        /* File item styles */
        .file-item {
            background: rgba(0, 0, 0, 0.3);
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }

        .safe {
            background-color: lightgreen;
            color: black;
        }

        .harmful {
            background-color: lightcoral;
            color: black;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            border-radius: 5px;
        }

        .btn-scan {
            background: dodgerblue;
            color: white;
        }

        .btn-view {
            background: green;
            color: white;
        }

        .btn-delete {
            background: red;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }

    </style>
</head>
<body>

<div class="navbar">
    <div class="title">File Scanner</div>
    <a href="shared_files.php" class="logout">Observe</a>
    <a href="admin_dashboard.php" class="logout">Home</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<h2>File Scanner</h2>

<div class="container">
    <!-- File Send Container -->
    <div class="box" id="file-send">
        <h3>File Send</h3>
        <?php foreach ($files as $file): ?>
            <div class="file-item" id="file-<?= $file['id']; ?>">
                <span><?= htmlspecialchars($file['file_name']); ?></span>
                <button class="btn btn-scan" onclick="scanFile(<?= $file['id']; ?>, '<?= htmlspecialchars($file['file_path']); ?>', '<?= htmlspecialchars($file['file_name']); ?>')">Scan</button>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Result After Scan Container -->
    <div class="box" id="scan-result">
        <h3>Result After Scan</h3>
    </div>
</div>

<script>
function scanFile(fileId, filePath, fileName) {
    let fileElement = document.getElementById("file-" + fileId);
    if (!fileElement) return;

    // Check if the file is one of the harmful files
    let harmfulFiles = ["Virus.BAT", "CS GO +3 FPS.BAT"];
    let isHarmful = harmfulFiles.includes(fileName); // Only these two files are harmful

    fileElement.innerHTML += " (Scanning...)";

    setTimeout(() => {
        fileElement.remove(); // Remove from "File Send" container
        let resultContainer = document.getElementById("scan-result");

        let newElement = document.createElement("div");
        newElement.classList.add("file-item");
        newElement.classList.add(isHarmful ? "harmful" : "safe");
        newElement.innerHTML = `<span>${fileName.replace(" (Scanning...)", "")}</span>`;

        // If the file is safe, show "View" link. If harmful, show "Delete" button
        if (!isHarmful) {
            newElement.innerHTML += ` <a class="btn btn-view" href="${filePath}" target="_blank">View</a>`;
        } else {
            newElement.innerHTML += ` <button class="btn btn-delete" onclick="deleteFile(${fileId})">Delete</button>`;
        }

        resultContainer.appendChild(newElement);
    }, 2000); // 2-second delay to simulate scanning
}

function deleteFile(fileId) {
    let confirmDelete = confirm("Are you sure you want to delete this file?");
    if (confirmDelete) {
        fetch('delete_file.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: fileId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let fileElement = document.getElementById("file-" + fileId);
                if (fileElement) fileElement.remove();
                alert("File deleted successfully from the server and database.");
            } else {
                alert("Error deleting file: " + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>

</body>
</html>
