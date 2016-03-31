<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Keys are in Manyware, Values in Sugar
 *
 * @author Michiel
 */

class SyncColumnsSugar {
        public $ContactColumns = Array(
            'id',
            'project_id',
            'contact_id',
            'voorletters_c',
            'first_name',
            'middle_name',
            'last_name',
            'primary_address_street',
            'primary_address_number_c',
            'primary_address_number_add_c',
            'primary_address_postalcode',
            'primary_address_city',
			'primary_address_country',
            'phone_home',
            'phone_mobile',
            'email1',
            'birthdate',
            'gender_c',
            'bankrekeningnummer_c',
            'rekeninghoudernaam_c',
            'datum_ondertekening',
            'akkoord_ondertekening',
            'verificatie_ok',
            'paspoortnr_c'=>'passpoortnumber_c',
            'dieet_c',
            'ehbo_c',
            'manyware_relatienummer_c',
            'manyware_aiarelatie_c',
            'sport_c',
            'sport_level_c',
            'kledingmaat_c'
	);
        
        public $DeelnameColumns = Array(
            // Sportkamp
            'kamervoorkeur1',
            'kamervoorkeur2',
            'kamervoorkeur3',
            'kamervoorkeur4',
            
            'emergency_contact',
            'emergency_phonenumber',
            
            //'historie_sportkamp_omschrijving',
            'historie_sportkamp_team',
            /*'historie_coach_geweest',
            'trainigs_ervaring',
            'ervaring_met_tieners',
            'relatie_met_Jezus_omschrijving',
            'motivatie_sportkamcoach',
            
            // Project
            'ervaring',
            'ervaring_omschrijving',
            'persoonlijke_relatie',
            'lid_kerkgenootschap',
            'lid_kerkgenootschap_omschrijving',
            'evangelisatie_ervaring_omschrijving',
            'sterke_punten',
            'zwakke_punten',
            'overige_informatie',
            'referentie_naam',
            'referentie_relatie',
            'referentie_telefoonnummer',

            'emergency_contact',
            'emergency_phonenumber',
            
            // Leiderstraject
            'motivatie',
            'ervaring_leiderschap',
            'ervaring_aia',
            'waarom_volgende_stap',*/
            
            'projectprijs',
            'project_naam',
            'bankrekeninghouder',
            'bankrekeningnummer',
            'deelnemertype',
            'geen_conferentie',
            'tiener_korting',
            'familie_korting',
            'motivatie_korting',
            'opmerkingen',
			'extra_optie_1'=>'extra_optie_1_c',
			'extra_optie_2'=>'extra_optie_2_c'
	);
        

        
        
        function phone_home($record) {
            if (strlen($record['phone_home'])<=1) return "";
            if ($record['phone_home']{0}=='0' && $record['phone_home']{1}!='0') {
                $record['phone_home']='+31'.substr($record['phone_home'],1);
            }
            return $record['phone_home'];
        }
        
        function phone_mobile($record) {
            if (strlen($record['phone_mobile'])<=1) return "";
            if ($record['phone_mobile']{0}=='0' && $record['phone_mobile']{1}!='0') {
                $record['phone_mobile']='+31'.substr($record['phone_mobile'],1);
            }
            return $record['phone_mobile'];
        }
        
        function emergency_phonenumber($record) {
            if (strlen($record['emergency_phonenumber'])<=1) return $record['emergency_phonenumber'];
            if ($record['emergency_phonenumber']{0}=='0' && $record['emergency_phonenumber']{1}!='0') {
                $record['emergency_phonenumber']='+31'.substr($record['emergency_phonenumber'],1);
            }
            return $record['emergency_phonenumber'];
        }
}

?>
