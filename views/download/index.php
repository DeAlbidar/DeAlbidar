<?php
$file = 'public/assets/cv/ebenezer_albidar_narh_cv.pdf';

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    header('Location: index.php?error=file_not_found');
}
?>