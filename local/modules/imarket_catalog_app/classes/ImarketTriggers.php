<?
/**
 * Send errors in telegram
 */

if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../..');
}

use ImarketHeplers\ImarketLogger;

if (!class_exists("ImarketHeplers\ImarketLogger")) {
    require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/ImarketLogger.php');
}


class ImarketTriggers {
    private $Logger; // logger
    private $adminTelegram = [];//[867209420, 308810684, 418005403]; // users id whom will be send message
    private $token = '';//'696826322:AAE0IVmkss4tU1NA7_bvT8QBAoNcgTNn2b8'; // telegram bot token
    private $arData = []; // all data

    public function __construct () {
        $this->Logger = new ImarketLogger("/upload/log/ImarketTriggers/");
    }

    /**
     * set the errors
     * @param array $error error messages
     *
     * @return bool
     */
    public function SetError($errors = []) {
        $this->Logger->log("LOG", "Try to set trigger errors");

        if (empty($errors)) {
            $this->Logger->log("ERROR", "Error array is empty");
            return false;
        }

        foreach ($errors as $error) {
            $this->arData["ERRORS"][] = $error;
            $this->Logger->log("LOG", "Set new error '".$error."'");
        }

        $this->Logger->log("LOG", "All errors are setted");

        return true;
    }

    /**
     * return all current errors
     * @return mixed
     */
    public function GetErrors() {
        return $this->arData["ERRORS"];
    }

    /**
     * send all messages and delete
     * @return bool
     */
    public function SendTriggerErrors($userId = 0) {
        $this->Logger->log("LOG", "Try to send errors in telegram");

        if (empty($this->arData["ERRORS"])) {
            $this->Logger->log("ERROR", "Error array is empty");
            return false;
        }

        $text = '';

        foreach ($this->arData["ERRORS"] as $error) {
            if (!empty(trim($error))) {
                $text .= $error."\r\n";
            }
        }

        if (!empty($text)) {
            foreach ($this->adminTelegram as $telegramId) {
                if (!empty($userId) && $userId != $telegramId) {
                    continue;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token . '/sendMessage');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, 'chat_id=' . $telegramId . '&text=' . urlencode($text));
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);

                $curlResult = curl_exec($ch);
                curl_close($ch);
                $responsive = json_decode($curlResult, true);

                $this->Logger->log("LOG", "Responsive ".print_r($responsive, true));

                if (!strlen($curlResult)) {
                    $this->Logger->log("ERROR", "Message not send!");
                    return false;
                } elseif (!empty($responsive["error_code"]) && $responsive["error_code"] == 400) {
                    $firstError = $this->arData["ERRORS"][0];
                    $this->arData["ERRORS"] = null;
                    $this->SetError([$firstError]);
                    $this->SendTriggerErrors();
                }

                $this->Logger->log("LOG", "Message send successfully");
                $this->arData["ERRORS"] = null;
            }
        }

        return true;
    }
}
