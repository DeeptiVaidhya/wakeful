<?php

// Server file
class PushNotifications {

    function sendNotification($dataArr) {
        $fcmApiKey = 'AIzaSyBKt-6MwJERvHtDyZETE-gdvFINgqbuthc'; //App API Key(This is google cloud messaging api key not web api key)
        $url = 'https://fcm.googleapis.com/fcm/send'; //Google URL

        $registrationIds = $dataArr['device_id']; //Fcm Device ids array

        $message = $dataArr['message']; //Message which you want to send
        $title = $dataArr['message'];

        // prepare the bundle
        $msg = array('message' => $message, 'title' => $title,'status_code'=>"-1");
        $fields = array('registration_ids' => $registrationIds, 'data' => $msg);
   
        $headers = array(
            'Authorization: key=' . $fcmApiKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);

        var_dump($result);
        die;

        return $result;
    }

}

?>