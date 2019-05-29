<?php

namespace Dewep\Tests;

use Dewep\Http\Objects\Base;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    /** @var Base */
    private $tob;

    protected function setUp()
    {
        $this->tob = new Base();
    }

    public function testHas()
    {
        self::assertFalse($this->tob->has('a'));
        $this->tob->set('a', 1);
        self::assertTrue($this->tob->has('a'));
        $this->tob->remove('a');
        self::assertFalse($this->tob->has('a'));
    }

    public function testReplace()
    {
        $this->tob->set('a', 1);
        self::assertEquals($this->tob->get('a'), 1);
        self::assertNotEquals($this->tob->get('a'), 2);
        $this->tob->replace(['a' => 2]);
        self::assertEquals($this->tob->get('a'), 2);
    }

    public function testGet()
    {
        self::assertNull($this->tob->get('a'));
        $this->tob->set('a', 1);
        self::assertEquals($this->tob->get('a'), 1);
    }

    public function testClear()
    {
        $this->tob->set('a', 3);
        $this->tob->set('b', 6);
        self::assertEquals($this->tob->get('a'), 3);
        self::assertEquals($this->tob->get('b'), 6);
        $this->tob->clear();
        self::assertNull($this->tob->get('a'));
        self::assertNull($this->tob->get('b'));
    }

    public function testJsonSerialize()
    {
        $this->tob->set('aaa', 3);
        self::assertEquals(json_encode($this->tob), '{"Aaa":3}');
    }

    public function testSet()
    {
        self::assertNull($this->tob->get('aaa'));
        $this->tob->set('AAA', 1);
        self::assertEquals($this->tob->get('aaa'), 1);
    }

    public function testRemove()
    {
        $this->tob->set('AAA', 1);
        self::assertEquals($this->tob->get('aaa'), 1);
        $this->tob->remove('AAA');
        self::assertNull($this->tob->get('aaa'));
    }

    public function testAll()
    {
        $this->tob->set('aaa_aaa', 3);
        self::assertEquals($this->tob->all(), ['aaa-aaa' => 3]);
    }

    public function testAllOrig()
    {
        $this->tob->set('aaa_aaa', 3);
        self::assertEquals($this->tob->allOrig(), ['Aaa-Aaa' => 3]);
    }

    public function testKeys()
    {
        $this->tob->set('aaa_aaa', 3);
        $this->tob->set('aaa_bbb', 3);
        self::assertEquals($this->tob->keys(), ['aaa-aaa', 'aaa-bbb']);
    }
}
