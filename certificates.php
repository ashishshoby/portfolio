<?php
$uploadDirCert = __DIR__ . '/uploads/certificates/';
$certificates = [];
if (is_dir($uploadDirCert)) {
    $files = scandir($uploadDirCert);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            // only show images
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $certificates[] = $file;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Certifications · Ashish Shoby Jacob</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #09090b;
      --surface: #121216;
      --primary: #8ef0bd;
      --text: #f8fafc;
      --text-muted: #94a3b8;
      --border: rgba(255, 255, 255, 0.08);
      --font-body: 'Inter', sans-serif;
      --font-heading: 'Outfit', sans-serif;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background-color: var(--bg); color: var(--text); font-family: var(--font-body); line-height: 1.6; }
    
    nav { padding: 2rem 0; border-bottom: 1px solid var(--border); background: var(--surface); }
    .nav-inner { display: flex; align-items: center; justify-content: space-between; }
    .logo { font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: var(--text); text-decoration: none; }
    .logo span { background: linear-gradient(135deg, var(--primary), #c68e2e); -webkit-background-clip: text; color: transparent; }

    .container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
    h1 { color: var(--text); font-family: var(--font-heading); font-size: clamp(2.5rem, 5vw, 3.5rem); margin-top: 4rem; margin-bottom: 1rem; }
    .subtitle { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 4rem; max-width: 600px; }
    
    .cert-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 2.5rem;
      margin-bottom: 6rem;
    }
    .cert-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 1rem;
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .cert-card:hover { transform: translateY(-5px); border-color: rgba(142,240,189, 0.4); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5); }
    .cert-img-wrapper {
      width: 100%;
      height: 250px;
      overflow: hidden;
      background: #000;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .cert-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.4s;
    }
    .cert-card:hover .cert-img { transform: scale(1.05); }
    .cert-info {
      padding: 1.5rem;
      background: rgba(255,255,255,0.02);
      border-top: 1px solid var(--border);
    }
    .cert-name {
      font-weight: 600;
      font-size: 1.1rem;
      color: var(--text);
      word-break: break-all;
    }
  </style>
</head>
<body>

  <nav>
    <div class="container nav-inner">
      <a href="portfolio.html" class="logo">A<span>.</span></a>
      <a href="portfolio.html" style="color: var(--text-muted); text-decoration: none; font-weight: 500;">&larr; Back to Portfolio</a>
    </div>
  </nav>

  <div class="container">
    <h1>My Certifications</h1>
    <p class="subtitle">A showcase of my verified qualifications, course completions, and technical achievements.</p>
    
    <?php if (empty($certificates)): ?>
        <div style="text-align: center; padding: 4rem; background: var(--surface); border-radius: 1rem; border: 1px dashed var(--border); color: var(--text-muted);">
            No certificates found. Upload them from the <a href="admin.php" style="color: var(--primary); text-decoration: none;">Admin Dashboard</a>.
        </div>
    <?php else: ?>
        <div class="cert-grid">
            <?php foreach ($certificates as $cert): ?>
                <?php 
                    $namePart = preg_replace('/^[0-9]+_/', '', $cert); 
                    $displayName = pathinfo($namePart, PATHINFO_FILENAME);
                    $displayName = str_replace('_', ' ', $displayName);
                ?>
                <div class="cert-card">
                    <div class="cert-img-wrapper">
                        <img src="uploads/certificates/<?= urlencode($cert) ?>" alt="<?= htmlspecialchars($displayName) ?>" class="cert-img" loading="lazy">
                    </div>
                    <div class="cert-info">
                        <div class="cert-name"><?= htmlspecialchars($displayName) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
  </div>

</body>
</html>
