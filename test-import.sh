#!/bin/bash

echo "(+) Installing fake data into /ecommerce/product"
curl -XPOST http://localhost:9200/ecommerce/product/_bulk --data-binary @test-data.json;
