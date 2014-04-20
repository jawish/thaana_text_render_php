<?php
include('ThaanaTextRender.php');

// Initialize
$ttr = new ThaanaTextRender('./fonts/', 'a_waheed', 16, 'white', 0, 50, 'rgb(0,0,0)', 127, 2, '#555555', 80);

// Render image and save output
$ttr->renderImage('gUgulunc aepwlc awaimUvIaW vWdwkurW kwhwlw aeve. gUgulc aencDcroaiDc fOnutwkwSc mihWru vwnI mUvI scTUDiaOaeac dIfw aeve.', 300, 'output.png', 'png');
?>
