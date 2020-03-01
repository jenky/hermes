<?php

namespace Jenky\Hermes\Test;

use GuzzleHttp\ClientInterface;

use function Jenky\Hermes\array_merge_recursive_distinct;

class UnitTest extends TestCase
{
    public function test_client_is_instance_of_guzzle()
    {
        $this->assertInstanceOf(ClientInterface::class, guzzle()->channel());
    }

    public function test_array_merge_recursive_distinct()
    {
        $this->assertIsArray($empty = array_merge_recursive_distinct());
        $this->assertEmpty($empty);

        $merged1 = array_merge_recursive_distinct([]);

        $this->assertIsArray($merged1);
        $this->assertEmpty($merged1);
        $this->assertEquals([], $merged1);

        $merged2 = array_merge_recursive_distinct($array1 = ['foo' => 'bar']);

        $this->assertIsArray($merged1);
        $this->assertCount(1, $merged2);
        $this->assertEquals($array1, $merged2);

        $merged3 = array_merge_recursive_distinct($array1, [
            'foo' => 'baz',
            'baz' => 'qux',
        ]);

        $this->assertIsArray($merged3);
        $this->assertCount(2, $merged3);
        $this->assertEquals($merged3['foo'], 'baz');

        $merged4 = array_merge_recursive_distinct(
            [
                'name' => 'foo',
                'colors' => [
                    'red',
                    'blue',
                    'green',
                ],
            ],
            [
                'colors' => [
                    'black',
                    'white',
                ],
            ]
        );

        $this->assertIsArray($merged4);
        $this->assertCount(2, $merged4);
        $this->assertCount(5, $merged4['colors']);

        $merged5 = array_merge_recursive_distinct(
            [
                'name' => 'foo',
                'colors' => [
                    'lightsalmon' => [
                        'hex' => '#FFA07A',
                        'rgb' => 'RGB(255, 160, 122)',
                    ],
                    'mediumvioletred' => [
                        'hex' => '#C71585',
                        'rgb' => 'RGB(199, 21, 133)',
                    ],
                ],
            ],
            [
                'name' => 'bar',
                'colors' => [
                    'lightsalmon' => [
                        'hex' => '#FFA07C',
                        'rgb' => 'RGB(255, 160, 124)',
                    ],
                    'firebrick' => [
                        'hex' => '#B22222',
                        'rgb' => 'RGB(178, 34, 34)',
                    ],
                ],
            ]
        );

        $this->assertIsArray($merged5);
        $this->assertCount(2, $merged5);
        $this->assertEquals('bar', $merged5['name']);
        $this->assertCount(3, $merged5['colors']);
        $this->assertEquals('#FFA07C', $merged5['colors']['lightsalmon']['hex']);
        $this->assertEquals('RGB(255, 160, 124)', $merged5['colors']['lightsalmon']['rgb']);
    }
}
