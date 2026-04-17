<?php
$path = 'C:/Users/zamza/Downloads/Методические_рекомендации_Курсовая_работа_1.docx';
$zip = new ZipArchive();
if ($zip->open($path) !== true) {
    exit("ZIP_OPEN_FAILED\n");
}
$xml = $zip->getFromName('word/document.xml');
$zip->close();
$doc = new DOMDocument();
$doc->loadXML($xml);
$texts = $doc->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 't');
$result = [];
foreach ($texts as $node) {
    $result[] = $node->textContent;
}
echo mb_substr(implode(' ', $result), 0, 8000);
