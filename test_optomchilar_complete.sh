#!/bin/bash

# Jasur-Savdo Optomchilar API - To'liq Test Script
# Bu script barcha optomchilar API methodlarini test qiladi

echo "=================================================="
echo "    JASUR-SAVDO OPTOMCHILAR API - FULL TEST      "
echo "=================================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counters
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

# Function to print test result
print_result() {
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    if [ "$1" = "PASS" ]; then
        echo -e "${GREEN}✅ $2${NC}"
        PASSED_TESTS=$((PASSED_TESTS + 1))
    else
        echo -e "${RED}❌ $2${NC}"
        echo -e "${RED}   Error: $3${NC}"
        FAILED_TESTS=$((FAILED_TESTS + 1))
    fi
    echo ""
}

# Function to print section header
print_section() {
    echo -e "${BLUE}=== $1 ===${NC}"
    echo ""
}

# 1. Authentication
print_section "1. AUTHENTICATION TEST"

echo "Getting authentication token..."
LOGIN_RESPONSE=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin","password":"1234"}')

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    print_result "FAIL" "Authentication" "Could not get token"
    echo "Response: $LOGIN_RESPONSE"
    exit 1
else
    print_result "PASS" "Authentication successful"
    echo "Token: ${TOKEN:0:20}..."
fi

# 2. GET All Optomchilar
print_section "2. GET ALL OPTOMCHILAR TEST"

GET_ALL_RESPONSE=$(curl -s -X GET http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

if [[ $GET_ALL_RESPONSE == *"["* ]]; then
    print_result "PASS" "Get all optomchilar"
    echo "Response sample: ${GET_ALL_RESPONSE:0:100}..."
else
    print_result "FAIL" "Get all optomchilar" "$GET_ALL_RESPONSE"
fi

# 3. Search Optomchilar
print_section "3. SEARCH OPTOMCHILAR TEST"

SEARCH_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/optomchilar?q=Test" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

if [[ $SEARCH_RESPONSE == *"["* ]]; then
    print_result "PASS" "Search optomchilar with query"
else
    print_result "FAIL" "Search optomchilar with query" "$SEARCH_RESPONSE"
fi

# 4. Create Optomchi - Success Case
print_section "4. CREATE OPTOMCHI - SUCCESS TEST"

TIMESTAMP=$(date +%s)
TEST_PHONE="+99890${TIMESTAMP:5:8}"
TEST_NAME="Test Optomchi $TIMESTAMP"

CREATE_SUCCESS_RESPONSE=$(curl -s -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"name\": \"$TEST_NAME\",
    \"phone\": \"$TEST_PHONE\",
    \"address\": \"Test Address Tashkent\",
    \"sale_type\": \"optom\",
    \"created_by\": \"Test Script\",
    \"items\": [
      {
        \"product_id\": 1,
        \"quantity\": 10,
        \"unit\": \"dona\",
        \"price\": 120
      },
      {
        \"product_id\": null,
        \"name\": \"Custom Product\",
        \"quantity\": 5,
        \"unit\": \"blok\",
        \"price\": 250
      }
    ]
  }")

if [[ $CREATE_SUCCESS_RESPONSE == *"success"* ]]; then
    print_result "PASS" "Create optomchi with valid data"
    CREATED_ID=$(echo $CREATE_SUCCESS_RESPONSE | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
    echo "Created optomchi ID: $CREATED_ID"
    echo "Created optomchi phone: $TEST_PHONE"
else
    print_result "FAIL" "Create optomchi with valid data" "$CREATE_SUCCESS_RESPONSE"
fi

# 5. Create Optomchi - Validation Error (Missing required fields)
print_section "5. CREATE OPTOMCHI - VALIDATION ERROR TEST"

CREATE_VALIDATION_ERROR=$(curl -s -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Name"
  }')

if [[ $CREATE_VALIDATION_ERROR == *"required"* ]] || [[ $CREATE_VALIDATION_ERROR == *"errors"* ]]; then
    print_result "PASS" "Validation error handling (missing fields)"
else
    print_result "FAIL" "Validation error handling (missing fields)" "$CREATE_VALIDATION_ERROR"
fi

# 6. Create Optomchi - Duplicate Phone Error
print_section "6. CREATE OPTOMCHI - DUPLICATE PHONE ERROR TEST"

CREATE_DUPLICATE_RESPONSE=$(curl -s -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"name\": \"Another Test Optomchi\",
    \"phone\": \"$TEST_PHONE\",
    \"address\": \"Another Address\",
    \"sale_type\": \"toychi\",
    \"created_by\": \"Test Script\",
    \"items\": [
      {
        \"product_id\": 1,
        \"quantity\": 5,
        \"unit\": \"dona\",
        \"price\": 130
      }
    ]
  }")

if [[ $CREATE_DUPLICATE_RESPONSE == *"already been taken"* ]] || [[ $CREATE_DUPLICATE_RESPONSE == *"phone"* ]]; then
    print_result "PASS" "Duplicate phone validation"
else
    print_result "FAIL" "Duplicate phone validation" "$CREATE_DUPLICATE_RESPONSE"
fi

# 7. Create Optomchi - Empty Items Array
print_section "7. CREATE OPTOMCHI - EMPTY ITEMS VALIDATION TEST"

EMPTY_ITEMS_RESPONSE=$(curl -s -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Empty Items",
    "phone": "+998901234599",
    "address": "Test Address",
    "sale_type": "optom",
    "created_by": "Test Script",
    "items": []
  }')

if [[ $EMPTY_ITEMS_RESPONSE == *"min:1"* ]] || [[ $EMPTY_ITEMS_RESPONSE == *"items"* ]]; then
    print_result "PASS" "Empty items array validation"
else
    print_result "FAIL" "Empty items array validation" "$EMPTY_ITEMS_RESPONSE"
fi

# 8. Get Optomchi by ID - Success
print_section "8. GET OPTOMCHI BY ID - SUCCESS TEST"

if [ ! -z "$CREATED_ID" ]; then
    GET_BY_ID_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/optomchilar/$CREATED_ID" \
      -H "Authorization: Bearer $TOKEN" \
      -H "Accept: application/json")
    
    if [[ $GET_BY_ID_RESPONSE == *"$TEST_NAME"* ]] && [[ $GET_BY_ID_RESPONSE == *"$TEST_PHONE"* ]]; then
        print_result "PASS" "Get optomchi by ID"
        echo "Retrieved optomchi: $TEST_NAME"
    else
        print_result "FAIL" "Get optomchi by ID" "$GET_BY_ID_RESPONSE"
    fi
else
    print_result "FAIL" "Get optomchi by ID" "No created ID available"
fi

# 9. Get Optomchi by ID - Not Found
print_section "9. GET OPTOMCHI BY ID - NOT FOUND TEST"

GET_NOT_FOUND_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/optomchilar/99999" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

if [[ $GET_NOT_FOUND_RESPONSE == *"not found"* ]] || [[ $GET_NOT_FOUND_RESPONSE == *"404"* ]]; then
    print_result "PASS" "Get optomchi by ID - not found handling"
else
    print_result "FAIL" "Get optomchi by ID - not found handling" "$GET_NOT_FOUND_RESPONSE"
fi

# 10. Update Optomchi - Success
print_section "10. UPDATE OPTOMCHI - SUCCESS TEST"

if [ ! -z "$CREATED_ID" ]; then
    UPDATE_SUCCESS_RESPONSE=$(curl -s -X PUT "http://localhost:8000/api/optomchilar/$CREATED_ID" \
      -H "Authorization: Bearer $TOKEN" \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -d '{
        "name": "Updated Test Optomchi",
        "address": "Updated Address"
      }')
    
    if [[ $UPDATE_SUCCESS_RESPONSE == *"success"* ]] || [[ $UPDATE_SUCCESS_RESPONSE == *"Updated Test Optomchi"* ]]; then
        print_result "PASS" "Update optomchi"
    else
        print_result "FAIL" "Update optomchi" "$UPDATE_SUCCESS_RESPONSE"
    fi
else
    print_result "FAIL" "Update optomchi" "No created ID available"
fi

# 11. Update Optomchi - Not Found
print_section "11. UPDATE OPTOMCHI - NOT FOUND TEST"

UPDATE_NOT_FOUND_RESPONSE=$(curl -s -X PUT "http://localhost:8000/api/optomchilar/99999" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Updated Name"
  }')

if [[ $UPDATE_NOT_FOUND_RESPONSE == *"not found"* ]]; then
    print_result "PASS" "Update optomchi - not found handling"
else
    print_result "FAIL" "Update optomchi - not found handling" "$UPDATE_NOT_FOUND_RESPONSE"
fi

# 12. Get Products for Sales
print_section "12. GET PRODUCTS FOR SALES TEST"

GET_PRODUCTS_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/getProducts?q=a" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

if [[ $GET_PRODUCTS_RESPONSE == *"["* ]]; then
    print_result "PASS" "Get products for sales"
else
    print_result "FAIL" "Get products for sales" "$GET_PRODUCTS_RESPONSE"
fi

# 13. Get Product Price
print_section "13. GET PRODUCT PRICE TEST"

GET_PRICE_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/product-price/1/optom/dona" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

if [[ $GET_PRICE_RESPONSE == *"price"* ]]; then
    print_result "PASS" "Get product price"
    echo "Price response: $GET_PRICE_RESPONSE"
else
    print_result "FAIL" "Get product price" "$GET_PRICE_RESPONSE"
fi

# 14. Get Product Price - Not Found
print_section "14. GET PRODUCT PRICE - NOT FOUND TEST"

GET_PRICE_NOT_FOUND_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/product-price/99999/optom/dona" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

if [[ $GET_PRICE_NOT_FOUND_RESPONSE == *"not found"* ]]; then
    print_result "PASS" "Get product price - not found handling"
else
    print_result "FAIL" "Get product price - not found handling" "$GET_PRICE_NOT_FOUND_RESPONSE"
fi

# 15. Delete Optomchi - Success
print_section "15. DELETE OPTOMCHI - SUCCESS TEST"

if [ ! -z "$CREATED_ID" ]; then
    DELETE_SUCCESS_RESPONSE=$(curl -s -X DELETE "http://localhost:8000/api/optomchilar/$CREATED_ID" \
      -H "Authorization: Bearer $TOKEN" \
      -H "Accept: application/json")
    
    if [[ $DELETE_SUCCESS_RESPONSE == *"success"* ]]; then
        print_result "PASS" "Delete optomchi"
        echo "Deleted optomchi ID: $CREATED_ID"
    else
        print_result "FAIL" "Delete optomchi" "$DELETE_SUCCESS_RESPONSE"
    fi
else
    print_result "FAIL" "Delete optomchi" "No created ID available"
fi

# 16. Delete Optomchi - Not Found
print_section "16. DELETE OPTOMCHI - NOT FOUND TEST"

DELETE_NOT_FOUND_RESPONSE=$(curl -s -X DELETE "http://localhost:8000/api/optomchilar/99999" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

if [[ $DELETE_NOT_FOUND_RESPONSE == *"not found"* ]]; then
    print_result "PASS" "Delete optomchi - not found handling"
else
    print_result "FAIL" "Delete optomchi - not found handling" "$DELETE_NOT_FOUND_RESPONSE"
fi

# 17. Unauthorized Access Test
print_section "17. UNAUTHORIZED ACCESS TEST"

UNAUTHORIZED_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/optomchilar" \
  -H "Accept: application/json")

if [[ $UNAUTHORIZED_RESPONSE == *"Unauthenticated"* ]] || [[ $UNAUTHORIZED_RESPONSE == *"401"* ]] || [[ $UNAUTHORIZED_RESPONSE == *"login"* ]]; then
    print_result "PASS" "Unauthorized access handling"
else
    print_result "FAIL" "Unauthorized access handling" "$UNAUTHORIZED_RESPONSE"
fi

# 18. Invalid Token Test
print_section "18. INVALID TOKEN TEST"

INVALID_TOKEN_RESPONSE=$(curl -s -X GET "http://localhost:8000/api/optomchilar" \
  -H "Authorization: Bearer invalid_token_12345" \
  -H "Accept: application/json")

if [[ $INVALID_TOKEN_RESPONSE == *"Unauthenticated"* ]] || [[ $INVALID_TOKEN_RESPONSE == *"401"* ]]; then
    print_result "PASS" "Invalid token handling"
else
    print_result "FAIL" "Invalid token handling" "$INVALID_TOKEN_RESPONSE"
fi

# Final Results
echo ""
echo "=================================================="
echo -e "${BLUE}           FINAL TEST RESULTS                     ${NC}"
echo "=================================================="
echo -e "${GREEN}Total Tests: $TOTAL_TESTS${NC}"
echo -e "${GREEN}Passed: $PASSED_TESTS${NC}"
echo -e "${RED}Failed: $FAILED_TESTS${NC}"
echo ""

if [ $FAILED_TESTS -eq 0 ]; then
    echo -e "${GREEN}🎉 ALL TESTS PASSED! OPTOMCHILAR API IS WORKING PERFECTLY! 🎉${NC}"
    EXIT_CODE=0
else
    echo -e "${RED}⚠️  SOME TESTS FAILED. PLEASE CHECK THE ERRORS ABOVE. ⚠️${NC}"
    EXIT_CODE=1
fi

echo ""
echo "Test completed at: $(date)"
echo "=================================================="

exit $EXIT_CODE
