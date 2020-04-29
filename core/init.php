<?php

error_reporting(0);
$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 't00r',
        'db' => 'security_system'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ),
    'session' => array(
        'session_name' => 'user',
        'token_name' => 'token'
    )
);
spl_autoload_register(function($class) {
    if (file_exists('classes/' . $class . '.php')) {
        require_once 'classes/' . $class . '.php';
        return TRUE;
    }
    return FALSE;
});
require_once 'functions/functions.php';
//Declarations
$hospital_main_title="MILLENIUM SECURITY LIMITED";
$hospital_monthly_title="MILLENIUM SECURITY LIMITED";
$department_list_array=array("Guards","Officers","Others");
$position_list_array=array("Guard","Officer","Director","Manager","Admin","Accountant","Secretory");
$current_country="Uganda";
$countries_list=array("Afghanistan","Albania","Algeria","American Samoa","Andorra","Angola","Anguilla","Argentina","Armenia","Aruba","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia and Herzegowina","Botswana","Bouvet Island","Brazil","British Indian Ocean Territory","Brunei Darussalam","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Cayman Islands","Central African Republic","Chad","Chile","China","Christmas Island","Cocos (Keeling) Islands","Colombia","Comoros","Congo","Congo, the Democratic Republic of the","Cook Islands","Costa Rica","Cote d'Ivoire","Croatia (Hrvatska)","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Falkland Islands (Malvinas)","Faroe Islands","Fiji","Finland","France","French Guiana","French Polynesia","French Southern Territories","Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guadeloupe","Guam","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Heard and Mc Donald Islands","Holy See (Vatican City State)","Honduras","Hong Kong","Hungary","Iceland","India","Indonesia","Iran (Islamic Republic of)","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, Democratic People's Republic of","Korea, Republic of","Kuwait","Kyrgyzstan","Lao People's Democratic Republic","Latvia","Lebanon","Lesotho","Liberia","Libyan Arab Jamahiriya","Liechtenstein","Lithuania","Luxembourg","Macau","Macedonia, The Former Yugoslav Republic of","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Martinique","Mauritania","Mauritius","Mayotte","Mexico","Micronesia, Federated States of","Moldova, Republic of","Monaco","Mongolia","Montserrat","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","Netherlands Antilles","New Caledonia","New Zealand","Nicaragua","Niger","Nigeria","Niue","Norfolk Island","Northern Mariana Islands","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Pitcairn","Poland","Portugal","Puerto Rico","Qatar","Reunion","Romania","Russian Federation","Rwanda","Saint Kitts and Nevis","Saint LUCIA","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Seychelles","Sierra Leone","Singapore","Slovakia (Slovak Republic)","Slovenia","Solomon Islands","Somalia","South Africa","South Georgia and the South Sandwich Islands","Spain","Sri Lanka","St. Helena","St. Pierre and Miquelon","Sudan","Suriname","Svalbard and Jan Mayen Islands","Swaziland","Sweden","Switzerland","Syrian Arab Republic","Taiwan, Province of China","Tajikistan","Tanzania, United Republic of","Thailand","Togo","Tokelau","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Turks and Caicos Islands","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","United States Minor Outlying Islands","Uruguay","Uzbekistan","Vanuatu","Venezuela","Viet Nam","Virgin Islands (British)","Virgin Islands (U.S.)","Wallis and Futuna Islands","Western Sahara","Yemen","Zambia","Zimbabwe");
$modules_list_array=array("Stock Management","Security Staff","System Users","Finance","Payroll","Reports");

$districtList='<option value="Abim"></option>
<option value="Adjumani">Adjumani</option>
<option value="Agago">Agago</option>
<option value="Alebtong">Alebtong</option>
<option value="Amolatar">Amolatar</option>
<option value="Amudat">Amudat</option>
<option value="Amuria">Amuria</option>
<option value="Amuru">Amuru</option>
<option value="Apac">Apac</option>
<option value="Arua">Arua</option>
<option value="Budaka">Budaka</option>
<option value="Bududa">Bududa</option>
<option value="Bugiri">Bugiri</option>
<option value="Bugweri">Bugweri</option>
<option value="Buhweju">Buhweju</option>
<option value="Buikwe">Buikwe</option>
<option value="Bukedea">Bukedea</option>
<option value="Bukomansimbi">Bukomansimbi</option>
<option value="Bukwa">Bukwa</option>
<option value="Bulambuli">Bulambuli</option>
<option value="Buliisa">Buliisa</option>
<option value="Bundibugyo">Bundibugyo</option>
<option value="Bunyangabu">Bunyangabu</option>
<option value="Bushenyi">Bushenyi</option>
<option value="Busia">Busia</option>
<option value="Butaleja">Butaleja</option>
<option value="Butambala">Butambala</option>
<option value="Butambala">Butebo</option>
<option value="Buvuma">Buvuma</option>
<option value="Buyende">Buyende</option>
<option value="Dokolo">Dokolo</option>
<option value="Gomba">Gomba</option>
<option value="Gulu">Gulu</option>
<option value="Hoima">Hoima</option>
<option value="Ibanda">Ibanda</option>
<option value="Iganga">Iganga</option>
<option value="Isingiro">Isingiro</option>
<option value="Jinja">Jinja</option>
<option value="Kaabong">Kaabong</option>
<option value="Kabale">Kabale</option>
<option value="Kabarole">Kabarole</option>
<option value="Kaberamaido">Kaberamaido</option>
<option value="Kagadi">Kagadi</option>
<option value="Kakumiro">Kakumiro</option>
<option value="Kalangala">Kalangala</option>
<option value="Kaliro">Kaliro</option>
<option value="Kalungu">Kalungu</option>
<option value="Kampala">Kampala</option>
<option value="Kamuli">Kamuli</option>
<option value="Kamwenge">Kamwenge</option>
<option value="Kanungu">Kanungu</option>
<option value="Kapchorwa">Kapchorwa</option>
<option value="Karenga">Karenga</option>
<option value="Kasanda">Kasanda</option>
<option value="Kasese">Kasese</option>
<option value="Katakwi">Katakwi</option>
<option value="Kayunga">Kayunga</option>
<option value="Kazo">Kazo</option>
<option value="Kibaale">Kibaale</option>
<option value="Kiboga">Kiboga</option>
<option value="Kibuku">Kibuku</option>
<option value="Kiruhura">Kiruhura</option>
<option value="Kiryandongo">Kiryandongo</option>
<option value="Kisoro">Kisoro</option>
<option value="Kitagwenda">Kitagwenda</option>
<option value="Kitgum">Kitgum</option>
<option value="Koboko">Koboko</option>
<option value="Kole">Kole</option>
<option value="Kotido">Kotido</option>
<option value="Kumi">Kumi</option>
<option value="Kween">Kween</option>
<option value="Kyankwanzi">Kyankwanzi</option>
<option value="Kyegegwa">Kyegegwa</option>
<option value="Kyenjojo">Kyenjojo</option>
<option value="Kyotera">Kyotera</option>
<option value="Lamwo">Lamwo</option>
<option value="Lira">Lira</option>
<option value="Lusot">Lusot</option>
<option value="Luuka">Luuka</option>
<option value="Luweero">Luweero</option>
<option value="Lwengo">Lwengo</option>
<option value="Lyantonde">Lyantonde</option>
<option value="Madi-Okollo">Madi-Okollo</option>
<option value="Manafwa">Manafwa</option>
<option value="Maracha">Maracha</option>
<option value="Masaka">Masaka</option>
<option value="Masindi">Masindi</option>
<option value="Mayuge">Mayuge</option>
<option value="Mbale">Mbale</option>
<option value="Mbarara">Mbarara</option>
<option value="Mitooma">Mitooma</option>
<option value="Mityana">Mityana</option>
<option value="Moroto">Moroto</option>
<option value="Moyo">Moyo</option>
<option value="Mpigi">Mpigi</option>
<option value="Mubende">Mubende</option>
<option value="Mukono">Mukono</option>
<option value="Nabilatuk">Nabilatuk</option>
<option value="Nakapiripirit">Nakapiripirit</option>
<option value="Nakaseke">Nakaseke</option>
<option value="Nakasongola">Nakasongola</option>
<option value="Namayingo">Namayingo</option>
<option value="Namisindwa">Namisindwa</option>
<option value="Namutumba">Namutumba</option>
<option value="Napak">Napak</option>
<option value="Nebbi">Nebbi</option>
<option value="Ngora">Ngora</option>
<option value="Ntoroko">Ntoroko</option>
<option value="Ntungamo">Ntungamo</option>
<option value="Nwoya">Nwoya</option>
<option value="Obongi">Obongi</option>
<option value="Omoro">Omoro</option>
<option value="Otuke">Otuke</option>
<option value="Oyam">Oyam</option>
<option value="Pader">Pader</option>
<option value="Pakwach">Pakwach</option>
<option value="Pallisa">Pallisa</option>
<option value="Rakai">Rakai</option>
<option value="Rubanda">Rubanda</option>
<option value="Rubirizi">Rubirizi</option>
<option value="Rukiga">Rukiga</option>
<option value="Rukungiri">Rukungiri</option>
<option value="Rwampara">Rwampara</option>
<option value="Sembabule">Sembabule</option>
<option value="Serere">Serere</option>
<option value="Sheema">Sheema</option>
<option value="Sironko">Sironko</option>
<option value="Soroti">Soroti</option>
<option value="Tororo">Tororo</option>
<option value="Wakiso">Wakiso</option>
<option value="Yumbe">Yumbe</option>
<option value="Zombo">Zombo</option>';
?>