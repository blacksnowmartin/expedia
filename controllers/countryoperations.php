<?php
    require_once("../models/countries.php");

    $country= new country();

    if(isset($_POST['savecountry'])){
        $countryid=$_POST['countryid'];
        $countryname=$_POST['countryname'];
        $response=$country->savecountry($countryid,$countryname);
        echo json_encode($response);
    }

    if(isset($_GET['getcountries'])){
        echo $country->getcountries();
    }

    if(isset($_GET['getcountrydetails'])){
        $countryid=$_GET['countryid'];
        echo $country->getcountrydetails($countryid);
    }

    if(isset($_POST['deletecountry'])){
        $countryid=$_POST['countryid'];
        $response=$country->deletecountry($countryid);
        echo json_encode($response);
    }

?>