<?namespace ImarketHeplers;

/**
 * Class ImarketLogger
 * @package ImarketHeplers
 */
class ImarketLogger {
    private $loggerLogPath = '/upload/log/Logger/';
    private $logFolder = '/upload/log/';
    public $lastError = '';
    public $lastLog = '';

    /**
     * ImarketLogger constructor.
     * @param string $folderPath
     */
    public function __construct ($folderPath = "") {
        if (!empty($folderPath)) {
            $this->loggerLogPath = $folderPath;
        }

        if (!file_exists($_SERVER["DOCUMENT_ROOT"].$this->logFolder)) {
            mkdir($_SERVER["DOCUMENT_ROOT"].$this->logFolder, 0777, true);
        }
    }

    /**
     * @param int $type [ERROR, LOG]
     * @param string $str
     * @return bool
     */
    public function log($type = "LOG", $str = "", $arr = []) {
        if (empty($type)) {
            $this->log("ERROR", 'No logger type');
            return $this->lastError;
        }

        if (empty($str)) {
            $this->log("ERROR", 'Empty logger string');
            return $this->lastError;
        }

        $str = date("d.m.Y H:i:s")." ".$type." - ".print_r($str,true);
        $defaultFileName = '.'.date("d.m.Y").'_loggerLog';

        switch ($type) {
            case "ERROR":
                $fileName = '.'.date("d.m.Y").'_loggerErrors';
                $this->lastError = ["ERROR" => $str];
                $str .= "\r\nBug trace\r\n".print_r($arr, true);
                $str .= "\r\nBug trace\r\n".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true);
                break;
            case "LOG":
            case "DEBUG":
                $fileName = '.'.date("d.m.Y").'_loggerLog';
                $this->lastLog = $str;
                break;
            default:
                $this->logger("ERROR", 'Unknown type for log');
                return $this->lastError;
                break;
        }

        if (!is_dir($_SERVER["DOCUMENT_ROOT"].$this->loggerLogPath)) {
            mkdir($_SERVER["DOCUMENT_ROOT"].$this->loggerLogPath, 0777);
        }

        if ($type != "LOG") {
            $filePath = $_SERVER["DOCUMENT_ROOT"].$this->loggerLogPath.$fileName;
        }

        $defaultFilePath = $_SERVER["DOCUMENT_ROOT"].$this->loggerLogPath.$defaultFileName;

        $str .= "\r\n";
        if ($type == "ERROR") {
            file_put_contents($filePath, $str, FILE_APPEND);
        }

        file_put_contents($defaultFilePath, $str, FILE_APPEND);

        return true;
    }
}
?>