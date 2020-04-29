<?php

function generatePassword($length = 8) {

    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);

    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
        $length = $maxlength;
    }

    // set up a counter for how many characters are in the password so far
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {

        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, $maxlength - 1), 1);

        // have we already used this character in $password?
        if (!strstr($password, $char)) {
            // no, so it's OK to add it onto the end of whatever we've already got...
            $password .= $char;
            // ... and increase the counter by one
            $i++;
        }
    }

    // done!
    return $password;
}

function escape($string) {
    return htmlentities($string);
}

function english_date($date) {
    $create_date = date_create($date);
    $new_date = date_format($create_date, "j M Y");
    return $new_date;
}

function redirect($message, $url) {
    ?>
    <script type="text/javascript">
        //        function Redirect()
        //        {
        //            window.location = "<?php echo $url; ?>";
        //        }
        //        alert('<?php echo $message; ?>');
        //        setTimeout('Redirect()', 10);
        alert('<?php echo $message; ?>');
        window.location = "<?php echo $url; ?>"
    </script>
    <?php
}

function english_date_time($date) {
    $create_date = date_create($date);
    $new_date = date_format($create_date, "jS F Y  H:i:s a");
    return $new_date;
}
function english_months($date) {
    $datecame="0000-".$date."-04";
            
    $create_date = date_create($datecame);
    $new_date = date_format($create_date, "M");
    return $new_date;
}

function english_months_year($date) {
     $create_date = date_create($date);
    $new_date = date_format($create_date, "M Y");
    return $new_date;
}
function english_year($date) {
    $create_date = date_create($date);
    $new_date = date_format($create_date, "Y");
    return $new_date;
}

function english_time($date) {
    $create_date = date_create($date);
    $new_date = date_format($create_date, "H:i:s a");
    return $new_date;
}

function ugandan_shillings($value) {
    $value = number_format($value, 0, ".", ",");
    return $value . " UGx";
}

function increaseDateToDate($value, $type, $dateConvert) {
    $date = date_create($dateConvert);
    date_add($date, date_interval_create_from_date_string($value . ' ' . $type));
    return date_format($date, 'Y-m-d');
}

function calculateAge($smallDate, $largeDate) {
    $age = "";
    $diff = date_diff(date_create($smallDate), date_create($largeDate));
    $age .= ($diff->y > 0) ? $diff->y . "Y " : "";
    $age .= ($diff->m > 0 && $diff->y < 10) ? $diff->m . "M " : "";
    $age .= ($diff->d > 0 && $diff->y < 1) ? $diff->d . "D " : "";
    $age = ($age != "") ? $age : 0;
    return $age;
}

function calculateDateDifference($smallDate, $largeDate, $type) {
    $age = 0;
    $diff = strtotime($largeDate)-strtotime($smallDate);
    $age = ($type == "years") ? $diff/(60 * 60 * 24*30*12) : $age;
    $age = ($type == "months") ? $diff/(60 * 60 * 24*30) : $age;
    $age = ($type == "days") ? $diff/(60 * 60 * 24) : $age;
    return $age;
}
function calculateEmployeeTax($gross_income) {
    $paye_tax = 0;
    if ($gross_income >= 10000000) {
        $paye_tax = (($gross_income - 410000) * 0.3) + 25000 + ((0.1 * ($gross_income - 10000000)));
    } else if ($gross_income < 10000000 && $gross_income >= 410000) {
        $paye_tax = (($gross_income - 410000) * 0.3) + 25000;
    } else if ($gross_income < 410000 && $gross_income >= 335000) {
        $paye_tax = (($gross_income - 335000) * 0.2) + 10000;
    } else if ($gross_income < 335000 && $gross_income >= 235000) {
        $paye_tax = ($gross_income - 235000) * 0.1;
    }
    $nssf_5percent = $gross_income * 0.05;
    $nssf_10percent = $gross_income * 0.1;
    return array("paye"=>$paye_tax,"nssf_5percent"=>$nssf_5percent,"nssf_10percent"=>$nssf_10percent);
}

//Function to generate a tag
function generate_tag($code, $middle, $string_value) {
    $code = ($code != "") ? $code . "-" : "";
    $middle = ($middle != "") ? $middle . "-" : "";
//    $strData = explode(' ', $string_value);
//    $tag = '';
//    foreach ($strData as $substring_data) {
//        $tag .= strtoupper(substr($substring_data, 0, 1));
//    }
    $Tag = $code . $middle . str_pad($string_value, 2, '0', STR_PAD_LEFT);
    return $Tag;
}
?>
