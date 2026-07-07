<?php

$file = 'E:\laragon\www\symfony_acl\api\var\log\dev.log';
if (!file_exists($file)) {
    echo 'Log file not found.';
    exit;
}
$lines = file($file);
$last_lines = array_slice($lines, -100);
echo implode('', $last_lines);
