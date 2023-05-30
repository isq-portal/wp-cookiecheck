<?php
class GDPRCookieAjax
{
    public function __construct()
    {
        // require $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    private function loadConfig()
    {
        $configTable = $this->wpdb->prefix.'isq_cookiecheck_config';
        $dbConfigs = $this->wpdb->get_results("SELECT * FROM $configTable");
        foreach ($dbConfigs as $dbConfig) {
            $this->config[$dbConfig->name] = $dbConfig->value;
        }
    }

    public function getConfig($name)
    {
        if (!isset($this->config)) {
            $this->loadConfig();
        }
        if (!isset($this->config[$name])) {
            return false;
        }
        return $this->config[$name];
    }

    public function clearCookieGroup($slug)
    {
        $snippetsTable = $this->wpdb->prefix.'isq_cookiecheck_snippets';
        $cookiesTable = $this->wpdb->prefix.'isq_cookiecheck_snippet_cookies';
        $clearCookies = $this->wpdb->get_results(
            "SELECT c.*
            FROM $cookiesTable c
            LEFT JOIN $snippetsTable s
                ON c.snippet_id = s.id
            WHERE s.slug = '$slug'"
        );

        foreach ($clearCookies as $cookieObj) {
            $cookie = [
                'name'   => $this->replaceSpecials($cookieObj->name),
                'path'   => $this->replaceSpecials($cookieObj->path),
                'domain' => $this->replaceSpecials($cookieObj->domain),
            ];
            self::destroyCookie($cookie['name'], $cookie['path'], $cookie['domain']);
            $return[] = $cookie;
        }

        return $return;
    }

    public function replaceSpecials($string)
    {
        if (strrpos($string, '{*') === false) {
            return $string;
        }
        if (preg_match('/{\* (.*?) \*}/', $string, $match) == 1) {
            $specialReplace = $match[0];
            $specialTerm    = $match[1];

            $split       = explode('|', $specialTerm);
            $specialTerm = explode('.', $split[0]);
            $format      = isset($split[1]) ? $split[1] : '';

            switch ($specialTerm[0]) {
                case 'config':
                    $replacement = $this->getConfig($specialTerm[1]);
                    break;
                case 'snippets':
                    if ($specialTerm[2] == 'variables') {
                        $replacement = $this->getVariable($specialTerm[1], $specialTerm[3]);
                    }
                    break;
                default:
                    $replacement = '';
                    break;
            }

            switch ($format) {
                case 'hyp-unds':
                    $replacement = str_replace('-', '_', $replacement);
                    break;
            }

            $string = str_replace($specialReplace, $replacement, $string);
        }
        return $string;
    }

    public function getVariable($snippetSlug, $variableSlug)
    {
        $snippetsTable          = $this->wpdb->prefix.'isq_cookiecheck_snippets';
        $snippetVariablesTable = $this->wpdb->prefix.'isq_cookiecheck_snippet_variables';
        $return = $this->wpdb->get_row(
            "SELECT v.value
            FROM $snippetVariablesTable v
            LEFT JOIN $snippetsTable s
                ON s.id = v.snippet_id
            WHERE v.slug = '$variableSlug'
                AND s.slug = '$snippetSlug'"
        );
        return $return ? $return->value : '';
    }

    public static function encrypt($value)
    {
        $return = json_encode($value);
        return $return;
    }

    public static function decrypt($value)
    {
        $value = str_replace('\"', '"', $value);
        $return = json_decode($value, true);
        return $return;
    }

    public static function getCookie($name)
    {
        // If cookie exists - return it, otherwise return false
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
    }

    public static function destroyCookie($name, $path = '', $domain = '')
    {
        // Set cookie expiration to 1 hour ago
        return setcookie($name, '', time() - 3600, $path, $domain);
    }

     public static function setCookie(
        $name,
        $value,
        $lifetime = 30,
        $lifetimePeriod = 'days',
        $domain = '/',
        $secure = false
    ) {
        // Validate parameters
        self::validateSetCookieParams($name, $value, $lifetime, $domain, $secure);

        // Calculate expiry
        $expiry = strtotime('+'.$lifetime.' '.$lifetimePeriod);

        // Set cookie
        return setcookie($name, $value, $expiry, $domain, $secure);
    }

    public static function validateSetCookieParams($name, $value, $lifetime, $domain, $secure)
    {
        // Types of parameters to check
        $paramTypes = [
            // Type => Array of variables
            'string' => [$name, $value, $domain],
            'int'    => [$lifetime],
            'bool'   => [$secure],
        ];

        // Validate basic parameters
        $validParams = self::basicValidationChecks($paramTypes);

        // Ensure parameters are still valid
        if (!$validParams) {
            // Failed parameter check
            header('HTTP/1.0 403 Forbidden');
            throw new \Exception("Incorrect parameter passed to Cookie::set");
        }

        return true;
    }

    public static function basicValidationChecks($paramTypes)
    {
        foreach ($paramTypes as $type => $variables) {
            $functionName = 'is_'.$type;
            foreach ($variables as $variable) {
                if (!$functionName($variable)) {
                    return false;
                }
            }
        }
        return true;
    }
}
?>
