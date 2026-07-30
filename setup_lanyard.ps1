# <#
# setup_lanyard.ps1
# ---------------------------------------------------------------
# This script installs the Lanyard 3‑D widget into your existing
# "A portfolio" site.
#
# Requirements:
#   • Windows 10/11 with PowerShell (any recent version)
#   • Node.js v20 (or newer) installed via the official MSI – this
#     adds `node` and `npm` to the system PATH. If you have just installed
#     Node, close this terminal and open a new one before running the script.
#
# What the script does:
#   1️⃣ Checks that `node` and `npm` are available.
#   2️⃣ Downloads the two required 3‑D asset files (card.glb & lanyard.png)
#       into the widget's src/assets folder.
#   3️⃣ Runs `npm install` to fetch the widget's dependencies.
#   4️⃣ Builds the widget with Vite (`npm run build`).
#   5️⃣ Copies the built bundle (index.js, index.css) **and** the GLB/PNG
#       files into the public `assets` folder of your site.
#   6️⃣ Installs `http-server` globally (if not already present) and starts
#       a quick preview server on http://127.0.0.1:8080.
#
# Usage:
#   1. Open a new PowerShell window (so the PATH includes Node).
#   2. Navigate to the project root:
#        cd "C:\wamp64\www\A  portfolio"
#   3. Run the script:
#        .\setup_lanyard.ps1
#   4. When the server starts, open the printed URL in a browser.
# ---------------------------------------------------------------
#>

# ===================== 0️⃣ CONFIGURATION =====================
$ProjectRoot   = "C:\wamp64\www\A  portfolio"
$WidgetFolder  = Join-Path $ProjectRoot "lanyard-widget"
$AssetsFolder  = Join-Path $ProjectRoot "assets"
$SrcAssets     = Join-Path $WidgetFolder "src\assets"

# URLs for the 3‑D assets (raw files from the upstream repo)
$CardUrl = "https://raw.githubusercontent.com/DavidHDev/react-bits/main/src/assets/lanyard/card.glb"
$BandUrl = "https://raw.githubusercontent.com/DavidHDev/react-bits/main/src/assets/lanyard/lanyard.png"

# ===================== 1️⃣ CHECK NODE =====================
if (-not (Get-Command node -ErrorAction SilentlyContinue)) {
    Write-Host "🚨 Node.js is NOT on the PATH."
    Write-Host "   • Install it from https://nodejs.org/dist/v20.12.0/node-v20.12.0-x64.msi"
    Write-Host "   • After installing, CLOSE this PowerShell window and OPEN a NEW one."
    exit 1
} else {
    Write-Host "✅ Node version: $(node -v)   npm version: $(npm -v)"
}

# ===================== 2️⃣ DOWNLOAD ASSETS =====================
Write-Host "\n📥 Downloading required 3‑D assets..."
New-Item -ItemType Directory -Force -Path $SrcAssets | Out-Null
Invoke-WebRequest -Uri $CardUrl -OutFile (Join-Path $SrcAssets "card.glb") -UseBasicParsing -ErrorAction Stop
Invoke-WebRequest -Uri $BandUrl -OutFile (Join-Path $SrcAssets "lanyard.png") -UseBasicParsing -ErrorAction Stop
Write-Host "✅ Assets downloaded to $SrcAssets"

# ===================== 3️⃣ NPM INSTALL =====================
Write-Host "\n📦 Installing widget npm dependencies…"
Set-Location $WidgetFolder
npm install --silent
if ($LASTEXITCODE -ne 0) { Write-Host "❌ npm install failed"; exit $LASTEXITCODE }

# ===================== 4️⃣ BUILD =====================
Write-Host "\n🚧 Building widget with Vite…"
npm run build --silent
if ($LASTEXITCODE -ne 0) { Write-Host "❌ npm run build failed"; exit $LASTEXITCODE }

# ===================== 5️⃣ COPY BUNDLE & ASSETS =====================
Write-Host "\n📂 Copying bundle and static assets to $AssetsFolder…"
New-Item -ItemType Directory -Force -Path $AssetsFolder | Out-Null
Copy-Item -Path (Join-Path $WidgetFolder "dist\assets\*") -Destination $AssetsFolder -Recurse -Force
Copy-Item -Path (Join-Path $SrcAssets "card.glb")   -Destination $AssetsFolder -Force
Copy-Item -Path (Join-Path $SrcAssets "lanyard.png") -Destination $AssetsFolder -Force
Write-Host "✅ Bundle and assets are now in $AssetsFolder"

# ===================== 6️⃣ START PREVIEW SERVER =====================
if (-not (Get-Command http-server -ErrorAction SilentlyContinue)) {
    Write-Host "\n🔧 Installing http‑server globally…"
    npm i -g http-server --silent
    if ($LASTEXITCODE -ne 0) { Write-Host "❌ Failed to install http-server"; exit $LASTEXITCODE }
}
Write-Host "\n🌐 Starting local preview server (http://127.0.0.1:8080) …"
Set-Location $ProjectRoot
http-server . -c-1
# The script will keep running until you stop it with Ctrl+C.
