#!/bin/bash

echo "=== JASUR-SAVDO API QUICK TEST ==="
echo ""

# 1. Token olish
echo "1. Getting authentication token..."
TOKEN=$(curl -s -X POST http://localhost:8000/api/login -H "Content-Type: application/json" -d '{"email":"admin","password":"1234"}' | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "❌ Login failed!"
    exit 1
fi

echo "✅ Token received: ${TOKEN:0:20}..."
echo ""

# 2. Clients test
echo "2. Testing Clients API..."
CLIENT_RESULT=$(curl -s -X GET http://localhost:8000/api/clients -H "Authorization: Bearer $TOKEN" -H "Accept: application/json")
if [[ $CLIENT_RESULT == *"Client name"* ]]; then
    echo "✅ Clients API working"
else
    echo "❌ Clients API failed"
fi
echo ""

# 3. Products test
echo "3. Testing Products API..."
PRODUCT_RESULT=$(curl -s -X GET http://localhost:8000/api/products -H "Authorization: Bearer $TOKEN" -H "Accept: application/json")
if [[ $PRODUCT_RESULT == *"name"* ]]; then
    echo "✅ Products API working"
else
    echo "❌ Products API failed"
fi
echo ""

# 4. Optomchilar test
echo "4. Testing Optomchilar API..."
OPTOM_RESULT=$(curl -s -X GET http://localhost:8000/api/optomchilar -H "Authorization: Bearer $TOKEN" -H "Accept: application/json")
if [[ $OPTOM_RESULT == *"["* ]]; then
    echo "✅ Optomchilar list API working"
else
    echo "❌ Optomchilar list API failed"
fi

# 5. Create optomchi test
echo "5. Testing Create Optomchi API..."
RANDOM_PHONE="+99890$(date +%s | tail -c 8)"
CREATE_RESULT=$(curl -s -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test Optomchi $(date +%H%M%S)\",
    \"phone\": \"$RANDOM_PHONE\",
    \"address\": \"Test Address\",
    \"sale_type\": \"optom\",
    \"created_by\": \"Test Script\",
    \"items\": [
      {
        \"product_id\": 1,
        \"quantity\": 5,
        \"unit\": \"dona\",
        \"price\": 120
      }
    ]
  }")

if [[ $CREATE_RESULT == *"success"* ]]; then
    echo "✅ Create Optomchi API working"
    echo "📊 Created optomchi with phone: $RANDOM_PHONE"
else
    echo "❌ Create Optomchi API failed"
    echo "Error: $CREATE_RESULT"
fi

echo ""
echo "=== TEST COMPLETED ==="

