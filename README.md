# Table of Contents
<!-- TOC -->

- [Table of Contents](#table-of-contents)
- [Introduction](#introduction)
- [Installation](#installation)
- [References](#references)
- [Terms](#terms)
    - [Facts](#facts)
    - [Scaling](#scaling)
    - [Database](#database)
    - [MetaData](#metadata)
    - [Mapping](#mapping)
    - [Mapping](#mapping-1)
    - [Meta Fields](#meta-fields)
    - [Field Data Types](#field-data-types)
- [Meta Fields Usage](#meta-fields-usage)
    - [Category Identities:](#category-identities)
- [Create Mapping](#create-mapping)
- [Import Sample Data](#import-sample-data)
- [Modifying Real Documents](#modifying-real-documents)
    - [Index: Create](#index-create)
    - [Index: Display](#index-display)
    - [Index: Delete](#index-delete)
    - [Document: Replace](#document-replace)
    - [Document: Update](#document-update)
    - [Document: Delete](#document-delete)
- [Batch Processing](#batch-processing)
    - [Batch Insert](#batch-insert)
    - [MUST Insert this if you follow examples](#must-insert-this-if-you-follow-examples)
    - [Batch Multi-Methods](#batch-multi-methods)
    - [Bulk Mutli-Method Alternative](#bulk-mutli-method-alternative)
    - [What is the Source Field?](#what-is-the-source-field)
- [Searching](#searching)
    - [Revelence and Scoring](#revelence-and-scoring)
    - [Elastic Search Calculates a Score](#elastic-search-calculates-a-score)
    - [Query String](#query-string)
    - [Query DSL](#query-dsl)
    - [Types of Queries](#types-of-queries)
        - [Leaf and Compound](#leaf-and-compound)
        - [Full Text](#full-text)
        - [Term](#term)
        - [Joining Queries](#joining-queries)
        - [GeoQueries](#geoqueries)
- [Sample Queries](#sample-queries)
    - [Global Searching](#global-searching)
    - [Boolean Query](#boolean-query)
            - [Prefixing Booleans](#prefixing-booleans)
    - [Search with Query String](#search-with-query-string)
        - [Using an Anaylzer (Why a Hyphen works)](#using-an-anaylzer-why-a-hyphen-works)
- [! Deprecation: analyzer request parameter is deprecated and will be removed in the next major release. Please use the JSON in the request body instead request param*](#-deprecation-analyzer-request-parameter-is-deprecated-and-will-be-removed-in-the-next-major-release-please-use-the-json-in-the-request-body-instead-request-param)
    - [Query DSL](#query-dsl-1)
        - [Multi_Match](#multi_match)
        - [Phrase Match](#phrase-match)
    - [Term Queries](#term-queries)
        - [Single Term](#single-term)
        - [Multiple Terms](#multiple-terms)
        - [Range Terms](#range-terms)
        - [Other Term Level Queries](#other-term-level-queries)
            - [Prefix](#prefix)
            - [Regexp/Wildcard](#regexpwildcard)
            - [Exists](#exists)
            - [Missing](#missing)
- [Compound Queries](#compound-queries)
    - [More Compound Queries](#more-compound-queries)
- [Search Across Index & Mapping Types](#search-across-index--mapping-types)
    - [Create a Dynamic Index/Mapping Record](#create-a-dynamic-indexmapping-record)
    - [Ensure Indices Created](#ensure-indices-created)
    - [Ensure Mappings Created](#ensure-mappings-created)
    - [Search Multi-Index](#search-multi-index)
    - [Search Multi-Mappings](#search-multi-mappings)
    - [Excluding Items](#excluding-items)
    - [Search for All Types](#search-for-all-types)
    - [Search All Indexes, Specified Types](#search-all-indexes-specified-types)
    - [Search All Indexes, All Types](#search-all-indexes-all-types)
- [Fuzzy Searches](#fuzzy-searches)
    - [Auto Fuzziness](#auto-fuzziness)
    - [Performance Details](#performance-details)
- [Proximity Searches](#proximity-searches)
    - [Finding in Name](#finding-in-name)
    - [Finding With Word Gaps](#finding-with-word-gaps)
    - [DSL](#dsl)
- [Boost](#boost)
    - [Boost a Term](#boost-a-term)
    - [Boost a Phrase](#boost-a-phrase)
    - [DSL](#dsl-1)
- [FIlter Results](#filter-results)
    - [DSL](#dsl-2)
- [Size of Results Returned](#size-of-results-returned)
    - [DSL](#dsl-3)
- [Pagination](#pagination)
    - [DSL](#dsl-4)
- [Sorting Results](#sorting-results)
- [Aggregations](#aggregations)
    - [Sum - Metric:Single](#sum---metricsingle)
    - [Average - Metric:Single](#average---metricsingle)
    - [Min/Max - Metric:Single](#minmax---metricsingle)
    - [Stats - Metric:Multi](#stats---metricmulti)
    - [Buckets - Aggregations](#buckets---aggregations)
    - [Buckets - Sub-Aggregations](#buckets---sub-aggregations)

<!-- /TOC -->

# Introduction

This is my documentation from a few weeks of study, with plenty of trial and error. My primary notes are locked away in Atlassian Confluence I which won't be taking out for Company Reasons.

These are notes based off these [References Docs](#references) and this fine course I took to supplement it: [https://www.udemy.com/elasticsearch-complete-guide](https://www.udemy.com/elasticsearch-complete-guide).

# Installation

First, install the git submodule:

```
git submodule init && gitsubmodule update
```

If you are inclined to install the ELK stack and practice or see how it works you need a few things.
- Linux OS (I am using Ubuntu 16.04)
- **Docker** 1.12+/CE
- **Docker Compose**
- **PHP** to run the Example Application
  - I went with the PHP API since it's a common language, it was part of the course, and I have to build it in PHP anyways. I would have tried something else for fun. Perhaps, Ruby as I never use that!

To instatiate, run the `./init-docker.sh`. This will set your Virtual Machine memory properly before instantiating ElasticSearch. _More notes are contained in the `./init.docker.sh` file.



# References

> **Disclaimer**: I have already gone through the following documentation and couldn't remember it as well as I wanted. Integration into someones custom framework has made this incredibly difficult with 4000 lines of MySQL search to replace with Elastic. :\

- [Elasticsearch Reference 5.x](https://www.elastic.co/guide/en/elasticsearch/reference/5.x/index.html)
- [Elasticsearch Clients](https://www.elastic.co/guide/en/elasticsearch/client/index.html)
- [Plugins and Integrations](https://www.elastic.co/guide/en/elasticsearch/plugins/current/index.html) (Only parts of)


# Terms

This is a broken down section of terms to hopefully make this easier.

## Facts

- Elasticsearch is Free
- The #1 Open Search Search Engine in the world.
  - Used by AWS Elastic Search, Github Search,
- Elastic Search automatically builds a REST client
- Elastic Search has API's for nearly every language
- Often tied into ELK stack
  - **E in ELK**: Elasticsearch (The Search Engine)
    - Default Port: **9200**
  - **L in ELK**: Logstash (Collecting Logs)
    - Default Port: **9300** (Can be used for many more things besides Elasticsearch)
  - **K in ELK**: Kibana (GUI to visual/display data from Elasticsearch)
    - Default Port: **5601**

- Elasticsearch is powered by Apache Lucene
- NTR means Near Teal Time, there is a one second delay for Indexing (eg: Creating Database Records)
- **Sense** is now within **Dev Tools > Console** within Kibana for testing queries (Elastic 5.x and above)

## Scaling

- **Cluster**: Collection of Nodes (servers)
  - Cluster should have a Name
- **Nodes(n1)**: Conain as many servers as you want
  - Belongs to Cluster, default Cluster to join; `elasticsearch`
- **Shards**: Indexes can be divided into multiple pieces (For limited hardware)
  - Fully functional and independant index.
  - Horizontal Scaling
- **Replicas**: Copy of a Shard (HA/High Availablity)
  - Replicas live on a separate node (server)
  - Default: Elastisearch adds 5 primary shards and 1 replica per index (Unless configured mannually).

## Database

Everything is stored in Apache Lucene, which powers Eastic Search. Lucene also powers Apache Solr which is the second most popular search engine.

- **Index**: Products, Users; Similar to an SQL Database Name
  - Lowercased names for: CRUD and Search.
  - Unlimited Indexes,
- **Type**: A category of a relevant document beneath an index; Such as SQL Table Name
  - Can have it's own Mapping
  - example:
- **Mapping**: Schema like in MySQL (string, integer, keyword)
- **Document**: Similar to a databose row
  - JSON chunk of key/value field (string, object, nested object, text, etc).
  - Example: user_profiles, items

## MetaData
- Begin with underscores
- Always lowercase
- Examples:
  - `_id`
  - `_source`

## Mapping
- **This is similar to a Database Schema.**
- Defines how Documents (`JSON Data`) and their fields are stored in indexes.
- Similar to defining a VARCHAR, TEXT, INT in SQL.

## Mapping
This in my opinion, is a little tricky as there are several ways to do it, and when injecting data you can have plenty of conflicts.

Mappings are not required as there is **Dynamic Mapping**, however this can cause problems with some search queries. I would encourage the use of mapping, even if it's for some fields.

## Meta Fields

These are always inside each returned result:

- `_id`
- `_type`
- `_uid`
- `_index`
- `_all` is a "catch_all"

- Can I update Mappings later on? Yes
- Can I add extra fields to a Document? No, Schemas (Mappings) must be consistent per Document (Row)
- You cannot update Field Mapping without creating a new Index.

## Field Data Types

These are similar to SQL row types.

- **Core Data Types** (Easiest)
  - `strings`
    - `full-text`: Searching for items by name, strings are converted into individual terms (or keywors) before indexing.
      - **IMPORTANT** NOT used for SORTING or AGGREGATIONS
    - `keywords` Tags, status, NOT used for analyzing
      - **IMPORTANT** Used for SORTING or AGGREGATIONS
    - Integers
    - Date
      - Date Formats
        - YYYY-MM-DD
        - YYYY-MM-DDTHH:MM:SSZ
        - 130000000000 (Epoch)
        - Multiple Dates can use a Boolean
          - YYYY-MM-DD HH:MM:SS || YYYY-MM-DD || Epoch
    - Boolean
       - Valid: false, "false", "off", "no", "0", "", 0.0
    - Binary: Base64 Encoded, Not Searchable
- **Complex Data Types**
  - **IMPORTANT** Elastic search flattens everything before storing data so you can lose associations as values are
    not sorted. A nested might object would list as: `name.nested.age`. To perform this you must map a `Nested Datatype`,
    which makes all the values indexed a single document.
    - *These can cause conflicts* by mis-aligning a flattened datatype.
    - Apache Lucene does not know what inner-objects are with JSON and Arrays.
  - JSON Objects
  - Array
  - Geo Datatype (Lat/long pairs within json), eg:
    - As String: `{"location": "999,999"}`
    - As JSON:   `{"location": {"lat": 999, "long:999"}`
    - Two other variations that i don't feel are necessary
  - GeoShapes
    - LineString, Polgygon, not very interested.
  - Specialized IPv4 (Long Values)
  - Completion; "Prefix Suggester", FST (Finate State Transducer) - you probably don't need to use this.
  - Token
  - Attachment

# Meta Fields Usage

Each document has and: `_index`, `_type`, and `_id`.


## Category Identities:

  - `_index`
    - Matches documents based on index
    - Stored as virtual field
  - `_type`
    - Type of Document
    - Indexed to speed up searching
  - `_id`
    - ID of the document
    - Not indexed, can be directed from `_uid, this is autoassigned unless specified.
  - `_uid`
    - _type and _id are combined as {type}#{id} and indexed

  - `_source`
    - JSON passed to Elasticsearch
    - Not Indexed, but returned in search results
    - Can be disabled
  - `_size`
    - Indexes the _source in bytes
    - Must install `bin/plugin install mapper-size` (From the elastic search plugins path)

  - `_all`
    - Concatted data with space as a delimeter
    - Can be searched but not retrieved.

  - `_field_names`
    - Indexes names of every field in document that are not `null`.
    - Used my `exists` and `missing` to check fields.

  - `_routing`
    - Routes a document to a shard to an index
    - You can define rules, though it's used for advanced use cases.

    - `_parent`
      - This is like having a MySQL Foreign Key in the Schema, this is a bit complicated for what Im whipping up.

    - `_meta`
      - Store app specific metadata (Anything)
      - Each mapping type can have metadata (Elastic does nothing with it beside store/retrieve).


# Create Mapping

A
```
PUT /anyname
{
  "mappings": {
    "products": {
      "properties": {
        "title": {
          "type": "string",
        },
        "price": {
          "type": "double",
        },
        "quantity": {
          "type": "integer",
        },
        "description": {
          "type": "text"
        }
        "categories": {
          "type": "nested",   <--- This isn't neessary, but for many fields in a nested item sure.
          "properties": {
            "title": {        <--- Only using one item
              "type": "string"
            }
          }
        },
        "tags": {
          "type": "string"  <--- There is no Array Type.
        }
      }
    }
  }
}
```



You can test the above in Kibana DevTools (recommended), and you should received `{"acknowledged": true}`.

# Import Sample Data

Im import sample JSON data use the test file, and import with `X PUT`

```
curl -XPOST http://localhost:9200/ecommerce/product/_bulk --data-binary @test-data.json;
```

# Modifying Real Documents

I'll be doing all these methods within the `Kibana` > `Dev Tools` (5.3).
These can be done with cURL or an API, but for brevity and readiblity I'll keep it short.

This assumes you imported the sample data

> Everything uses an HTTP request, that means HTTP verbs. If you are unfamiliar they are:

| Verbs | Action | |
| ---   |  ---   | --- |
| POST  | Create | |
| GET   | Read   | |
| PUT   | Update/Replace | |
| PATCH | Update/Modify | *This will NOT replace as PUT does, only modify* |
| DELETE | Delete | |

## Index: Create
```
curl -XPUT http://localhost:9200/ecommerce
```

You should have a result of: `{ "acknowledged": true} `

## Index: Display

The `?pretty` makes the JSON more readable.

```
curl -XGET http://localhost:9200/_cat/indices?pretty
```

## Index: Delete
```
curl -XDELETE https://localhost:9200/myindex
```

You should have a result of: `{ "acknowledged": true} `
- Also check the `_cat` API from [Display Indexes](#display-indexes) above.


## Document: Replace

> Use `PUT`

```
PUT ecommerce/product/1001
{
  "name": "Zend 2",
  "price": 40.00,
  "quantity": 1,
  "categories": [
    { "name": "Software" }
  ],
  "tags": [ "zend framework", "php", "zf2", "zf", "programming" ]

}
```

## Document: Update

> USE `POST` and `_update`

```
POST ecommerce/product/1001/_update
{
  "doc": {
    "price" 333.11
  }
}
```

## Document: Delete

> Use `DELETE`
> Note: You can only delete documents by ID unless you were to install a "delete by query" plugin.

```
DELETE /ecommerce/product/1001
```


# Batch Processing
Batch will do 1 network process rather than one import per call, thus saving tons of HTTP calls.

- Uses `_bulk` API
- Created with `\n` Newline character, JSON will not look pretty.
- The last newline must end with `\n`

## Batch Insert

**Do this if you decide to test some samples below**

> It's not pretty JSON for bulk

- Insert all the meta data in the first JSON row
- Insert the data in the second row
- .. Repeat ..

## MUST Insert this if you follow examples
```
POST /ecommerce/product/_bulk
{"index":{"_id":"1002"}}
{"name":"monkey - fluffy","price":20.21,"status":"active", "description":"Indeed, this precious little critter is healthy and in good shape."}
{"index":{"_id":"1003"}}
{"name":"monkey - bald","price":22.21,"status":"disabled", "description":"This primate has gotten old and lacks hair, he is quite an odd fellow."}
{"index":{"_id":"1004"}}
{"name":"monkey - momma","price":31.11,"status":"active", "description":"The mother of all monkeys."}
```

## Batch Multi-Methods

- The Below looks tricky, but Delete has no Document Data.
- Update DOES have document data, thats why below it we provide the `doc` info to update.

```
POST /ecommerce/product/_bulk
{"delete": {"_id": "1" }}
{"update": {"_id": "1002" }}
{"doc": {"quantity": "22" }}
```

**Ensure:**

```
GET /ecommerce/product/1
```
> Expect: Founds should be false

```
GET /ecommerce/product/1003
```
> Expect: Qty is 22

## Bulk Mutli-Method Alternative

```
POST _bulk
{"update": {"_id": "1002", "_index": "ecommerce", "_type": "products"}}
{"doc": {"quantity": 10}}
```

**Ensure:**

```
GET ecommerce/product/1002
# Expect: Qty is 10
```

## What is the Source Field?

`_source` is the JSON document we added

# Searching

This can get very complex, so the outline this is an outline:

## Revelence and Scoring

- Methods of Search:
  - **Query String** (eg: CURL/HTTP)
  - **Query DSL** (eg: API calls)
- Query Types
  - Full Text
  - Terms
  - **Leaf**
    - Look for a particular value in a particular field, such as the `match`, `term` or `range` queries. These queries can be used by themselves.
  - **Compound**
      - Wrap other compound or Leaf queries, either to combine their results and scores, to change their behaviour, or to switch from query to filter context.
      - In laymans terms, It's like doing a few Leaf queries in one, for example you could search a `boolean` that has `must_match` with two `term` nested in it.


## Elastic Search Calculates a Score

- Ranks Documents per query
- Score is calculated for each document matching query
- Higher Score = more relevant
- **Query Context**: DO affect Score of Matching Docs
- **Filter Context**: Do Not affect scores of Matching Docs.

## Query String

- Send Parameters via REST, URI.
- Simple Ad-Hoc Qeruies
- Supports Advanced Queries with `-d` flag
- `GET http://localhost/ecomerce/product/_search?q=monkey`


## Query DSL

- Define queries in JSON Request Body
- More features than Strings
- More Advanced Queries
- Easier to Read

A very simple example

```
GET http://localhost:9200/ecommerce/products/_search
{
  "query": {
    "match": {
      "name": "monkey - fluffy"
    }
  }
}
```

## Types of Queries

This can get complicated to understand among all the other parts
of the systems. There are two types of queries: `Leaf` and `Compound`.

### Leaf and Compound

This gets a bit complicated.

- Leaf
  - Look for particular in particular fields, eg: `monkey` in name
  - Can be used solo in a query without being part of a compound query.
  - Can also be used compound queries to construct advanced queries.
- Compound
  - Wrap leaf clauses or other compound query clauses
  - Combine multiple queries in logical funashion (eg: boolean and/or)
  - Alter Behavior of Queries.

### Full Text

- Runs full queries on full text fields
  - product name/description, etc
- Values anaylzed when adding documents/modifying values
  - removing stop words, tokenizing, lowercasing (?)
- Apply each fields analyzer to query string before executing (Now Im lost)


### Term

- Exact value match
- Structure like numbers/date, not full text
- Not analyzed before running


### Joining Queries

- **Joins in a system is expensive**
- **Nested query** (Type 1)
  - Documents may contain fields of type **nexted** with array of objects
  - Each object can be queried w/nested query as independent Doc.
- **has_child**/**has_parent** (Type 2):
  - Parent/Child relationship can exist between two document types w/Single Index.
  - `has_child` returns parent document whose child Doc matches the query
  - `has_parent` returns child document whose parent Doc matches the query.


### GeoQueries

Yeah, Yeah..

- geo_point (lat/lon pair)
- geoshapoe (pt, ln, cir, poly, etc)


# Sample Queries

To search via query use `_search` and `?q=`

Example:
```
GET /ecommerce/product/search?q=<search_string>
```

Match All
```
GET /ecommerce/product/search?q=*
```

**Hits Key:**

- `total` (# of matches results)
- `max_score` (Highest score of matched documents)
- `hits` (each resulting Document)
  - `_score` (How well the search matched the query)
  - `_source` (The JSON Data we added)


## Global Searching

Match Word in Any Field
```
GET /ecommerce/product/_search?q=monkey
```

Match Word in Specific Field
```
GET /ecommerce/product/_search?q=name:monkey
```

## Boolean Query

**Looks for name field with name that has `money` **AND** `fluffy`.**
```
GET /ecommerce/product/_search?q=name:(monkey AND fluffy)
```
> I get 1 result (If you followed from the top)

**Looks for name field with name that has `money` **OR** `fluffy`.**
```
GET /ecommerce/product/_search?q=name:(monkey OR fluffy)
```
> I get 2 results (If you followed from the top)

**Even more complex**
```
GET /ecommerce/product/_search?q=name:(monkey OR fluffy) AND status:active
```
> I get 1 results (If you followed from the top)

#### Prefixing Booleans

You can do the following as a **shorthand boolean method**:

- The `+` means a field value is required
- The `-` means a field value must not be present

```
GET /ecommerce/product/_search?q=name:+monkey -fluffy
```

## Search with Query String

> We can search for specific pharases by using quotes. `"` ... `"`

- Default: All terms are optional as long as one term matches.
- Default: Boolean operator is `_all`

Although there is a hyphen `-` in the name, it will disregard it and find it (`Standard Analyzer`).
```
GET /ecommerce/product/_search?q=name:"monkey fluffy"
```
> Ensure: I get 1 results.


**Switching the Order of the Terms will not work:**
```
GET /ecommerce/product/_search?q=name:"fluffy monkey"
```
> Ensure: I get 0 results.


### Using an Anaylzer (Why a Hyphen works)
Note: *#! Deprecation: text request parameter is deprecated and will be removed in the next major release. Please use the JSON in the request body instead request param
#! Deprecation: analyzer request parameter is deprecated and will be removed in the next major release. Please use the JSON in the request body instead request param*

```
GET /_analyze?analyzer=standard&text=fluffy - monkey
```


## Query DSL

Rather than URI queries, this is sending queries Request Body's in the JSON, basically a payload.

- Special Characters must be escaped with a backslash (`\`). Characeters are: `?` `+` `=` and so on, you should be familiar.

The Simplest example to match all, no *Body is present*.
```
GET /ecommerce/product/_search
{
  "query": {
    "match_all": {}
  }
}
```

A Simple match, just add in the `field_name: value`.
```
GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": "monkey"
    }
  }
}
```


### Multi_Match

Allows you to run a query against many fields

```
GET /ecommerce/product/_search
{
  "query": {
    "multi_match": {
      "query": "monkey",
      "fields": [ "name", "description" ]
    }
  }
}
```

### Phrase Match

Remember: The order of terms MATTER (switching `monkey bald` to `bald money` fails)

```
GET /ecommerce/product/_search
{
  "query": {
    "match_phrase": {
      "name": "monkey bald"
    }
  }
}
```


## Term Queries

Searches fields for **exact** values.

- Doc with value with: "!" will not work, the default analyzer will not be found, nor an exact match.
- String fields are analyzed by default
- Not Analyzed before Queries

To avoid these problems we do not Analyze field values with `not_analyzed`.

### Single Term

> Use `term` (Non-Plural)

```
GET /ecommerce/product/_search
{
  "query": {
    "term": {
      "status": "active"
    }
  }
}
```

### Multiple Terms

> Use `terms` (Plural)

```
GET /ecommerce/product/_search
{
  "query": {
    "terms": {
      "status": ["active", "incative"]
    }
  }
}
```

### Range Terms

> Use `terms` (Plural)

```
GET /ecommerce/product/_search
{
  "query": {
    "range": {
      "quantity": {
        "gte": 1,
        "lte": 10
      }
    }
  }
}
```

### Other Term Level Queries

#### Prefix
Search for fields that starts with  a prefix, eg `mon` (in my case, short for monkey)
```
GET /ecommerce/product/_search
{
  "query": {
    "prefix" : { "name" : "mon" }
  }
}
```

#### Regexp/Wildcard
These can be slow, use with caution.

There are more advanced flags at [Regexp Queries](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html)

**The `?` matches any next character**
```
GET /ecommerce/product/_search
{
    "query": {
        "regexp":{
            "name": "monk.?y"
        }
    }
}

```
> Expect: Received 2 results


**The `*` matches any character more than once. **

(Cannot use at the beginning of search)
```
GET /ecommerce/product/_search
{
    "query": {
        "regexp":{
            "name": "monk.*"
        }
    }
}

```
> Expect: Received 2 results

#### Exists

```
GET /ecommerce/product/_search
{
    "query": {
        "exists" : {
          "field" : "user"
        }
    }
}

```
> Expect: 0 Results

#### Missing
Depracated for `expected` in 5.3, example using Exists in this fashion:

```
GET /ecommerce/product/_search
{
    "query": {
        "bool": {
            "must_not": {
                "exists": {
                    "field": "user"
                }
            }
        }
    }
}
```

or use `must` rather than `must_not`

```
GET /ecommerce/product/_search
{
    "query": {
        "bool": {
            "must": {
                "exists": {
                    "field": "user"
                }
            }
        }
    }
}
```

# Compound Queries

> Compound queries wrap other compound or leaf queries, either to combine their results and scores, to change their behaviour, or to switch from query to filter context.

**Conditions in the `must` clause must all be `true`.**

```
GET /ecommerce/product/_search
{
  "query": {
    "bool": {
      "must": [
        {"match": {"name": "monkey"} },
        {"match": {"name": "bald"} }
      ]
    }
  }
}

```
> Expect: 1 Result

**Conditions in the `must_not` clause must all be `false`.**

```
GET /ecommerce/product/_search
{
  "query": {
    "bool": {
      "must_not": [
        {"match": {"name": "monkey"} },
        {"match": {"name": "bald"} }
      ]
    }
  }
}
```
> Expect: 5 Results

**This must have a monkey, but must not have bald in the `name` field**.
```
GET /ecommerce/product/_search
{
  "query": {
    "bool": {
      "must": [
        {"match": {"name": "monkey"} }
      ],
      "must_not": [
        {"match": {"name": "bald"} }
      ]
    }
  }
}
```
> Expect: 1 Results

**Should will increase the relevent value if it's more relevant** (Behaves like a logical OR).

- `should` will `boost` results below and not exclude them by like `must_not` does.
Should is optional, but when used documents that match better will rank it higher. For example,
- With out three monkey records: `money fluffy`, `monkey bald` and `monkey momma`, the order is:
  - `monkey - bald`
  - `monkey - mama`
  - `monkey - fluffly`

```
GET /ecommerce/product/_search
{
  "query": {
    "bool": {
      "must": [
        {"match": {"name": "monkey"} }
      ],
      "should": [
        {"match": {"name": "bald"} }
      ]
    }
  }
}
```
> Expect: 1 Results

## More Compound Queries

- We can use `_score` to boost popularity or better search relevance.
- `_boosting` can reduce the score.

For even more, see [Compound Queries](https://www.elastic.co/guide/en/elasticsearch/reference/current/compound-queries.html)

# Search Across Index & Mapping Types

Alright, toss this in with a little nested ingredients.

Behind the scenes, we did not create a mapping or index in advanced, this was created via Dynamic datatypes are inferring by Dynamic mapping.

## Create a Dynamic Index/Mapping Record
```
PUT /myfoodblog/recipe/1
{
  "name": "Pasta Quattro Formaggi",
  "description": "First boil pasta, toss it around, whip some cheese on that mother",
  "ingredients": [{
    "name": "Pasta",
    "amount": "500g"
  }, {
    "name": "Fontina Cheese",
    "amount": "100g"
  }, {
    "name": "Parmesan Cheese",
    "amount": "100g"
  }, {
    "name": "Romano Cheese",
    "amount": "100g"
  }, {
    "name": "Gorgonzalo Cheese",
    "amount": "100g"
  }]
}
```

## Ensure Indices Created
```
GET /_cat/indices?v
```
> Expect: myfoodblog with docs.count of `1`

## Ensure Mappings Created
```
GET /myfoodblog
```
> Expect: A list of mappings (Probably Strings and Keywords for 5.3)

## Search Multi-Index

Notice the Indice `ecommerce,myfoodblog`
```
GET /ecommerce,myfoodblog/product/_search?q=pasta
```
> Expect: 10 Hits, only from ecommerce though.

## Search Multi-Mappings

The Default return size is 10, so set `size=15`. Notices the `product,recipe`.
```
GET /ecommerce,myfoodblog/product,recipe/_search?q=pasta&size=15
```
> Expect: 11 Hits, ecommerce and myfoodblog (myfoodblog at the bottom)

## Excluding Items

Similar to examples far above we can use the `+` and `-` symbols.

Below, the `+` symbol is interpreted as a `space`, so it's url encoded as `%2B`.

> @TODO: This fails in 5.3

```
GET /-ecommerce,%2Bmyfoodblog/product,recipe/_search?q=paste
```

## Search for All Types

Any type will match within the ecommerce index
```
GET /ecommerce/_search?q=monkey
```

## Search All Indexes, Specified Types
```
GET /_all/product/_search?q=monkey
```

## Search All Indexes, All Types
```
GET /_all/_search?q=monkey
```

# Fuzzy Searches
Fuzziness is the # of characters allowed to be different. Append a tidle with an integer, eg: `~3`, the default is `2`.

- Terms must be in the correct order
- Allows you to specify distance for CHARACTERS
- Similar to Proximity search (Yet Proximity deals with WORDS)

**A fuzziness greater than `2` is too expensive for Apache Lucene.**

**Using REST**
```
GET /ecommerce/product/_search?q=past~1

```
> Expect: Results with "pasta" or "paste"

**Using Query DSL**

Indentical as the above REST query

```
GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": {
        "query": "past",
        "fuzziness": 1
      }
    }
  }
}
```


## Auto Fuzziness

Below, Elastic will determine the most appropriate edit distance based on the query.

The Auto Fuzziness:
- Query Length of `0-2`: exact match
- Query Length of `3-5`: 1 edit distance
- Query Length of `5+`: 2 fuzziness

```
GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": {
        "query": "past",
        "fuzziness": "AUTO"
      }
    }
  }
}
```

## Performance Details

Lucense has a fast Fuzzy query, but unique terms in an index makes it slower.
DFA searches are naturally slower than a binary search and process a larger number of terms.

A match query with Lucene is much faster doing a binary lookup for internally stored items.

- Fuzzy queries compare to terms in an index.
- **The analyzed terms are searched, not the visible documents.**
- **This can lead to confusing results.**


# Proximity Searches

- Terms can be in the different orders, and further apart
- Must use Quotes `"`
- A Phrase Search
- Allows you to specify distance for WORDS
- Similar to Fuzzy search (Yet Fuzzy deals with CHARACTERS)
- The `~2, ~3`  etc value is the amount of Edits made to find a word.
  - If a phrase has terms in the wrong order, such as two terms, it
    would require `2` editors to move the first time to the front and
    the second to the back to find a match.
  - The above can make it tricky to figure out why a phrase might not match.

## Finding in Name
This value of this `name` is `Pasta - Spaghetti` which contains "Spaghetti - Pasta"
```sh
; Works
GET /ecommerce/product/_search?q=name:"pasta spaghetti"~2

; Works
GET /ecommerce/product/_search?q=name:"spaghetti pasta "~2
```

## Finding With Word Gaps

An example record has part of the following in it's description `(test-data.json) - Last Record.`:
```
"If the moon was made of cheese and steak would you eat it?"
```

The word `and` is between `cheese` and `steak`, we can find it with `~1`:

```
GET /ecommerce/product/_search?q=description:"cheese steak"~1
```

> A high value for proximity such as `~30` can find words with that
phrase, it will give the phrases that are closer together a higher
ranking in the search results. (`_score`)

## DSL
To accomplish the same thing, we do the following, and `slop` is equivalent to `~2`.
```
GET /ecommerce/product/_search
{
  "query": {
    "match_phrase": {
      "name": {
        "query": "pasta spaghetti",
        "slop": 2
      }
    }
  }
}
```

# Boost

You can boost terms and query clauses and assign them high/low priorities.

- Default Boost Value: `1.0`
- Use the `^` operator to boost in the REST API.

## Boost a Term
```
GET /ecommerce/product/_search?q=name:pasta spaghetti^2.0
```

## Boost a Phrase
Note the Quotes `"`, a phrase.
```
GET /ecommerce/product/_search?q=name:"pasta spaghetti"^2.0
```

## DSL
Boolean Query. The boost value is not linaer, `2.0` is not twice the
value of `1.0`, but the higher the score the more relevent it will be.

Due to this `boost`, the term `spaghetti` is more important than `noodle`.
```
GET /ecommerce/product/_search
{
  "query": {
    "bool": {
      "must": [
        { "match": { "name": "pasta" } }
      ],
      "should": [
        {
          "match": {
            "name": {
              "query": "spaghetti",
              "boost": 2.0
            }
          }
        },
        {
          "match": {
            "name": {
              "query": "noodle",
              "boost": 1.5
            }
          }
        }
      ]
    }
  }
}
```

# FIlter Results

There are two Contexts:
- **1: Query Context;** Affects the relevence of a query depending on the match.
- **2: Filter Context;** Does not affect relevent scores. Can be used to exclude fields from the results. Since fields can be excluded, relevence doesn't make sense and ES handles this automatically.

## DSL
```
GET /ecommerce/product/_search
{
  "query": {
    "bool": {
      "must": [
        { "match": { "name": "pasta" }}
      ],
      "filter": [
        {
          "range": {
            "quantity": {
              "gte": 10,
              "lte": 15
            }
          }
        }
      ]
    }
  }
}
```

# Size of Results Returned
- The default value is `10`
- This uses the `size` parameter.

```
GET /ecommerce/product/_search?q=name:pasta&size=2
```
> Expect: hits.total 10 or 11, but only show two results.

## DSL
```
GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": "pasta"
    }
  },
  "size": 2
}
```

# Pagination

- Default starts from `0` like in programming.
- Uses `from` and `size`

```
 Page 1: Since the default is 0, you don't have to specify it for
GET /ecommerce/product/_search?q=name:pasta&size=5

; Page 2:
GET /ecommerce/product/_search?q=name:pasta&size=5&from=5

; Page 3:
GET /ecommerce/product/_search?q=name:pasta&size=5&from=10
```

## DSL
```
; Page 1: Since the default is 0, you don't have to specify it for Page 1
GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": "pasta"
    }
  },
  "size": 5
}

; Page 2
GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": "pasta"
    }
  },
  "size": 5,
  "from": 5
}
```

# Sorting Results

- Sort Property contains array of objects
- For multiple sort parameters, results are sorted by the first items in the sort query, then the next.

```
GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": "pasta"
    }
  },
  "sort": [
    {
      "quantity": {
        "order": "desc"
      }
    }
  ]
}
```

# Aggregations

- Grouping/Extracting Statistics
- eg: MySQL `GROUP BY`, `SUM`.

- **Types**:
  - **Metric**: Values extracted from Documents
    - **Single-value** numeric metric agg (sum, avg, min, max)
    - **Multi-value** numeric metric agg (stats)
  - **Bucket**: Create sets of documents (buckets are associated w/criteria containing sets of documents)
    - Allows Sub-Aggregations (Not possible with Metric)
  - **Pipeline**: Experimental/Advanced

## Sum - Metric:Single
```
; All Products -- The keyname is whatever we call it nested in 'aggs->name\_here(qty_sum)'

GET /ecommerce/product/_search
{
  "query": {
    "match_all": { }
  },
  "size": 0,
  "aggs": {
    "qty_sum": {
      "sum": {
        "field": "quantity"
      }
    }
  }
}


; By a Query -- The keyname is whatever we call it nested in 'aggs->name\_here(sum)'

GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": {
        "query": "pasta"
      }
    }
  },
  "size": 0,
  "aggs": {
    "qty_sum": {
      "sum": {
        "field": "quantity"
      }
    }
  }
}
```

## Average - Metric:Single
```
; By a Query -- The keyname is whatever we call it nested in 'aggs->name\_here(qty_avg)'

GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": {
        "query": "pasta"
      }
    }
  },
  "size": 0,
  "aggs": {
    "qty_avg": {
      "avg": {
        "field": "quantity"
      }
    }
  }
}
```

## Min/Max - Metric:Single
```
; Min -- The keyname is whatever we call it nested in 'aggs->name\_here(qty_min)'

GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": {
        "query": "pasta"
      }
    }
  },
  "size": 0,
  "aggs": {
    "qty_min": {
      "min": {
        "field": "quantity"
      }
    }
  }
}


; Max -- The keyname is whatever we call it nested in 'aggs->name\_here(qty_max)'

GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": {
        "query": "pasta"
      }
    }
  },
  "size": 0,
  "aggs": {
    "qty_max": {
      "max": {
        "field": "quantity"
      }
    }
  }
}
```

## Stats - Metric:Multi
```
GET /ecommerce/product/_search
{
  "query": {
    "match": {
      "name": {
        "query": "pasta"
      }
    }
  },
  "size": 0,
  "aggs": {
    "qty_stats": {
      "stats": {
        "field": "quantity"
      }
    }
  }
}
```

## Buckets - Aggregations
Buckets return groups of documents which meet their criteria.
They can also run sub-aggregations to apply to all buckets there-in.
This is an extremely powerful tool.

> Use Case: Find Product Quantity in a price range.

There are no hard-limits on how deep and how many times you can nest `aggs`.

```
GET /ecommerce/product/_search
{
  "query": {
    "match_all": { }
  },
  "size": 0,
  "aggs": {
    "qty_ranges": {
      "range": {
        "field": "quantity",
        "ranges": [
          {
              "from": 1,
              "to": 50
          },
          {
            "from": 50,
            "to": 100
          }
        ]
      }
    }
  }
}
```

**Example output from above**
```
  "aggregations": {
    "qty_ranges": {
      "buckets": [
        {
          "key": "1.0-50.0",
          "from": 1,
          "to": 50,
          "doc_count": 481
        },
        {
          "key": "50.0-100.0",
          "from": 50,
          "to": 100,
          "doc_count": 508
        }
      ]
    }
  }
```

## Buckets - Sub-Aggregations
Will use a Metric Sub-Aggregation with a Bucket.
```
GET /ecommerce/product/_search
{
  "query": {
    "match_all": { }
  },
  "size": 0,
  "aggs": {
    "quantity_ranges": {
      "range": {
        "field": "quantity",
        "ranges": [
          {
              "from": 1,
              "to": 50
          },
          {
            "from": 50,
            "to": 100
          }
        ]
      },
      "aggs":     <---- This Sub-Aggregation is run on every bucket
        "quantity_stats": {
          "stats": {
            "field": "quantity"
          }
        }
      }
    }
  }
}
```

**Example output from above**
```
  "aggregations": {
    "qty_ranges": {
      "buckets": [
        {
          "key": "1.0-50.0",
          "from": 1,
          "to": 50,
          "doc_count": 481,
          "qty_stats": {  <-- Our nested Agg Produced these
            "count": 481,
            "min": 1,
            "max": 49,
            "avg": 25.405405405405407,
            "sum": 12220
          }
        },
        {
          "key": "50.0-100.0",
          "from": 50,
          "to": 100,
          "doc_count": 508,
          "qty_stats": {  <-- Our nested Agg Produced these
            "count": 508,
            "min": 50,
            "max": 99,
            "avg": 74.57874015748031,
            "sum": 37886
          }
        }
      ]
    }
```


