<?php

declare(strict_types=1);

namespace Supseven\SolrPaginationLink\Tests\ViewHelpers;

use PHPUnit\Framework\TestCase;
use Supseven\SolrPaginationLink\ViewHelpers\SolrPaginationLinkViewHelper;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

class SolrPaginationLinkViewHelperTest extends TestCase
{
    public function testRender(): void
    {
        $testArray = [
            'page'   => 'foobar',
            'q'      => 'search term',
            'filter' => [
                0 => 'type:News',
            ],
        ];
        $expected  = '?tx_solr[page]=5&tx_solr[q]=search+term&tx_solr[filter][0]=type:News';

        $arguments = [
            'page'          => 5,
            'allowedParams' => 'q, filter',
            'extPrefix'     => 'tx_solr',
        ];
        $request   = $this->createMock(Request::class);
        $request->expects(static::once())->method('getArguments')->willReturn($testArray);
        $renderingContext = $this->createMock(RenderingContext::class);
        $renderingContext->expects(static::once())->method('getRequest')->willReturn($request);
        $uriBuilder = $this->createMock(UriBuilder::class);
        $renderingContext->expects(static::once())->method('getUriBuilder')->willReturn($uriBuilder);
        $uriBuilder->expects(static::once())->method('reset');

        $uriBuilder->expects(static::once())->method('uriFor')->with(
            static::equalTo('results'),
            static::equalTo(['page' => $arguments['page']]),
            static::equalTo('Search'),
        )->willReturn($expected);

        $renderingClosure = static fn() => null;

        $actual = SolrPaginationLinkViewHelper::renderStatic($arguments, $renderingClosure, $renderingContext);

        static::assertSame($expected, $actual);
    }

    /**
     * @dataProvider stringProvider
     */
    public function testSanitizeUrlStringIfUrlIsSanitized(string $test, string $expected): void
    {
        $this->assertSame(SolrPaginationLinkViewHelper::sanitizeUrlString($test), $expected);
    }

    public function stringProvider(): array
    {
        return [
            ['test', 'test'],
            [
                'a+b c:d1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZäöüÄÖÜ!"§$%&/()=-?<>{};.,',
                'a+b c:d1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZäöüÄÖÜ-',
            ],
        ];
    }
}
