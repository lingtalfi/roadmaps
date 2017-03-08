<?php


/**
 * $useHttpBuildQuery: in some cases you need to pass {tags} in the uri, so not using
 * http_build_query here is handy.
 */
function url($url, array $params = null, $mergeParams = true, $useHttpBuildQuery = true)
{
    if (null === $url) {
        $url = Spirit::get('uri');
    }
    $ret = URL_PREFIX . $url;
    if (null !== $params) {
        if (true === $mergeParams) {
            $params = array_replace($_GET, $params);
        }
        $end = '';
        if (true === $useHttpBuildQuery) {
            $end = http_build_query($params);
        } else {
            $i = 0;
            foreach ($params as $k => $v) {
                if (0 !== $i++) {
                    $end .= '&';
                }
                $end .= $k . '=' . $v;
            }
        }
        $ret = URL_PREFIX . $url . '?' . $end;
    }
    return htmlspecialchars($ret);
}


function __($identifier, $context = 'default', array $tags = [])
{
    static $terms = [];


    // load definitions for the given context
    if (array_key_exists($context, $terms)) {
        $defs = $terms[$context];
    } else {
        $defs = [];
        $file = APP_DICTIONARY_PATH . '/' . $context . '.php';
        if (false === file_exists($file)) {
            throw new \Exception("translation file not found: " . $file);
            return $identifier;
        }
        require $file;
        $terms[$context] = $defs;
    }


    // use the loaded definitions and check if there is a matching identifier
    if (array_key_exists($identifier, $defs)) {
        $value = $defs[$identifier];

    } else {
        // error?
        $value = $identifier;
//        throw new \Exception("__ error: dictionary term not found: " . $identifier);
    }
    if (count($tags) > 0) {
        $ks = array_map(function ($v) {
            return '{' . $v . '}';
        }, array_keys($tags));
        $vs = array_values($tags);
        $value = str_replace($ks, $vs, $value);
    }
    return $value;
}

function ___()
{
    return htmlspecialchars(call_user_func_array('__', func_get_args()));
}


function linkt($text, $href, $external = false)
{
    $target = '';
    if (true === $external) {
        $target = 'target="_blank"';
    }
    return '<a ' . $target . ' href="' . $href . '">' . $text . '</a>';
}


/**
 * relPath
 * currently originates from https://github.com/lingtalfi/nullos-admin/tree/master/doc
 */
function doclink($relPath)
{
    return "https://github.com/lingtalfi/nullos-admin/tree/master/doc/" . $relPath;
}

//------------------------------------------------------------------------------/
// BONUS FUNCTIONS, SO HANDFUL... (a huge time saver in the end)
//------------------------------------------------------------------------------/
if (!function_exists('a')) {
    function a()
    {
        foreach (func_get_args() as $arg) {
            ob_start();
            var_dump($arg);
            $output = ob_get_clean();
            if ('1' !== ini_get('xdebug.default_enable')) {
                $output = preg_replace("!\]\=\>\n(\s+)!m", "] => ", $output);
            }
            if ('cli' === PHP_SAPI) {
                echo $output;
            } else {
                echo '<pre>' . $output . '</pre>';
            }
        }
    }

    function az()
    {
        call_user_func_array('a', func_get_args());
        exit;
    }
}

