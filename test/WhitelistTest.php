<?php

/**
 * @see       https://github.com/laminas/laminas-filter for the canonical source repository
 * @copyright https://github.com/laminas/laminas-filter/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-filter/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Filter;

use Laminas\Filter\FilterPluginManager;
use Laminas\Filter\Whitelist as WhitelistFilter;
use Laminas\Stdlib\ArrayObject;

/**
 * @group      Laminas_Filter
 */
class WhitelistTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $filter = new WhitelistFilter(array(
            'list'    => array('test', 1),
            'strict'  => true,
        ));

        $this->assertEquals(true, $filter->getStrict());
        $this->assertEquals(array('test', 1), $filter->getList());
    }

    public function testConstructorDefaults()
    {
        $filter = new WhitelistFilter();

        $this->assertEquals(false, $filter->getStrict());
        $this->assertEquals(array(), $filter->getList());
    }

    public function testWithPluginManager()
    {
        $pluginManager = new FilterPluginManager();
        $filter = $pluginManager->get('whitelist');

        $this->assertInstanceOf('Laminas\Filter\Whitelist', $filter);
    }

    public function testNullListShouldThrowException()
    {
        $this->setExpectedException('Laminas\Stdlib\Exception\InvalidArgumentException');
        $filter = new WhitelistFilter(array(
            'list' => null,
        ));
    }

    public function testTraversableConvertsToArray()
    {
        $array = array('test', 1);
        $obj = new ArrayObject(array('test', 1));
        $filter = new WhitelistFilter(array(
            'list' => $obj,
        ));
        $this->assertEquals($array, $filter->getList());
    }

    public function testSetStrictShouldCastToBoolean()
    {
        $filter = new WhitelistFilter(array(
            'strict' => 1
        ));
        $this->assertSame(true, $filter->getStrict());
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     * @dataProvider defaultTestProvider
     */
    public function testDefault($value, $expected)
    {
        $filter = new WhitelistFilter();
        $this->assertSame($expected, $filter->filter($value));
    }

    /**
     * @param bool $strict
     * @param array $testData
     * @dataProvider listTestProvider
     */
    public function testList($strict, $list, $testData)
    {
        $filter = new WhitelistFilter(array(
            'strict' => $strict,
            'list'   => $list,
        ));
        foreach ($testData as $data) {
            list($value, $expected) = $data;
            $message = sprintf(
                '%s (%s) is not filtered as %s; type = %s',
                var_export($value, true),
                gettype($value),
                var_export($expected, true),
                $strict
            );
            $this->assertSame($expected, $filter->filter($value), $message);
        }
    }

    public static function defaultTestProvider()
    {
        return array(
            array('test',   null),
            array(0,        null),
            array(0.1,      null),
            array(array(),  null),
            array(null,     null),
        );
    }

    public static function listTestProvider()
    {
        return array(
            array(
                true, //strict
                array('test', 0),
                array(
                    array('test',   'test'),
                    array(0,        0),
                    array(null,     null),
                    array(false,    null),
                    array(0.0,      null),
                    array(array(),  null),
                ),
            ),
            array(
                false, //not strict
                array('test', 0),
                array(
                    array('test',   'test'),
                    array(0,        0),
                    array(null,     null),
                    array(false,    false),
                    array(0.0,      0.0),
                    array(0.1,      null),
                    array(array(),  null),
                ),
            ),
        );
    }
}
