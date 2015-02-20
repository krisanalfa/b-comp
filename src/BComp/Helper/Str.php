<?php namespace BComp\Helper;

use RuntimeException;

class Str
{
    /**
     * Convert a value to camel case.
     *
     * Produce: static::camel("my_method") // "myMethod"
     *
     * @param  string  $value
     *
     * @return string
     */
    public static function camel($value)
    {
        return lcfirst(static::studly($value));
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * Produce: static::contains('my_method', 'x'); // false
     *          static::contains('my_method', 'd'); // true
     *          static::contains('my_method', ['x', 'd']); // true
     *
     * @param  string        $haystack
     * @param  string|array  $needles
     *
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' and strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * Produce: static::endsWith('my_method', 'd'); // true
     *          static::endsWith('my_method', 'x'); // false
     *          static::endsWith('my_method', ['x', 'd']); // true
     *
     * @param string  $haystack
     * @param string|array  $needles
     *
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle == substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * Produce: static::is('*.php', 'myFile.php'); // true
     *          static::is('php', 'myFile.php'); // false
     *
     * @param  string  $pattern
     * @param  string  $value
     *
     * @return bool
     */
    public static function is($pattern, $value)
    {
        if ($pattern == $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern).'\z';

        return (bool) preg_match('#^'.$pattern.'#', $value);
    }

    /**
     * Return the length of the given string.
     *
     * @param  string  $value
     *
     * @return int
     */
    public static function length($value)
    {
        return mb_strlen($value);
    }

    /**
     * Limit the number of characters in a string.
     *
     * Produce: static::limit("You see me because you haven't overriden templates yet or default routes. May be this is your fist journey through the world of Bono. I wish you will enjoy and get comfy to the world of productive application development.");
     * To     : "You see me because you haven't overriden templates yet or default routes. May be this is your fist j..."
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     *
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')).$end;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     *
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value);
    }

    /**
     * Limit the number of words in a string.
     *
     * Produce: static::words("You see me because you haven't overriden templates yet or default routes. May be this is your fist journey through the world of Bono. I wish you will enjoy and get comfy to the world of productive application development.", 10));
     * To     : "You see me because you haven't overriden templates yet or..."
     *
     * @param  string  $value
     * @param  int     $words
     * @param  string  $end
     *
     * @return string
     */
    public static function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if ( ! isset($matches[0])) {
            return $value;
        }

        if (strlen($value) == strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }

    /**
     * Parse a Class@method style callback into class and method.
     *
     * Produce: static::parseCallback('Class@methodName', 'defaultMethod'); // ["Class", "methodName"]
     *          static::parseCallback('ClassName', 'defaultMethod'); // ["ClassName", "defaultMethod"]
     *
     * @param  string  $callback
     * @param  string  $default
     *
     * @return array
     */
    public static function parseCallback($callback, $default)
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : array($callback, $default);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int     $length
     * @return string
     *
     * @throws RuntimeException
     */
    public static function random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false) {
                throw new RuntimeException('Unable to generate random string.');
            }

            return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
        }

        return static::quickRandom($length);
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int     $length
     *
     * @return string
     */
    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param  string  $value
     *
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value);
    }

    /**
     * Convert the given string to title case.
     *
     * Produce: static::title('this is a title'); // "This Is A Title"
     *
     * @param  string  $value
     *
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * Produce: static::slug('mr. ganesha, this is a title'); // "mr-ganesha-this-is-a-title"
     *
     * @param  string  $title
     * @param  string  $separator
     *
     * @return string
     */
    public static function slug($title, $separator = '-')
    {
        // Convert all dashes/underscores into separator
        $flip = $separator == '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Convert a string to snake case.
     *
     * Produce: static::snake('theCamelCaseVariable'); // "the_camel_case_variable"
     *
     * @param  string  $value
     * @param  string  $delimiter
     *
     * @return string
     */
    public static function snake($value, $delimiter = '_')
    {
        $replace = '$1'.$delimiter.'$2';

        return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * Produce: static::startsWith('theCamelCaseVariable', 't'); // true
     *          static::startsWith('theCamelCaseVariable', 'z'); // false
     *          static::startsWith('theCamelCaseVariable', ['t', 'z']); // true
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     *
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' and strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a value to studly caps case.
     *
     * Produce: static::studly('the_snake_case_class_name'); // "TheSnakeCaseClassName"
     *
     * @param  string  $value
     *
     * @return string
     */
    public static function studly($value)
    {
        $value = ucwords(str_replace(array('-', '_'), ' ', $value));

        return str_replace(' ', '', $value);
    }
}
