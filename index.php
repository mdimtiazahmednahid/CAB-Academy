<?php
/**
 * ACS Academy - Production Root Entrypoint
 * Bridges DocumentRoot /htdocs directly to /public/index.php on shared hosting environments (InfinityFree / cPanel).
 */
require_once __DIR__ . '/public/index.php';
