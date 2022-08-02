<?php

declare(strict_types=1);

namespace Supseven\SolrPaginationLink\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * SolrPaginationLinkViewHelper takes all get parameters and creates a sanitized link used for Solr pagination.
 *
 * @example
 * {p:solrPaginationLink(page: '{page}')}
 * ... where 'page' is the current pagination number (not the pageUid!).
 *
 * @author Helmut Strasser <h.strasser@supseven.at>
 */
class SolrPaginationLinkViewHelper extends AbstractViewHelper
{
    /**
     * Initialize provided arguments
     */
    public function initializeArguments(): void
    {
        // The page param changes with every single link created for the pagination...
        $this->registerArgument('page', 'integer', 'The pagination page number (1,2,3...) the link should point to.', true);
        // ... therefore we don't need to provide it from the get params
        $this->registerArgument('allowedParams', 'string', 'Params the view helper should allowed to pass through.', false, 'q,filter');
    }

    /**
     * Create and sanitize an url used for solr pagination.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     *
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        // Access all get params
        $allQueryParams = GeneralUtility::_GET();

        $uriBuilder = $renderingContext->getUriBuilder();
        $uriBuilder->reset();

        $allowedParams = GeneralUtility::trimExplode(',', $arguments['allowedParams']);

        // Iterate over all get params and create new sanitized params
        foreach ($allQueryParams as $queryKey => $params) {
            // We only accept params prefixed with 'tx_solr'
            if ($queryKey === 'tx_solr') {
                foreach ($params as $key => $value) {
                    // We only accept params mentioned in $allowedParams
                    // Here the page param is excluded, because it has to be taken from $arguments
                    if (in_array($key, $allowedParams, true)) {
                        $queryValue = null;
                        // The query param might be an array, e.g. if facets/filters are used
                        if (is_array($value)) {
                            foreach ($value as $v) {
                                // Sanitize the value
                                $queryValue[] = self::sanitizeUrlString($v);
                            }
                        } else {
                            // Sanitize the value
                            $queryValue = self::sanitizeUrlString($value);
                        }
                        // Rebuild the argument and hand it over to the uriBuilder
                        $args[$queryKey][$key] = $queryValue;
                        $uriBuilder->setArguments($args);
                    }
                }
            }
        }

        // The param 'page' has to be taken from $arguments, not the get params, because the page param
        // is the one that changes for every single link in the pagination
        $page = (int)$arguments['page'];
        $newPage = $page ? : 1;
        return $uriBuilder->uriFor('results', ['page' => $newPage], 'Search');
    }

    /**
     * Sanitize the given parameter from url.
     * Here we can not use urlencode(), because Umlauts and colons must not be encoded for Solr.
     * Also, to keep the search string in working order we must allow spaces and plus sign.
     *
     * @param string $string
     *
     * @return string
     */
    private static function sanitizeUrlString(string $string): string
    {
        return preg_replace('/[^-a-z0-9_\\+äöü: ]/ui', '', $string);
    }
}
