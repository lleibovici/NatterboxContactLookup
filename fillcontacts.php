<?php
/**
 * Created by IntelliJ IDEA.
 * User: leo
 * Date: 22/09/2015
 * Time: 11:13
 */
date_default_timezone_set('Europe/London');
$dbfilename = './crmcontacts.db';
if (file_exists($dbfilename)) {
    $db = new PDO("sqlite:$dbfilename");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} else {
    $db = new PDO("sqlite:$dbfilename");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $db->exec("create table phonedir(contact VARCHAR(64), company VARCHAR(128), phonenumber VARCHAR (64))");
}
$db->exec("DELETE FROM phonedir");

$fh=fopen('crmcontacts.csv',"r");
while (!feof($fh)) {
    $line = fgetcsv($fh,1024);
    $contact = $line[2];
    $company = $line[1];
    $phone = str_replace(' ','',$line[0]);
    $phone= preg_replace("/[^0-9+]/","", $phone);
    if (substr($phone, 0, 1) == '0') {
        $phone = '+44' . substr($phone, 1);
    }
    if (substr($phone,0,4) == '+440') {
        $phone = '+44' . substr($phone,4);
    }
    //$phone = str_replace('(0)','',$phone);
    //print("Contact: $contact Company: $company Phone: $phone<br/>\r\n");
    $sql="INSERT INTO phonedir VALUES('$contact','$company','$phone')";
    echo("$sql\r\n");
    $db->exec($sql);
}
$db->exec("CREATE INDEX idx1 ON phonedir(phonenumber)");
/*$res=$db->query("SELECT * FROM phonedir");
while ($row =  $res->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
    print "<br/>\r\n";
}*/
