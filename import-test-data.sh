#!/bin/bash


echo "\n(+) Installing fake data into /ecommerce/product"



# Remove _old data if it exists
echo "\n- Deleting ecommerce index if it exists"
curl -XDELETE http://localhost:9200/ecommerce



# Create Mappings
echo -e "\n- Creating mappings"
curl -XPUT http://localhost:9200/ecommerce --data @test-mapping.json;

#exit

# Import Data
echo -e "\n- Importing Test Data"
curl -XPOST http://localhost:9200/ecommerce/product/_bulk --data-binary @test-data.json;

