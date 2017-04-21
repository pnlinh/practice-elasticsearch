# Elasticsearch Notes

This is documentation from a few weeks of study, trial and error. My primary notes are locked in Atlassian Confluence which won't be taking out (Company Reasons).  Therefore, these are notes based off this fine course at [https://www.udemy.com/elasticsearch-complete-guide](https://www.udemy.com/elasticsearch-complete-guide). 

Disclaimer: I have already gone through the following yet couldn't remember it swiftly as I liked, having to integrate this in some guys custom framework has made this incredibly difficult. :\

- [Elasticsearch Reference 5.x](https://www.elastic.co/guide/en/elasticsearch/reference/5.x/index.html)
- [Elasticsearch Clients](https://www.elastic.co/guide/en/elasticsearch/client/index.html)
- [Plugins and Integrations](https://www.elastic.co/guide/en/elasticsearch/plugins/current/index.html) (Only parts of)
- Another that I cannot find. The point is, there is so much to read it's easy to miss a small portion that I needed some extra assistance. This is a very large and powerful system. Many notes are reptitive of what I've written down, yet It will help me memorize it to write it down again.

[TOC]

# Terms

This is a broken down section of terms to hopefully make this easier.

## Facts:
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
Everything in stored in Apache Lucene, which powers elastic search.

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
  - 

## Mapping
- Defines how Documents and their fields are stored in indexes.
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

```
POST /ecommerce/product/_bulk
{"index":{"_id":"1002"}}
{"name":"monkey - fluffy","price":20.21,"status":"active", "description":"Indeed, this precious little critter is healthy and in good shape."}
{"index":{"_id":"1003"}}
{"name":"monkey - bald","price":22.21,"status":"disabled", "description":"This primate has gotten old and lacks hair, he is quite an odd fellow."}
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
# Expected: Founds should be false
```

```
GET /ecommerce/product/1003
# Expected: Qty is 22
```

## Bulk Mutli-Method Alternative

```
POST _bulk
{"update": {"_id": "1002", "_index": "ecommerce", "_type": "products"}}
{"doc": {"quantity": 10}}
```

**Ensure:**

```
GET ecommerce/product/1002
# Expected: Qty is 10
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
  - Leaf & Compount
  - Compount
  
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
 
 ### Leaf and Comfound
 
 This gets a bit complicated.
 
 - Leaf
   - Look for particular in particular fields, eg: `monkey` in name
   - Can be used solo in a query without being part of a compound query.
   - Can also be used compound queries to construct advanced queries.
 - Comfound
   - Wrap leaf clauses or other compound query clauses
   - Combine multiple queries in logical funashion (eg: boolean and/or)
   - Alter Behavior of Queries.
   
### Full Text
  - Run full queries on full text fields
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
  -  `_score` (How well the search matched the query)
  -  `_source` (The JSON Data we added)
  
  
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

Looks for name field with name that has `money` **AND** `fluffy`.
```
GET /ecommerce/product/_search?q=name:(monkey AND fluffy)

# I get 1 result (If you followed from the top)
```

Looks for name field with name that has `money` **OR** `fluffy`.
```
GET /ecommerce/product/_search?q=name:(monkey OR fluffy)

# I get 2 results (If you followed from the top)
```

Even more complex
```
GET /ecommerce/product/_search?q=name:(monkey OR fluffy) AND status:active

# I get 1 results (If you followed from the top)
```

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
# Ensure: I get 1 results.
```

Switching the Order of the Terms will not work:
```
GET /ecommerce/product/_search?q=name:"fluffy monkey"
# Ensure: I get 0 results.
```

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

# Expected: Received 2 results
```


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

# Expected: Received 2 results
```

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

# Expected: 0 Results
```

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

