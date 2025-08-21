<?php
    require_once("db.php");

    class city extends db{

        function checkcity($cityid,$cityname){
            $sql="CALL `sp_checkcity`({$cityid},'{$cityname}')";
            return $this->getData($sql)->rowCount();
        }

        function savecity($cityid,$cityname,$countryid){
            if($this->checkcity($cityid,$cityname)){
                return ["status"=>"exists","message"=>"The city name already exists"];
            }else{
                $sql="CALL `sp_savecity`({$cityid},'{$cityname}',{$countryid})";
                $this->getData($sql);
                return ["status"=>"success","message"=>"the city was saved successfully"];
            }
        }

        function getcities($countryid){
            $sql="CALL `sp_filtercities`({$countryid})";
            return $this->getJSON($sql);       
        }

        function getcitydetails($cityid){
            $sql="CALL `sp_getcitydetails`({$cityid})";
            return $this->getJSON($sql);
        }

        function deletecity($cityid){
            $sql="CALL `sp_deletecity`({$cityid})";
            $this->getData($sql);
            return ["status"=>"success","message"=>"city was deleted successfully"];
        }
    }

?>