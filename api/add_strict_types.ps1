$dir = "E:\laragon\www\symfony_acl\api"
Get-ChildItem -Path $dir -Filter "*.php" -Recurse | Where-Object { $_.FullName -notmatch "\\vendor\\" -and $_.FullName -notmatch "\\var\\" } | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    if ($content -match "^\s*<\?php" -and $content -notmatch "declare\s*\(\s*strict_types\s*=\s*1\s*\)") {
        $content = $content -replace "(?s)^\s*<\?php\s*", "<?php`r`n`r`ndeclare(strict_types=1);`r`n`r`n"
        Set-Content -Path $_.FullName -Value $content -NoNewline
        Write-Host "Updated $($_.FullName)"
    }
}
Write-Host "Done"
