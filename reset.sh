#!/bin/bash


echo "\n(+) Flushing Data and importing mappings (Schema)"


# Remove _old data if it exists
echo "\n- Deleting ecommerce index if it exists"
curl -XDELETE http://localhost:9200/inventory
curl -XDELETE http://localhost:9200/product
curl -XDELETE http://localhost:9200/customer
curl -XDELETE http://localhost:9200/ecommerce



# Create Mappings
echo -e "\n- Creating mappings (For all Product Items)"
curl -XPUT http://localhost:9200/product --data @mappings/product.json;
curl -XPUT http://localhost:9200/customer --data @mappings/customer.json;

#exit

# Import Data
echo -e "\n- Importing Test Data by running ./import-all.sh \n"

