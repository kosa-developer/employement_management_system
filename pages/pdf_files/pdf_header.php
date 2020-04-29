<?php
require 'pdf/fpdf.php';

class PDF extends FPDF {

    const DPI = 100;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 57;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 120;
    const MAX_HEIGHT = 100;
    
    
    /* Start of Printing lib functions */

    protected $javascript;
    protected $n_js;

    function IncludeJS($script, $isUTF8 = false) {
        if (!$isUTF8)
            $script = utf8_encode($script);
        $this->javascript = $script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js = $this->n;
        $this->_put('<<');
        $this->_put('/Names [(EmbeddedJS) ' . ($this->n + 1) . ' 0 R]');
        $this->_put('>>');
        $this->_put('endobj');
        $this->_newobj();
        $this->_put('<<');
        $this->_put('/S /JavaScript');
        $this->_put('/JS ' . $this->_textstring($this->javascript));
        $this->_put('>>');
        $this->_put('endobj');
    }

    function AutoPrint($printer = '') {
        // Open the print dialog
        if ($printer) {
            $printer = str_replace('\\', '\\\\', $printer);
            $script = "var pp = getPrintParams();";
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
            $script .= "pp.printerName = '$printer'";
            $script .= "print(pp);";
        } else
            $script = 'print(true);';
        $this->IncludeJS($script);
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_put('/Names <</JavaScript ' . ($this->n_js) . ' 0 R>>');
        }
    }

    /* End of Printing lib functions */


    function pixelsToMM($val) {
        return $val * self::MM_IN_INCH / self::DPI;
    }

    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return array(
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        );
    }

    function centreImage($img) {
        list($width, $height) = $this->resizeToFit($img);
        // you will probably want to swap the width/height
        // around depending on the page's orientation

        $this->Image($img, (self::A4_WIDTH - $width) / 2, (self::A4_HEIGHT - $height) / 2, $width, $height
        );
    }

    function Header() {
       
//        $this->centreImage('images/icon.png');
//       $this->Image("http://127.0.0.1/Millenium%20Security%20Limited/index.php?page=x9QyL2C9h6ONVY0h5vYBfhPjK2Shnm8mp23xPBelBac&download_type=download_bank_salary_report&data_sent=k9BUW3H3xM61CgsvmtKnKY6qO5onwx5NrvCtIvUJjyrOGR0mOWdcVqb9OTRxSJHmJarkswh4teDt9rhBGQknu7M6r8MvhM3YKete7H0P1SLNMChy_yKmOOoGcSsmWfAZ4KDwfnDXVvleGLOxGf2TJHI37JSmByM82lmFkfQCUMyL2lmu-U5YmljJJMT8KXZPPkKJBuC7pLwgRl8DcSt6TAj0VrC3AsC4gpy6geuyNUqh5rbpRRh3TSJuWG1dvf3mh1i4AvEhRk4oqzK2rO7wxNeLb947Jdz0AMto272yYsf27IzHvkmbnuhqsUYV4fPgycC_QEipedLObD2vMWebX7m2XsTyr-mz8SJ4RVrY6dF2eH8mTtglz5eS3XO7RdCaWEa93tlVjZtM1U6D4PKX0bl_CZHMbWFV5JvoMBgrZV9v4ajeA2RgrNoZzgZfoNqA1FQWMR9aFBTKdcm4_nzJb89qvCduGChC3M2-B2qbqKbyKRXWIJX5vSUHHoJzwOONzkD7ises3VzM6AFtkyARc5IdMw8fVMgPi5woXCm_E8KGleJGxgNDYImyqnb0CractNqGMFH90XmWkYaljs1jUEwWe37KPqog56CRyMZ47ZRXOoMAXGy_chMkRtHoP-da",190,-15,30,30,"png");
 
//        $this->Ln(32);
//        $this->SetTextColor(180, 0, 16);
//        $this->SetFont("Arial", "B", 16);
//        $this->Cell(0, 10, "$hospital_name ", 0, 1, "C");
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(0, 0, 0);

        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}  [Downloaded by: ' .$_SESSION['hospital_username']. "  on " . date("l jS \of F Y h:i:s A") . " ]", 0, 0, 'R');

        $this->Ln(5);
    }

    function createHeader($report_title,$page_size) {
        $this->SetTextColor(0, 0, 225);
        $this->SetFont("Arial", "B", 12);
         try {
            $this->Image("logo/logo.PNG", 140, 5, "C");
        } catch (Exception $ex) {
            
        }
       // $this->Cell(0, 10, strtoupper(""), 0, 1, "C");
        //$this->Write(5, strtoupper($report_title));
        $this->Cell(0, 5, $report_title, 0, 1, "C");
        $this->SetFont("Arial", "B", 10);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(5);
    }  function SetDash($black = null, $white = null) {
        if ($black !== null)
            $s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
        else
            $s = '[] 0 d';
        $this->_out($s);
    }
    function Code39($xpos, $ypos, $code,$text, $baseline=0.5, $height=5){

    $wide = $baseline;
    $narrow = $baseline / 3 ; 
    $gap = $narrow;

    $barChar['0'] = 'nnnwwnwnn';
    $barChar['1'] = 'wnnwnnnnw';
    $barChar['2'] = 'nnwwnnnnw';
    $barChar['3'] = 'wnwwnnnnn';
    $barChar['4'] = 'nnnwwnnnw';
    $barChar['5'] = 'wnnwwnnnn';
    $barChar['6'] = 'nnwwwnnnn';
    $barChar['7'] = 'nnnwnnwnw';
    $barChar['8'] = 'wnnwnnwnn';
    $barChar['9'] = 'nnwwnnwnn';
    $barChar['A'] = 'wnnnnwnnw';
    $barChar['B'] = 'nnwnnwnnw';
    $barChar['C'] = 'wnwnnwnnn';
    $barChar['D'] = 'nnnnwwnnw';
    $barChar['E'] = 'wnnnwwnnn';
    $barChar['F'] = 'nnwnwwnnn';
    $barChar['G'] = 'nnnnnwwnw';
    $barChar['H'] = 'wnnnnwwnn';
    $barChar['I'] = 'nnwnnwwnn';
    $barChar['J'] = 'nnnnwwwnn';
    $barChar['K'] = 'wnnnnnnww';
    $barChar['L'] = 'nnwnnnnww';
    $barChar['M'] = 'wnwnnnnwn';
    $barChar['N'] = 'nnnnwnnww';
    $barChar['O'] = 'wnnnwnnwn'; 
    $barChar['P'] = 'nnwnwnnwn';
    $barChar['Q'] = 'nnnnnnwww';
    $barChar['R'] = 'wnnnnnwwn';
    $barChar['S'] = 'nnwnnnwwn';
    $barChar['T'] = 'nnnnwnwwn';
    $barChar['U'] = 'wwnnnnnnw';
    $barChar['V'] = 'nwwnnnnnw';
    $barChar['W'] = 'wwwnnnnnn';
    $barChar['X'] = 'nwnnwnnnw';
    $barChar['Y'] = 'wwnnwnnnn';
    $barChar['Z'] = 'nwwnwnnnn';
    $barChar['-'] = 'nwnnnnwnw';
    $barChar['.'] = 'wwnnnnwnn';
    $barChar[' '] = 'nwwnnnwnn';
    $barChar['*'] = 'nwnnwnwnn';
    $barChar['$'] = 'nwnwnwnnn';
    $barChar['/'] = 'nwnwnnnwn';
    $barChar['+'] = 'nwnnnwnwn';
    $barChar['%'] = 'nnnwnwnwn';

    $this->SetFont('Arial','',10);
    $this->Text($xpos, $ypos + $height + 4, $text);
    $this->SetFillColor(0);

    $code = '*'.strtoupper($code).'*';
    for($i=0; $i<strlen($code); $i++){
        $char = $code[$i];
        if(!isset($barChar[$char])){
            $this->Error('Invalid character in barcode: '.$char);
        }
        $seq = $barChar[$char];
        for($bar=0; $bar<9; $bar++){
            if($seq[$bar] == 'n'){
                $lineWidth = $narrow;
            }else{
                $lineWidth = $wide;
            }
            if($bar % 2 == 0){
                $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
            }
            $xpos += $lineWidth;
        }
        $xpos += $gap;
    }
}


}
?>