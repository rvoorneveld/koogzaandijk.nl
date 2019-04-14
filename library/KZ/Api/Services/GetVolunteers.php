<?php

class KZ_Api_Services_GetVolunteers extends KZ_Api_Services
{

    public $service = 'GetVolunteers';
    public $version = '1.0';
    public $code = '3ORggLNhOq';
    public $locations = [
        'tasks' => 'vrijwilligerstaken',
        'volunteers' => 'vrijwilligers',
    ];

    public function run()
    {
        $objModelVolunteer = new KZ_Models_Volunteers();
        $objModelVolunteer->_truncate('volunteer');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://data.sportlink.com/{$this->locations['tasks']}?client_id={$this->code}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $strJsonData = curl_exec($ch);
        curl_close($ch);

        $arrTasks = json_decode($strJsonData, true);

        $arrTaskIds = [];
        if (false === empty($arrTasks) && true === is_array($arrTasks)) {
            foreach ($arrTasks as $arrTask) {
                if (false !== stripos($arrTask['naam'], 'bardienst')) {
                    $arrTaskIds[] = $arrTask['vrijwilligerstaakcode'];
                }
            }
        }

        $intVolunteersAdded = 0;
        if (false === empty($arrTaskIds) && true === is_array($arrTaskIds)) {
            foreach ($arrTaskIds as $intTaskId) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://data.sportlink.com/{$this->locations['volunteers']}?client_id={$this->code}&vrijwilligerstaakcode={$intTaskId}");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $strJsonData = curl_exec($ch);
                curl_close($ch);

                if (false === empty($arrVolunteers = json_decode($strJsonData, true)) && true === is_array($arrVolunteers)) {
                    foreach ($arrVolunteers as $arrVolunteer) {
                        $objModelVolunteer->insert([
                            'name' => $arrVolunteer['naam'],
                            'typeId' => 1,
                            'date' => $arrVolunteer['datumvanaf'],
                            'timeStart' => $arrVolunteer['tijdvanaf'],
                            'timeEnd' => $arrVolunteer['tijdtot'],
                        ]);
                        $intVolunteersAdded++;
                    }
                }
            }
        }
        return [
            'response' => [
                'type' => 'success',
                'service' => 'GetVolunteers',
                'Volunteers' => [
                    'inserted' => $intVolunteersAdded
                ]
            ]
        ];
    }
}
