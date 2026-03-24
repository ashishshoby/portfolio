<?php
$message = '';
$uploadDirRes = __DIR__ . '/uploads/';
$uploadDirCert = __DIR__ . '/uploads/certificates/';

if (!is_dir($uploadDirRes)) mkdir($uploadDirRes, 0755, true);
if (!is_dir($uploadDirCert)) mkdir($uploadDirCert, 0755, true);

// Handle Delete Certificate
if (isset($_GET['delete_cert'])) {
    $fileToDelete = basename($_GET['delete_cert']);
    $filePath = $uploadDirCert . $fileToDelete;
    if (file_exists($filePath) && is_file($filePath)) {
        unlink($filePath);
        $message = '<div class="success">Certificate deleted successfully.</div>';
    }
}

// Handle Uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload_resume']) && isset($_FILES['resume'])) {
        $uploadFile = $uploadDirRes . 'resume.pdf';
        $fileType = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if ($fileType != "pdf") {
            $message = '<div class="error">Sorry, only PDF files are allowed for resume.</div>';
        } else {
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $uploadFile)) {
                $message = '<div class="success">Resume successfully updated!</div>';
            } else {
                $message = '<div class="error">Upload failed!</div>';
            }
        }
    } elseif (isset($_POST['upload_cert']) && isset($_FILES['certificate']) && isset($_POST['cert_name'])) {
        $certName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $_POST['cert_name']);
        $fileType = strtolower(pathinfo($_FILES['certificate']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $message = '<div class="error">Only JPG, PNG, WEBP files are allowed for certificates images.</div>';
        } else {
            // Using time to prevent overlap, and keep the user's title
            $newFileName = time() . '_' . $certName . '.' . $fileType;
            $uploadPath = $uploadDirCert . $newFileName;
            if (move_uploaded_file($_FILES['certificate']['tmp_name'], $uploadPath)) {
                $message = '<div class="success">Certificate uploaded successfully!</div>';
            } else {
                $message = '<div class="error">Certificate upload failed! Check permissions.</div>';
            }
        }
    }
}

// Get certificates list
$certificates = [];
if (is_dir($uploadDirCert)) {
    $files = scandir($uploadDirCert);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $certificates[] = $file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0b0a0f; color: #f1f3f8; margin: 0; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: #1a1a26; padding: 2rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 2rem; }
        h1, h2 { margin-top: 0; color: #8ef0bd; }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
        input[type="file"], input[type="text"] { width: 100%; padding: 0.75rem; background: #0b0a0f; color: white; border: 1px solid rgba(255,255,255,0.2); border-radius: 0.5rem; margin-bottom: 1rem; box-sizing: border-box;}
        button { background: #8ef0bd; color: #0b0a0f; border: none; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-size: 1rem; cursor: pointer; font-weight: 600; width: 100%;}
        button:hover { background: #6ddb9f; }
        .success { color: #8ef0bd; background: rgba(142,240,189,0.1); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid rgba(142,240,189,0.3);}
        .error { color: #f472b6; background: rgba(244,114,182,0.1); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid rgba(244,114,182,0.3);}
        .cert-list { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .cert-item { background: #0b0a0f; padding: 1rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;}
        .cert-item a { color: #f472b6; text-decoration: none; font-size: 0.9rem; font-weight: 500; padding: 0.25rem 0.5rem; border: 1px solid #f472b6; border-radius: 4px; transition: 0.2s;}
        .cert-item a:hover { background: #f472b6; color: #000; }
        .nav-link { color: #8ef0bd; text-decoration: none; display: inline-block; margin-bottom: 2rem; font-weight: 500;}
        .nav-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content: space-between; align-items: center;">
            <a href="portfolio.html" class="nav-link">&larr; Back to Portfolio</a>
            <a href="certificates.php" class="nav-link" style="color: white;">View Certificates Page &rarr;</a>
        </div>
        <h1>Admin Dashboard</h1>
        <?= $message ?>
        
        <div class="card">
            <h2>1. Update Resume (PDF)</h2>
            <p style="color: #9ca3af; font-size: 0.9rem; margin-top:0;">This file is linked to the "Download Resume" buttons.</p>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="file" name="resume" accept=".pdf" required>
                <button type="submit" name="upload_resume">Replace Resume</button>
            </form>
        </div>

        <div class="card">
            <h2>2. Upload a New Certificate</h2>
            <p style="color: #9ca3af; font-size: 0.9rem; margin-top:0;">Certificates appear automatically on the certificates page.</p>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="cert_name" placeholder="Certificate Title (e.g. AWS Certified)" required>
                <input type="file" name="certificate" accept=".jpg,.jpeg,.png,.webp" required>
                <button type="submit" name="upload_cert">Upload Certificate Image</button>
            </form>
        </div>

        <div class="card">
            <h2>Manage Uploaded Certificates</h2>
            <?php if (empty($certificates)): ?>
                <p style="color: #9ca3af; font-style: italic;">No certificates uploaded yet.</p>
            <?php else: ?>
                <div class="cert-list">
                    <?php foreach ($certificates as $cert): ?>
                        <?php 
                            $namePart = preg_replace('/^[0-9]+_/', '', $cert); 
                            $displayName = pathinfo($namePart, PATHINFO_FILENAME);
                            $displayName = str_replace('_', ' ', $displayName);
                        ?>
                        <div class="cert-item">
                            <span style="word-break: break-all; margin-right: 1rem; font-size: 0.95rem;"><?= htmlspecialchars($displayName) ?></span>
                            <a href="?delete_cert=<?= urlencode($cert) ?>" onclick="return confirm('Delete this certificate?');">Delete</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
