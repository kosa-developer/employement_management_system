<?php

ob_start();
//error_reporting(E_ALL);
date_default_timezone_set('Africa/Nairobi');
$date_today = date("Y-m-d");
session_start();
include 'core/init.php';
$title = $hospital_main_title . " | ";


$crypt = new Encryption();
$encoded_page = isset($_GET['page']) ? $_GET['page'] : ('login');
$page = $crypt->decode($encoded_page);
$page_title = str_replace("_", " ", strtoupper($page));
//$page = $encoded_page;
//Delete all the pending drug prescriptions that have not been taken or paid within 12 hrs time
switch ($page) {
    default:
        $page = "login";
        include 'pages/users/login.php';
        break;

    case 'dashboard':
        if (file_exists('pages/' . $page . '.php'))
            include 'pages/' . $page . '.php';
        break;

    /* Users***************************** */
    case 'add_user':
        if (file_exists('pages/users/' . $page . '.php'))
            include 'pages/users/' . $page . '.php';
        break;

    case 'view_users':
        if (file_exists('pages/users/' . $page . '.php'))
            include 'pages/users/' . $page . '.php';
        break;

    case 'update_account':
        if (file_exists('pages/users/' . $page . '.php'))
            include 'pages/users/' . $page . '.php';
        break;

    case 'add_staff':
        if (file_exists('pages/staff/' . $page . '.php'))
            include 'pages/staff/' . $page . '.php';
        break;

    case 'view_staff':
        if (file_exists('pages/staff/' . $page . '.php'))
            include 'pages/staff/' . $page . '.php';
        break;

    case 'staff_profile':
        if (file_exists('pages/staff/' . $page . '.php'))
            include 'pages/staff/' . $page . '.php';
        break;
    //Hospital Settings

    case 'departments':
        if (file_exists('pages/settings/' . $page . '.php'))
            include 'pages/settings/' . $page . '.php';
        break;
    //drug names

    case 'logout':
        if (file_exists('pages/users/' . $page . '.php'))
            include 'pages/users/' . $page . '.php';
        break;


    case 'assets':
        if (file_exists('pages/stock/' . $page . '.php'))
            include 'pages/stock/' . $page . '.php';
        break;

    case 'register_asset':
        if (file_exists('pages/settings/' . $page . '.php'))
            include 'pages/settings/' . $page . '.php';
        break;
    case 'signatories':
        if (file_exists('pages/settings/' . $page . '.php'))
            include 'pages/settings/' . $page . '.php';
        break;


    case 'ajax_data':
        if (file_exists('pages/' . $page . '.php'))
            include 'pages/' . $page . '.php';
        break;


    //staff_list_pdf
    case 'staff_list_pdf':
        if (file_exists('pages/pdf_files/' . $page . '.php'))
            include 'pages/pdf_files/' . $page . '.php';
        break;
    //financial reports
    case 'financial_report_pdf':
        if (file_exists('pages/pdf_files/' . $page . '.php'))
            include 'pages/pdf_files/' . $page . '.php';
        break;
        

    //---------24/09/2018----------
    case 'staff_attendance':
        if (file_exists('pages/staff/' . $page . '.php'))
            include 'pages/staff/' . $page . '.php';
        break;
    case 'overtime_attendance':
        if (file_exists('pages/staff/' . $page . '.php'))
            include 'pages/staff/' . $page . '.php';
        break;
    case 'daily_salary_rates':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;


    case 'add_staff_salary_scale':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;
    case 'add_staff_payment':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;
    case 'view_staff_payments':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;
    case 'loan':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;
    case 'guards_pay':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;

    case 'patient_invoice':
        if (file_exists('pages/billing/' . $page . '.php'))
            include 'pages/billing/' . $page . '.php';
        break;
    case 'user_guide':
        if (file_exists('pages/' . $page . '.php'))
            include 'pages/' . $page . '.php';
        break;
    case 'new_purchase_order':
        if (file_exists('pages/stock/' . $page . '.php'))
            include 'pages/stock/' . $page . '.php';
        break;
    case 'view_purchase_orders':
        if (file_exists('pages/stock/' . $page . '.php'))
            include 'pages/stock/' . $page . '.php';
        break;
    case 'view_income_statement':
        if (file_exists('pages/finance/' . $page . '.php'))
            include 'pages/finance/' . $page . '.php';
        break;
    case 'add_tax_settings':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;

    case 'staff_allowance':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;
    case 'officer_payroll':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;

    case 'bank_salaries':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;

    case 'expenditure_report':
        if (file_exists('pages/staff/payroll/' . $page . '.php'))
            include 'pages/staff/payroll/' . $page . '.php';
        break;

    case 'excel_download':
        if (file_exists('pages/excel_exports/' . $page . '.php'))
            include 'pages/excel_exports/' . $page . '.php';
        break;

    case 'add_bank':
        if (file_exists('pages/settings/' . $page . '.php'))
            include 'pages/settings/' . $page . '.php';
        break;

    case 'company_branches':
        if (file_exists('pages/settings/' . $page . '.php'))
            include 'pages/settings/' . $page . '.php';
        break;

    case 'expenses':
        if (file_exists('pages/settings/' . $page . '.php'))
            include 'pages/settings/' . $page . '.php';
        break;
    case 'add_customer':
        if (file_exists('pages/customer/' . $page . '.php'))
            include 'pages/customer/' . $page . '.php';
        break;
    case 'customer_billing':
        if (file_exists('pages/customer/' . $page . '.php'))
            include 'pages/customer/' . $page . '.php';
        break;
    case 'billing_report':
        if (file_exists('pages/customer/' . $page . '.php'))
            include 'pages/customer/' . $page . '.php';
        break;
         case 'invoicing':
        if (file_exists('pages/customer/' . $page . '.php'))
            include 'pages/customer/' . $page . '.php';
        break;
        
         case 'qrcode_generator':
        if (file_exists('pages/pdf_files/' . $page . '.php'))
            include 'pages/pdf_files/' . $page . '.php';
        break;
        
        
}
ob_flush();
?>
