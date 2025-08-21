<?php
    require_once("../models/city.php");

    $city=new city();

    if(isset($_POST['savecity'])){
        $cityid=$_POST['cityid'];
        $cityname=$_POST['cityname'];
        $countryid=$_POST['countryid'];

        $response=$city->savecity($cityid,$cityname,$countryid);
        echo json_encode($response);
    }

    if(isset($_GET['filtercities'])){
        $countryid=isset($_GET['countryid'])?$_GET['countryid']:0;

        // if(isset($_GET['countryid'])){
        //     $countryid=$_GET['countryid'];
        // }else{
        //     $countryid=0;
        // }

        echo $city->getcities($countryid);
    }

    if(isset($_GET['getcitydetails'])){
        $cityid=$_GET['cityid'];
        echo $city->getcitydetails($cityid);
    }

    if(isset($_POST['deletecity'])){
        $cityid=$_POST['cityid'];
        $response=$city->deletecity($cityid);
        echo json_encode($response);
    }


?>