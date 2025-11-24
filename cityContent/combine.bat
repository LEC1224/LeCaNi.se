@echo off
REM Combine all JSON files in the current directory into a single JSON array.
REM Usage: merge-json.bat [outputFileName.json]
REM If no output name is provided, defaults to combined.json

setlocal
set "OUT=%~1"
if not defined OUT set "OUT=combined.json"

REM Run a small PowerShell routine to parse each file and emit a JSON array
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$ErrorActionPreference='Stop';" ^
  "$out = $ExecutionContext.SessionState.Path.GetUnresolvedProviderPathFromPSPath('%OUT%');" ^
  "$files = Get-ChildItem -LiteralPath . -Filter '*.json' | Where-Object { $_.FullName -ne $out } | Sort-Object Name;" ^
  "if (-not $files) { Set-Content -LiteralPath $out -Value '[]' -Encoding UTF8; return }" ^
  "$list = New-Object System.Collections.ArrayList;" ^
  "foreach ($f in $files) {" ^
    "try { $obj = Get-Content -LiteralPath $f.FullName -Raw | ConvertFrom-Json }" ^
    "catch { Write-Error \"Invalid JSON in '$($f.Name)': $($_.Exception.Message)\"; exit 1 }" ^
    "[void]$list.Add($obj)" ^
  "}" ^
  "$json = $list | ConvertTo-Json -Depth 100;" ^
  "Set-Content -LiteralPath $out -Value $json -Encoding UTF8"

if errorlevel 1 (
  echo Failed to combine JSON. See errors above.
  exit /b 1
)

echo Done. Wrote %OUT%.
endlocal
