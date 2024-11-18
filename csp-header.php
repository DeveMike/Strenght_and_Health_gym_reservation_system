<?php
// Määrittele CSP-otsikot
$cspRules = "default-src 'self'; "
    . "script-src 'self' 'unsafe-inline'; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
    . "img-src 'self' https://trusted-images.com https://cdn.000webhost.com; "
    . "font-src 'self' https://fonts.gstatic.com; "
    . "frame-src 'none'; "
    . "object-src 'none';";
header("Content-Security-Policy: " . $cspRules);
