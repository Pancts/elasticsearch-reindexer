# elasticsearch-reindexer
A quick console tool for reindexing your elasticsearch data. It's made using only *symfony/console* and official elasticsearch php client.

## setup
### basic way
```
# git clone https://github.com/martiis/elasticsearch-reindexer.git
# cd elasticsearch-reindexer
# composer install
# bin/reindex <scan_index> <index_index>
```

### composer way
```
# composer require martiis/elasticsearch-reindexer
# vendor/bin/reindex <scan_index> <index_index>
```

## usage
Reindex command has few more arguments and options to play with:

| Type     | Name  | Meaning             |
|----------|-------|---------------------|
| Argument | scan  | Index name to scan  |
| Argument | index | Index name to index |
| Option   | host  | Elasticsearch host  |
| Option   | port  | Elasticsearch port  |
| Option   | bulk  | Indexing bulk size  |
