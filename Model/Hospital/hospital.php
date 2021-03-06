<?php

    class Hospital
    {
        
        /**
         * Get all hospitals from the database
         * @return array
         */
        public static function GetHospitals()
        {
            $request = database::GetDB('bloodbankdb')->prepare('SELECT ref_hospital, hospital_name, hospital_city FROM hospitals');
            $request->execute();
            $results = $request->fetchAll();
            
            return $results;
        }

        /**
         * Return a list of hospital who have an specified amount of blood
         * @param int $amount Amount of blood to verify
         * @param string $bloodgroup Bloodgroup to verify
         * @return array
         */
        public static function GetRequestHospitals($amount, $bloodgroup)
        {
            $bg = 'blood_bank.' . $bloodgroup;
            $request = database::GetDB('bloodbankdb')->prepare('SELECT hospitals.ref_hospital, hospitals.hospital_name, hospitals.hospital_city FROM hospitals, blood_bank WHERE hospitals.ref_hospital = blood_bank.ref_hospital AND ' . $bg . ' >= ' . $amount . ' ORDER BY ' . $bg . ' DESC');
            $request->execute();
            $results = $request->fetchAll();

            return $results;
        }

        /**
         * Return the reference of the hospital with the specified request id
         * @param string $id ID of the request to return hospital reference
         * @return string
         */
        public static function GetHospitalByRequestId($id)
        {
            $request = database::GetDB('bloodbankdb')->prepare('SELECT ref_hospital FROM hospital_request WHERE id = :id');
            $request->bindParam(':id', $id);
            $request->execute();
            $results = $request->fetch();
            
            return $results[0];
        }

    }