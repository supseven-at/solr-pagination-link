# Solr Pagination Link

This extension sanitizes links created for the solr pagination template.

## Installation

1. Add the path to the "repositories" section within the project's composer.json
```php
    "repositories": [
        { "type": "vcs", "url": "https://github.com/supseven-at/solr-pagination-link.git" }
    ]
```
2. Add the package to composer: ```composer req supseven/solr-pagination-link```

## Integration
1. Within your pagination template add the namespace of the viewhelper of this extension:
```html
<html data-namespace-typo3-fluid="true"
      xmlns:s="http://typo3.org/ns/Supseven/SolrPaginationLink/ViewHelpers">
```
2. In the pagination template exchange all links with the following viewhelper:
```html
{s:solrPaginationLink(page: '{page}')}
```
or
```html
<s:solrPaginationLink page="{page}"/>
```
where ```{page}``` is the current pagination link.

## License
[GPL 3.0 or later](LICENSE)