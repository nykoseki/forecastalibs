<?php
/**
 * Created by PhpStorm.
 * User: nykos
 * Date: 2019/12/21
 * Time: 1:53
 */
namespace ForecastaTest\Loader\Xml;

use PHPUnit\Framework\TestCase;

use Forecasta\Loader\Xml\XMLLoader;
use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class XMLLoaderTestCase extends TestCase
{
    private$loader = null;

    public function setUp(): void {
        $this->loader = new XMLLoader;
    }


    public function testLoad() {
        $xmlpath = __DIR__. '/testLoad.xml';

        $this->loader->init($xmlpath);

        $this->assertTrue(true);
    }
}
