<?php
use App\Core\App;
/*
 * This function redirects the user to a page.
 */
function redirect($path): void
{
    header("Location: /{$path}");
}

/*
 * This function returns the view of a page.
 */
function view($name, $data = [])
{
    extract($data);
    return require "../app/views/{$name}.php";
}

/*
 * This function is used for dying and dumping.
 */
function dd($value): void
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}

/*
 * This function is used for generating pagination links.
 */
function paginate($table, $page, $limit, $count): string
{
    $totalPages = ceil($count / $limit);
    $offset = ($page - 1) * $limit;
    $output = "<span class='text-dark'>";

    $showFirstLast = App::Config()['pagination']['show_first_last'];
    
    if ($showFirstLast && $page > 1) {
        $output .= "<a href='/{$table}/1' class='text-primary'>1</a> ";
    }
    
    if ($page > 1) {
        $prev = $page - 1;
        $output .= "<a href='/{$table}/{$prev}' class='text-primary'>назад</a> ";
    }

    $output .= " стр. $page ";
    
    if ($count > ($offset + $limit)) {
        $next = $page + 1;
        $output .= "<a href='/{$table}/{$next}' class='text-primary'>вперед</a> ";
    }
    
    if ($showFirstLast && $page < $totalPages) {
        $output .= "<a href='/{$table}/{$totalPages}' class='text-primary'>последняя</a>";
    }

    $output .= "</span>";
    return $output;
}

/*
 * This function displays a session variable's value if it exists.
*/
function session($name) {
    return $_SESSION[$name] ?? "";
}

/*
 * This function displays a session variable's value and unsets it if it exists.
 */
function session_once($name) {
    if (isset($_SESSION[$name])) {
        $value = $_SESSION[$name];
        unset($_SESSION[$name]);
        return $value;
    }
    return "";
}

/*
 * This function enables displaying of errors in the web browser.
 */
function display_errors(): void
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function now_date(): string
{
    return date('Y-m-d H:i:s');
}

if (!function_exists('env')) {

    /**
     * Gets an environment variable from available sources, and provides emulation
     * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
     * IIS, or SCRIPT_NAME in CGI mode). Also exposes some additional custom
     * environment information.
     *
     * @param string $key Environment variable name.
     * @return string|bool|null Environment variable setting.
     */
    function env($key) {
        if ($key === 'HTTPS') {
            if (isset($_SERVER['HTTPS'])) {
                return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            }
            return (strpos(env('SCRIPT_URI'), 'https://') === 0);
        }

        if ($key === 'SCRIPT_NAME') {
            if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
                $key = 'SCRIPT_URL';
            }
        }

        $val = null;
        if (isset($_SERVER[$key])) {
            $val = $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            $val = $_ENV[$key];
        } elseif (getenv($key) !== false) {
            $val = getenv($key);
        }

        if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
            $addr = env('HTTP_PC_REMOTE_ADDR');
            if ($addr !== null) {
                $val = $addr;
            }
        }

        if ($val !== null) {
            return $val;
        }

        switch ($key) {
            case 'DOCUMENT_ROOT':
                $name = env('SCRIPT_NAME');
                $filename = env('SCRIPT_FILENAME');
                $offset = 0;
                if (!strpos($name, '.php')) {
                    $offset = 4;
                }
                return substr($filename, 0, -(strlen($name) + $offset));
            case 'PHP_SELF':
                return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
            case 'CGI_MODE':
                return (PHP_SAPI === 'cgi');
            case 'HTTP_BASE':
                $host = env('HTTP_HOST');
                $parts = explode('.', $host);
                $count = count($parts);

                if ($count === 1) {
                    return '.' . $host;
                } elseif ($count === 2) {
                    return '.' . $host;
                } elseif ($count === 3) {
                    $gTLD = array(
                        'aero',
                        'asia',
                        'biz',
                        'cat',
                        'com',
                        'coop',
                        'edu',
                        'gov',
                        'info',
                        'int',
                        'jobs',
                        'mil',
                        'mobi',
                        'museum',
                        'name',
                        'net',
                        'org',
                        'pro',
                        'tel',
                        'travel',
                        'xxx'
                    );
                    if (in_array($parts[1], $gTLD)) {
                        return '.' . $host;
                    }
                }
                array_shift($parts);
                return '.' . implode('.', $parts);
        }
        return null;
    }

}