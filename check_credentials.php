<?php

##
## 1.2024 urig.digital/ir
##
## Refer:
##   Matrix: https://github.com/ma1uta/matrix-synapse-rest-password-provider
##   ISPConfig: https://timmehosting.de/ispconfig-api-schnittstelle-zur-automatisierung
##
## php-soap package is needet for SoapClient
##

# ISPConfig authentication
require dirname(__FILE__) . "/soap_config.php";

# Get content from Matrix
$sMatrixRequest = file_get_contents("php://input", true);
$oMatrixRequest = json_decode($sMatrixRequest, true);

# Convert mxsid -> email address
$aUsername = explode(":", trim($oMatrixRequest["user"]["id"], "@"));
$sUsername = $aUsername[0] . "@matrix." . $aUsername[1];

# Prepare SoapConnection to ISPConfig
$context = stream_context_create(array(
    'ssl' => array(
        'verify_peer'       => false,
        'verify_peer_name'  => false,
    )
));


$client = new SoapClient(null, array('location' => $soap_location,
    'uri'      => $soap_uri,
    'trace' => 1,
    'exceptions' => 1,
    'stream_context' => $context));

# Compare login from Matrix to ISPConfig
try {
    if($session_id = $client->login($username, $password))
    {
      $alias_record = $client->mail_alias_get($session_id, array('source' => $sUsername));
      $mail_record = $client->mail_user_get($session_id, array('email' => $alias_record[0]["destination"]));

      if($alias_record[0]["active"] == "y" and password_verify($oMatrixRequest["user"]["password"], $mail_record[0]["password"]))
      {
        $oResult["auth"]["success"] = true;
        $oResult["auth"]["mxid"] = $oMatrixRequest["user"]["id"];
        $oResult["auth"]["profile"]["display_name"] = $mail_record[0]["name"];
        $oResult["auth"]["profile"]["three_pids"][0]["medium"] = "email";
        $oResult["auth"]["profile"]["three_pids"][0]["address"] = $alias_record[0]["destination"];
      }
      else
      {
        $oResult["auth"]["success"] = false;
      }
    }

    if($client->logout($session_id)) {
        echo json_encode($oResult);
/*
        file_put_contents("result.data", json_encode($oResult));
        file_put_contents("matrix.data", json_encode($oMatrixRequest));
        file_put_contents("ispconfig_a.data", json_encode($alias_record));
        file_put_contents("ispconfig_m.data", json_encode($mail_record));
        file_put_contents("matrix_id.data", json_encode($aUsername));
        file_put_contents("matrix_username.data", $sUsername);
*/
    }

} catch (SoapFault $e) {
    $oResult["auth"]["success"] = false;
    file_put_contents("ispconfig_errpr.data", $client->__getLastResponse());
    die(json_encode($oResult)); 
}
?>
