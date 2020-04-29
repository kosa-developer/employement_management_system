<?php
require 'pdf/phpqrcode/qrlib.php';
 $finaldata=$_GET["code"];
QRcode::png($finaldata);
?>