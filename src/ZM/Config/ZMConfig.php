<?php


namespace ZM\Config;


class ZMConfig
{
    private static $path = ".";

    private static $env = "";

    private static $config = [];

    public static $last_error = "";

    public static function setDirectory($path = ".")
    {
        return self::$path = realpath($path);
    }

    public static function env($env = "")
    {
        self::$env = $env;
    }

    public static function get($name, $key = null)
    {
        if (isset(self::$config[$name])) $r = self::$config[$name];
        else $r = self::loadConfig($name);
        if($r === false) return false;
        if($key !== null) return $r[$key] ?? null;
        else return $r;
    }

    private static function loadConfig($name)
    {
        $ext = [".php", ".json"];
        $env = ["", ".development", ".staging", ".production"];
        foreach ($ext as $ext_name) {
            if (self::$env === '') {
                foreach ($env as $env_name) {
                    if (file_exists(self::$path . "/" . $name . $env_name . $ext_name)) {
                        return self::storeConfig($name, self::$path . "/" . $name . $env_name . $ext_name, $ext_name);
                    }
                }
            } else {
                if (file_exists(self::$path . "/" . $name . "." . self::$env . $ext_name)) {
                    return self::storeConfig($name, self::$path . "/" . $name . "." . self::$env . $ext_name, $ext_name);
                } elseif (file_exists(self::$path . "/" . $name . $ext_name)) {
                    return self::storeConfig($name, self::$path . "/" . $name . $ext_name, $ext_name);
                } else {
                    self::$last_error = "你已指定环境 '" . self::$env . "', 但是配置文件 " . $name . "." . self::$env . "(.php/.json) 不存在，请检查";
                    return false;
                }
            }
        }
        self::$last_error = "未找到名称为 " . $name . " 的config文件，请检查文件名后缀是否为 \"json\" 或 \"php\"。";
        return false;
    }

    private static function storeConfig($name, $string, $ext_name)
    {
        switch ($ext_name) {
            case ".php":
                $r = include_once $string;
                if (is_array($r)) {
                    return self::$config[$name] = $r;
                } else {
                    self::$last_error = "php配置文件include失败，请检查终端warning错误";
                    return false;
                }
            case ".json":
                $r = json_decode(file_get_contents($string), true);
                if (is_array($r)) {
                    return self::$config[$name] = $r;
                } else {
                    self::$last_error = "json反序列化失败，请检查文件内容";
                    return false;
                }
            default:
                self::$last_error = "内部错误";
                return false;
        }
    }
}