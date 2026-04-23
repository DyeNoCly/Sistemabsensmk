Set-Location "d:\xampp\htdocs\sistemsmk"
$archiveRoot = "archive/phase3-unused-legacy-2026-04-23"
$rows = Get-Content "$archiveRoot/manifest.csv"
foreach ($r in $rows) {
  $parts = $r -split "\|",3
  if ($parts[0] -ne "MOVED") { continue }
  $src = $parts[2]
  $dst = $parts[1]
  if (Test-Path $src) {
    $dstDir = Split-Path $dst -Parent
    if ($dstDir -and -not (Test-Path $dstDir)) { New-Item -ItemType Directory -Path $dstDir -Force | Out-Null }
    Move-Item -Path $src -Destination $dst -Force
  }
}
