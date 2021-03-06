<?php

class Store extends App {

    public static $path = 'langs';
    public static $storeVarName = 'data';
    public static $toPath;

    public static function init() {
        self::setToPath();
        self::mountTarget();
    }

    private static function setToPath() {
        $path = sprintf('%s/%s/%s.php', dirname(__FILE__).'/..', self::$path, self::$to);
                error_log($path);
        self::$toPath = $path;
    }

    public static function getToPath() {
        return self::$toPath;
    }

    private static function mountTarget() {
        if (!is_file(self::$toPath)) {
            self::create();
        }
        global ${self::$storeVarName};
        include_once self::$toPath;
    }

    private static function create() {
        $file = fopen(self::$toPath, 'w')
                or die('Cannot create file: ' . self::$toPath);
        self::rewrite();
        fclose($file);
        chmod(self::$toPath, 0777); 
    }

    public static function update($hash, $text) {
        $store = self::getAll();
        $store[$hash] = $text;
        self::rewrite($store);
    }

    public static function updateByOrigin($origin, $text) {
        $hash = self::getHashByText($origin);
        self::update($hash, $text);
    }

    public static function rewrite($data = array()) {
        $array = sprintf('$%s = %s', self::$storeVarName, var_export($data, true));
        $str = "<?php\n" . $array . ";\n";
        return file_put_contents(self::$toPath, $str);
    }

    public static function getFrom($text) {
        global ${self::$storeVarName};
        $hash = self::getHashByText($text);
        return isset($data[$hash]) ? $data[$hash] : FALSE;
    }

    public static function getTextByHash($hash) {
        global ${self::$storeVarName};
        return isset($data[$hash]) ? $data[$hash] : FALSE;
    }

    public static function getAll() {
        global ${self::$storeVarName};
        return ${self::$storeVarName};
    }

    public static function getHashByText($text = '') {
        return crc32($text);
    }
}
