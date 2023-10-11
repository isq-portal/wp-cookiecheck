<?php
// require $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
require dirname(__FILE__).'/../../../wp-load.php';

$ISQWPCookieAjax = new ISQWPCookieAjax();
$ISQWPCookie = new ISQWPCookie();

if (!isset($_POST['action'])) {
    // no post action, send forbidden header and exit
    header('HTTP/1.0 403 Forbidden');
    exit();
    // throw new Exception("Action not specified");
}

switch ($_POST['action']) {
    case 'hide':
        // Set cookie
        ISQWPCookieAjax::setCookie('isqWPCookieHidden', 'true', 52, 'weeks');
        header('Content-Type: application/json');
        die(json_encode(['success' => true]));
        break;

    case 'toggle':
        $return    = [];

        // Update if cookie allowed or not
        $choices = ISQWPCookieAjax::getCookie('isqWPCookie');
        if ($choices == false) {
            $choices = [];
            $enabledCookies = $ISQWPCookie->enabledSnippets();
            foreach ($enabledCookies as $name => $label) {
                $choices[$name] = $ISQWPCookieAjax->getConfig('default_setting');
            }
            ISQWPCookieAjax::setCookie('isqWPCookie', ISQWPCookieAjax::encrypt($choices), 52, 'weeks');
        } else {
            $choices = ISQWPCookieAjax::decrypt($choices);
        }
        $choices[$_POST['name']] = $_POST['value'] == 'true' ? 'enabled' : 'disabled';

        // Remove cookies if now disabled
        if ($choices[$_POST['name']] == 'disabled') {
            $removeCookies = $ISQWPCookieAjax->clearCookieGroup($_POST['name']);
            $return['removeCookies'] = $removeCookies;
        }

        $choices = ISQWPCookieAjax::encrypt($choices);
        ISQWPCookieAjax::setCookie('isqWPCookie', $choices, 52, 'weeks');

        header('Content-Type: application/json');
        die(json_encode($return));
        break;

    case 'load':
        $return    = [];

        $removeCookies = [];

        foreach ($ISQWPCookie->enabledSnippets() as $cookie => $label) {
            if (!$ISQWPCookie->isAllowed($cookie)) {
                $removeCookies = array_merge($removeCookies, $ISQWPCookieAjax->clearCookieGroup($cookie));
            }
        }
        $return['removeCookies'] = $removeCookies;

        header('Content-Type: application/json');
        die(json_encode($return));
        break;

    default:
        header('HTTP/1.0 403 Forbidden');
        throw new Exception("Action not recognised");
        break;
}
