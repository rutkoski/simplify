<?php

/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */

/**
 *
 * Language inflector - default english
 *
 */
class Simplify_Inflector
{

  /**
   * Plural forms
   *
   * @var array
   */
  protected static $plural = array(
    'regular' => array('/(quiz)$/i' => '\1zes', '/^(ox)$/i' => '\1en', '/([m|l])ouse$/i' => '\1ice',
      '/(matr|vert|ind)ix|ex$/i' => '\1ices', '/(x|ch|ss|sh)$/i' => '\1es', '/([^aeiouy]|qu)ies$/i' => '\1y',
      '/([^aeiouy]|qu)y$/i' => '\1ies', '/(hive)$/i' => '\1s', '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
      '/sis$/i' => 'ses', '/([ti])um$/i' => '\1a', '/(buffal|tomat)o$/i' => '\1oes', '/(bu)s$/i' => '\1ses',
      '/(alias|status)/i' => '\1es', '/(octop|vir)us$/i' => '\1i', '/(ax|test)is$/i' => '\1es', '/s$/i' => 's',
      '/$/' => 's'),

    'uncountable' => array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep'),

    'irregular' => array('person' => 'people', 'man' => 'men', 'child' => 'children', 'sex' => 'sexes',
      'move' => 'moves'));

  /**
   * Singular forms
   *
   * @var array
   */
  protected static $singular = array(
    'regular' => array('/(quiz)zes$/i' => '\\1', '/(matr)ices$/i' => '\\1ix', '/(vert|ind)ices$/i' => '\\1ex',
      '/^(ox)en/i' => '\\1', '/(alias|status)es$/i' => '\\1', '/([octop|vir])i$/i' => '\\1us',
      '/(cris|ax|test)es$/i' => '\\1is', '/(shoe)s$/i' => '\\1', '/(o)es$/i' => '\\1', '/(bus)es$/i' => '\\1',
      '/([m|l])ice$/i' => '\\1ouse', '/(x|ch|ss|sh)es$/i' => '\\1', '/(m)ovies$/i' => '\\1ovie',
      '/(s)eries$/i' => '\\1eries', '/([^aeiouy]|qu)ies$/i' => '\\1y', '/([lr])ves$/i' => '\\1f', '/(tive)s$/i' => '\\1',
      '/(hive)s$/i' => '\\1', '/([^f])ves$/i' => '\\1fe', '/(^analy)ses$/i' => '\\1sis',
      '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\\1\\2sis', '/([ti])a$/i' => '\\1um',
      '/(n)ews$/i' => '\\1ews', '/s$/i' => ''),

    'uncountable' => array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep'),

    'irregular' => array('person' => 'people', 'man' => 'men', 'child' => 'children', 'sex' => 'sexes',
      'move' => 'moves'));

  /**
   * Pluralizes nouns
   *
   * @param string $word noun to pluralize
   * @return string plural noun
   */
  public static function pluralize($word)
  {
    $lowercased_word = strtolower($word);

    foreach (Simplify_Inflector::$plural['uncountable'] as $_uncountable) {
      if (substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
        return $word;
      }
    }

    foreach (Simplify_Inflector::$plural['irregular'] as $_plural => $_singular) {
      if (preg_match('/(' . $_plural . ')$/i', $word, $arr)) {
        return preg_replace('/(' . $_plural . ')$/i', substr($arr[0], 0, 1) . substr($_singular, 1), $word);
      }
    }

    foreach (Simplify_Inflector::$plural['regular'] as $rule => $replacement) {
      if (preg_match($rule, $word)) {
        return preg_replace($rule, $replacement, $word);
      }
    }

    return false;
  }

  /**
   * Singularizes nouns
   *
   * @param string $word noun to singularize
   * @return string singular noun
   */
  public static function singularize($word)
  {
    $lowercased_word = strtolower($word);
    foreach (Simplify_Inflector::$singular['uncountable'] as $_uncountable) {
      if (substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
        return $word;
      }
    }

    foreach (Simplify_Inflector::$singular['irregular'] as $_plural => $_singular) {
      if (preg_match('/(' . $_singular . ')$/i', $word, $arr)) {
        return preg_replace('/(' . $_singular . ')$/i', substr($arr[0], 0, 1) . substr($_plural, 1), $word);
      }
    }

    foreach (Simplify_Inflector::$singular['regular'] as $rule => $replacement) {
      if (preg_match($rule, $word)) {
        return preg_replace($rule, $replacement, $word);
      }
    }

    return $word;
  }

  /**
   *
   * @param string $word
   * @param string $uppercase
   */
  public static function titleize($word, $uppercase = '')
  {
    $uppercase = $uppercase == 'first' ? 'ucfirst' : 'ucwords';
    return $uppercase(Simplify_Inflector::humanize(Simplify_Inflector::underscore($word)));
  }

  /**
   *
   * @param string $word
   * @return string
   */
  public static function camelize($word)
  {
    return str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/', ' ', $word)));
  }

  /**
   *
   * @param string $word
   * @return string
   */
  public static function underscore($word)
  {
    return strtolower(
      preg_replace('/[^A-Z^a-z^0-9]+/', '_',
        preg_replace('/([a-z\d])([A-Z])/', '\1_\2', preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', $word))));
  }

  /**
   *
   * @param string $word
   * @param string $uppercase
   */
  public static function humanize($word, $uppercase = '')
  {
    $uppercase = $uppercase == 'all' ? 'ucwords' : 'ucfirst';
    return $uppercase(str_replace('_', ' ', preg_replace('/_id$/', '', $word)));
  }

  /**
   *
   * @param string $word
   * @return string
   */
  public static function variablize($word)
  {
    $word = Simplify_Inflector::camelize($word);
    return strtolower($word[0]) . substr($word, 1);
  }

  /**
   *
   * @param string $class_name
   * @return string
   */
  public static function tableize($class_name)
  {
    return Simplify_Inflector::pluralize(Simplify_Inflector::underscore($class_name));
  }

  /**
   *
   * @param string $table_name
   * @return string
   */
  public static function classify($table_name)
  {
    return Simplify_Inflector::camelize(Simplify_Inflector::singularize($table_name));
  }

  /**
   *
   * @param int $number
   * @return string
   */
  public static function ordinalize($number)
  {
    if (in_array(($number % 100), range(11, 13))) {
      return $number . 'th';
    }
    else {
      switch (($number % 10)) {
        case 1 :
          return $number . 'st';
          break;
        case 2 :
          return $number . 'nd';
          break;
        case 3 :
          return $number . 'rd';
        default :
          return $number . 'th';
          break;
      }
    }
  }

}
