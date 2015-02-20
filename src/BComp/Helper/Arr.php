<?php namespace BComp\Helper;

/**
 * Array Helper
 *
 * @category  Helper
 * @package   Bono
 * @author    Krisan Alfa Timur <krisan47@gmail.com>
 * @copyright 2015 PT Sagara Xinix Solusitama
 */
class Arr
{
    /**
     * Get a list of array except the second attribute
     *
     * $array = [
     *     'name' => 'Alfa',
     *     'sex' => 'Male',
     *     'age' => 23,
     * ];
     *
     * Arr::except($array, ['age']);
     *
     * Output:
     *
     * $array = [
     *     'name' => 'Alfa',
     *     'sex' => 'Male',
     * ]
     *
     * @param  array  $attributes The array you want to filter
     * @param  array  $hidden     List of array keys you want to ignore from your array
     *
     * @return array
     */
    public static function except(array $attributes, array $hidden)
    {
        return array_diff_key($attributes, array_flip($hidden));
    }

    /**
     * Determine if array is empty, works on multidimension array
     *
     * @param array $array Array you want to check whether it's empty or not
     *
     * @return boolean
     */
    public static function isEmpty(array $array)
    {
        if (self::depth($array) > 0) {
            $empty = true;

            foreach ($array as $value) {
                $empty = (empty($value) or is_null($value));
            }

            return $empty;
        }

        return empty($array);
    }

    /**
     * Get a list of array which only if the key is exists on the second argument
     *
     * $array = [
     *     'name' => 'Alfa',
     *     'sex' => 'Male',
     *     'age' => 23,
     * ];
     *
     * Arr::only($array, ['age']);
     *
     * Output:
     *
     * $array = [
     *     'age' => 23,
     * ]
     *
     * @param  array  $attributes Array you want to filter
     * @param  array  $shown      List of array key you want to get from your array
     *
     * @return array
     */
    public static function only(array $attributes, array $shown)
    {
        return array_intersect_key($attributes, array_flip((array) $shown));
    }

    /**
     * Replace your array keys
     *
     * $array = [
     *     ':type_address'     => 'Foo',
     *     ':type_citizenship' => 'Bar',
     *     ':type_city'        => 'Baz',
     *     ':type_country'     => 'Qux',
     * ]
     *
     * Arr::replaceKey($array, ':type', 'user')
     *
     * Will produce
     *
     * $array = [
     *     'user_address'     => 'Foo',
     *     'user_citizenship' => 'Bar',
     *     'user_city'        => 'Baz',
     *     'user_country'     => 'Qux',
     * ]
     *
     * @param  array  $input       [description]
     * @param  [type] $regex       [description]
     * @param  string $replacement [description]
     * @return [type]              [description]
     */
    public static function replaceKey(array $input, $regex, $replacement = '')
    {
        $array = array();

        foreach ($input as $key => $value) {
            $array[str_replace($regex, $replacement, $key)] = $value;
        }

        return $array;
    }

    /**
     * Replace your array value
     *
     * $header = [
     *     ':type_address',
     *     ':type_citizenship',
     *     ':type_city',
     *     ':type_country',
     * ]
     *
     * Arr::replaceValue($header, ':type_', '')
     *
     * Will produce:
     *
     * $header = [
     *     'address',
     *     'citizenship',
     *     'city',
     *     'country',
     * ]
     *
     * @param  array  $input
     * @param  string $search
     * @param  string $replacement
     *
     * @return array
     */
    public static function replaceValue(array $input, $search, $replacement)
    {
        $array = array();

        foreach ($input as $value) {
            $array[] = str_replace($search, $replacement, $value);
        }

        return $array;
    }

    /**
     * Determine your multidimension array depth
     *
     * @param  array  $array [description]
     *
     * @return int
     */
    public static function depth(array $array)
    {
        $maxDepth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::depth($value) + 1;

                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;
    }

    /**
     * Flip your array, mostly comes from stackable form
     * <input name="first-name[]" />
     * <input name="last-name[]" />
     *
     * It produce array like this:
     * [
     *     "first-name" => ["Ganesha", "Krisan", "Farid"],
     *     "last-name"  => ["Muharso", "Timur", "Hidayat"],
     * ]
     *
     * This method will convert the array into this form:
     * [
     *     [
     *         "first-name" => "Ganesha",
     *         "last-name" => "Muharso",
     *     ],
     *     [
     *         "first-name" => "Krisan",
     *         "last-name" => "Timur",
     *     ],
     *     [
     *         "first-name" => "Farid",
     *         "last-name" => "Hidayat",
     *     ],
     * ]
     *
     * So you can loop them, and save them to Norm via:
     *
     * foreach(Arr::normalizeStacked($personInput) as $person) {
     *     Norm::factory('User')->set($person)->save();
     * }
     *
     * @param  array  $array [description]
     *
     * @return array
     */
    public static function normalizeStacked(array $array)
    {
        $data = array();

        for ($i = 0; $i < count(reset($array)); $i++) {
            $data[$i] = array();

            foreach ($array as $key => $value) {
                $data[$i][$key] = $value[$i];
            }
        }

        return $data;
    }

    /**
     * Flatten your multidimension array to one dimension array
     *
     * @param  array  $array
     *
     * @return array
     */
    public static function flatten(array $array)
    {
        $return = array();

        array_walk_recursive($array, function($x) use (&$return) {
            $return[] = $x;
        });

        return $return;
    }
}
