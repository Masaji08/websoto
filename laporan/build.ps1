$env:Path += ";C:\Program Files\MiKTeX\miktex\bin\x64"

$DIR = Split-Path -Parent $MyInvocation.MyCommand.Path
$OUTDIR = $DIR
$REPORT = "$OUTDIR\laporan-project.pdf"
$TMPDIR = Join-Path $env:TEMP "laporan-build-$([System.IO.Path]::GetRandomFileName())"
New-Item -ItemType Directory -Path $TMPDIR -Force | Out-Null

try {
    Copy-Item "$OUTDIR\cover.md" "$TMPDIR\"
    Copy-Item "$OUTDIR\template.latex" "$TMPDIR\"
    
    if (Test-Path "$OUTDIR\logo-kampus.jpg") {
        Copy-Item "$OUTDIR\logo-kampus.jpg" "$TMPDIR\"
    }

    if (Test-Path "$OUTDIR\gambar") {
        Copy-Item "$OUTDIR\gambar" "$TMPDIR\gambar" -Recurse
    }

    $COMBINED = "$TMPDIR\report.md"

    function StripYaml($file) {
        $content = Get-Content $file -Raw
        $content = $content -replace '(?ms)^---\s*\n.*?^---\s*\n', ''
        return $content
    }

    function StripHash($content) {
        $lines = $content -split "`n"
        $lines = $lines | Where-Object { $_ -notmatch '^#{1,6}\s+BAB' }
        return $lines -join "`n"
    }

    $bab1 = StripYaml "$OUTDIR\01-pendahuluan.md"
    $bab1 = StripHash $bab1

    $bab2 = StripYaml "$OUTDIR\02-profil-perusahaan.md"
    $bab2 = StripHash $bab2

    $bab3 = StripYaml "$OUTDIR\03-pembahasan.md"
    $bab3 = StripHash $bab3

    $bab4 = StripYaml "$OUTDIR\04-penutup.md"
    $bab4 = StripHash $bab4

    $daftarpustaka = Get-Content "$OUTDIR\daftar-pustaka.md" -Raw

    $combinedContent = @"
# PENDAHULUAN

$bab1

# PROFIL PERUSAHAAN

$bab2

# PEMBAHASAN

$bab3

# PENUTUP

$bab4

$daftarpustaka
"@

    Set-Content -Path $COMBINED -Value $combinedContent

    Set-Location -Path $TMPDIR

    $pandocCmd = "pandoc `"$COMBINED`" --template=`"$TMPDIR\template.latex`" --include-before-body=`"$TMPDIR\cover.md`" --top-level-division=chapter --pdf-engine=pdflatex -o `"$REPORT`""
    
    Write-Host "Building PDF..." -ForegroundColor Green
    Invoke-Expression $pandocCmd
    
    if (Test-Path $REPORT) {
        Write-Host "PDF berhasil dibuat: $REPORT" -ForegroundColor Green
    }
} finally {
    Remove-Item -Path $TMPDIR -Recurse -Force -ErrorAction SilentlyContinue
}
