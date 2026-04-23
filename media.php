<?php
$module = $_GET['module'] ?? 'home';
header('Location: /sistemsmk/sistemsmk-laravel/public/module/' . rawurlencode($module));
exit;