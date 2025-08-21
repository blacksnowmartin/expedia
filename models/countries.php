<?php
    require_once("db.php");

    class country extends db{

        function checkcounty($countryid,$countryname){
            $sql="CALL `sp_checkcountry`({$countryid},'{$countryname}')";
            return $this->getData($sql)->rowCount();
        }

        function savecountry($countryid,$countryname){
            if($this->checkcounty($countryid,$countryname)){
                return ["status"=>"exists","message"=>"country name alraedy exists"];
            }else{
                $sql="CALL `sp_savecountry`({$countryid},'{$countryname}')";
                $this->getData($sql);
                return ["status"=>"success","message"=>"country save successfully"];
            }
        }

        function getcountries(){
            $sql="CALL `sp_getcountries`()";
            return $this->getJSON($sql);
        }

        function getcountrydetails($countryid){
            $sql="CALL `sp_getcountrydetails`({$countryid})";
            return $this->getJSON($sql);
        }

        function deletecountry($countryid){
            $sql="CALL `sp_deletecountry`({$countryid})";
            $this->getData($sql);
            return ["status"=>"success","message"=>"the country was deleted successfully"];
        }
    }

?>