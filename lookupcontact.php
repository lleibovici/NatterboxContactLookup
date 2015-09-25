<?php
/**
 * Created by IntelliJ IDEA.
 * User: leo
 * Date: 22/09/2015
 * Time: 15:02
 */
function checkSpeedDial($cid)
{
    $ret = '';
    $name = 'NOT FOUND';

    date_default_timezone_set('Europe/London');
    $dbfilename = './speeddial.db';
    if (file_exists($dbfilename)) {
        $db = new PDO("sqlite:$dbfilename");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    } else {
        $db = new PDO("sqlite:$dbfilename");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $db->exec("create table speeddial(snumber INTEGER , phonenumber VARCHAR (32))");
    }
    $sql = "SELECT * FROM speeddial WHERE phonenumber='$cid'";
    $res = $db->query($sql);
    if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $ret = $row['name'];
    }
    return $ret;
}
$name = 'NOT FOUND';

if (isset($_GET['callerid']) && trim($_GET['callerid']) != '') {
    $callerid = str_replace(' ', '', $_GET['callerid']);
    if (substr($callerid, 0, 1) == '0') {
        $callerid = '+44' . substr($callerid, 1);
    }
    $sd = checkSpeedDial($callerid);
    if ($sd) {
        $name = $sd;
    }
    else {
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
        $sql = "SELECT * FROM phonedir WHERE phonenumber='$callerid'";
        $res = $db->query($sql);
        if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $company = $row['company'];
            if ($company == 'NULL') {
                $comapny = '';
            }
            $contact = $row['contact'];
            if ($contact == 'NULL') {
                $contact = '';
            }
            if (trim($contact) != '') {
                $name = $contact . ', ' . $company;
            } else {
                $name = $company;
            }
        }
    }
}
else {
    $name='NO callerid sent';
}
if ($name == 'NOT FOUND') {
    file_put_contents('unmatched.txt',"$callerid\r\n",FILE_APPEND);
}
header("Content-Type:text/xml");
echo('<?xml version="1.0" encoding="UTF-8"?>');
echo('<records>');
echo('<record>');
echo("<Name>$name</Name>");
echo("<Error>OK</Error>");
echo('</record>');
echo('</records>');
exit;
