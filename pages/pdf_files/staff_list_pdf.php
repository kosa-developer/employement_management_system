<?php

require ('pdf_header.php');
$pdf = new PDF();
$pdf->AliasNbPages();
if (isset($_GET["print_list"]) && $_GET["print_list"] == "print_staff_list" && $_GET["data_sent"] != "") {
    $data_sent = unserialize($crypt->decode($_GET["data_sent"]));
    $fileHeading = $data_sent[0];
    $staffQuery = $data_sent[1];
    $pdf->AddPage();
    $pdf->SetTextColor(180, 0, 16);
    $pdf->createHeader($fileHeading, 185);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(10, 5, "No.", 1, 0, "L");
    $pdf->Cell(45, 5, "Names", 1, 0, "L");
    $pdf->Cell(12, 5, "Gender", 1, 0, "L");
    $pdf->Cell(12, 5, "Age", 1, 0, "L");
    $pdf->Cell(25, 5, "Phone", 1, 0, "L");
    $pdf->Cell(35, 5, "Rank", 1, 0, "L");
    $pdf->Cell(25, 5, "Position", 1, 0, "L");
    $pdf->Cell(30, 5, "Serial No.", 1, 1, "L");

    $pdf->SetFont("Arial", "", 6);
    $number = 0;
    $staff_list = DB::getInstance()->querySample($staffQuery);
    foreach ($staff_list AS $staff):
        $brought_profile_picture = ($staff->Photo != "") ? $staff->Photo : "default.jpg";
        $pdf->Cell(10, 5, ++$number, 1, 0, "L");
        $pdf->Cell(45, 5, $staff->Title . '.  ' . $staff->Fname . ' ' . $staff->Lname, 1, 0, "L");
        $pdf->Cell(12, 5, "$staff->Gender", 1, 0, "L");
        $pdf->Cell(12, 5, calculateAge($staff->DOB, $date_today), 1, 0, "L");
        $pdf->Cell(25, 5, "$staff->Phone_Number", 1, 0, "L");
        $pdf->Cell(35, 5, "$staff->Rank", 1, 0, "L");
        $pdf->Cell(25, 5, "$staff->Position", 1, 0, "L");
        $pdf->Cell(30, 5, "$staff->Serial_Number", 1, 1, "L");
    endforeach;
    $pdf->AutoPrint();
    $pdf->Output();
}