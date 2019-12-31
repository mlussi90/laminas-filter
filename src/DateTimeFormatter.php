<?php

/**
 * @see       https://github.com/laminas/laminas-filter for the canonical source repository
 * @copyright https://github.com/laminas/laminas-filter/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-filter/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Filter;

use DateTime;

class DateTimeFormatter extends AbstractFilter
{
    /**
     * A valid format string accepted by date()
     *
     * @var string
     */
    protected $format = DateTime::ISO8601;

    /**
     * Sets filter options
     *
     * @param array|Traversable $options
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set the format string accepted by date() to use when formatting a string
     *
     * @param  string $format
     * @return \Laminas\Filter\DateTimeFormatter
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Filter a datetime string by normalizing it to the filters specified format
     *
     * @param  string $value
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function filter($value)
    {
        try {
            $result = $this->normalizeDateTime($value);
        } catch (\Exception $e) {
            // DateTime threw an exception, an invalid date string was provided
            throw new Exception\InvalidArgumentException('Invalid date string provided', $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * Normalize the provided value to a formatted string
     *
     * @param string|int|DateTime $value
     * @returns string
     */
    protected function normalizeDateTime($value)
    {
        if ($value === '' || $value === null) {
            return $value;
        } elseif (is_int($value)) {
            $value = new DateTime('@' . $value);
        } elseif (!$value instanceof DateTime) {
            $value = new DateTime($value);
        }

        return $value->format($this->format);
    }
}
