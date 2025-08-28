# Jasur-Savdo API - To'liq Test Misollar

## Server ishga tushirish
```bash
cd /home/nodirbek/Desktop/jasur-savdo/admin
php artisan serve --host=127.0.0.1 --port=8000
```

## 1. AUTHENTICATION (Kirish/Chiqish)

### 1.1 Login - Muvaffaqiyatli
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin",
    "password": "1234"
  }'
```
**Response:**
```json
{
  "status": "success",
  "token": "8|randomTokenString..."
}
```

### 1.2 Login - Noto'g'ri email
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "wrongemail",
    "password": "1234"
  }'
```
**Response:**
```json
{
  "status": "error",
  "message": "Invalid credentials"
}
```

### 1.3 Login - Noto'g'ri parol
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin",
    "password": "wrongpassword"
  }'
```

### 1.4 Login - Validatsiya xatosi
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "password": "1234"
  }'
```
**Response:**
```json
{
  "message": "The email field is required.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### 1.5 Logout - Muvaffaqiyatli
```bash
# Avval login qiling va TOKEN ni oling
TOKEN=$(curl -s -X POST http://localhost:8000/api/login -H "Content-Type: application/json" -d '{"email":"admin","password":"1234"}' | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```
**Response:**
```json
{
  "status": "success",
  "message": "Logged out successfully"
}
```

### 1.6 Logout - Token bo'lmagan holat
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Accept: application/json"
```

---

## 2. MIJOZLAR (CLIENTS)

### 2.1 Barcha mijozlarni olish
```bash
TOKEN="your_token_here"

curl -X GET http://localhost:8000/api/clients \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 2.2 Mijozlarni qidirish
```bash
curl -X GET "http://localhost:8000/api/clients?search=Client" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 2.3 Yangi mijoz qo'shish - Muvaffaqiyatli
```bash
curl -X POST http://localhost:8000/api/clients \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangi Mijoz",
    "info": "Mijoz haqida ma'\''lumot",
    "phone": "+998901234570",
    "image": "client.jpg",
    "debt": 50000,
    "recorded_by": "Admin"
  }'
```

### 2.4 Mijoz qo'shish - Validatsiya xatosi
```bash
curl -X POST http://localhost:8000/api/clients \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "info": "Mijoz haqida ma'\''lumot"
  }'
```

### 2.5 Mijozni ID bo'yicha olish
```bash
curl -X GET http://localhost:8000/api/clients/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 2.6 Mijozni yangilash
```bash
curl -X PUT http://localhost:8000/api/clients/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangilangan Mijoz",
    "phone": "+998901234571"
  }'
```

### 2.7 Mijozni o'chirish
```bash
curl -X DELETE http://localhost:8000/api/clients/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 2.8 Mijoz qarzini o'zgartirish
```bash
curl -X POST http://localhost:8000/api/clients/1/change-debt \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 25000,
    "status": "oldi",
    "recorded_by": "Admin"
  }'
```

---

## 3. FIRMALAR (FIRMS)

### 3.1 Barcha firmalarni olish
```bash
curl -X GET http://localhost:8000/api/firms \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 3.2 Yangi firma qo'shish
```bash
curl -X POST http://localhost:8000/api/firms \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangi Firma",
    "info": "Firma haqida ma'\''lumot",
    "phone": "+998901234572",
    "image": "firma.jpg",
    "debt": 100000,
    "recorded_by": "Admin"
  }'
```

### 3.3 Firmani yangilash
```bash
curl -X PUT http://localhost:8000/api/firms/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangilangan Firma",
    "debt": 150000
  }'
```

### 3.4 Firmani o'chirish
```bash
curl -X DELETE http://localhost:8000/api/firms/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 3.5 Firma qarzini o'zgartirish
```bash
curl -X POST http://localhost:8000/api/firms/1/change-debt \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 50000,
    "status": "berdi",
    "recorded_by": "Admin"
  }'
```

---

## 4. BUYURTMALAR (ORDERS)

### 4.1 Barcha buyurtmalarni olish
```bash
curl -X GET http://localhost:8000/api/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 4.2 Yangi buyurtma qo'shish
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "client_name": "Mijoz Ismi",
    "product_name": "Mahsulot Nomi",
    "quantity": 10,
    "unit": "dona",
    "price": 15000,
    "total_price": 150000,
    "status": "pending",
    "order_date": "2025-08-27",
    "delivery_date": "2025-08-30",
    "recorded_by": "Admin"
  }'
```

### 4.3 Buyurtmani yangilash
```bash
curl -X PUT http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "completed",
    "quantity": 12,
    "total_price": 180000
  }'
```

---

## 5. MAHSULOTLAR (PRODUCTS)

### 5.1 Barcha mahsulotlarni olish
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 5.2 Mahsulotlarni qidirish
```bash
curl -X GET "http://localhost:8000/api/products?search=Olma" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 5.3 Yangi mahsulot qo'shish
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangi Mahsulot",
    "image": "mahsulot.jpg",
    "kelish_narxi": 100,
    "sotish_narxi_optom_dona": 120,
    "sotish_narxi_optom_blok": 110,
    "sotish_narxi_toyga_dona": 130,
    "sotish_narxi_toyga_blok": 125,
    "recorded_by": "Admin"
  }'
```

### 5.4 Mahsulotni yangilash
```bash
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangilangan Mahsulot",
    "sotish_narxi_optom_dona": 125,
    "sotish_narxi_toyga_dona": 135
  }'
```

### 5.5 Mahsulotni o'chirish
```bash
curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

---

## 6. ISHCHILAR (WORKERS)

### 6.1 Barcha ishchilarni olish
```bash
curl -X GET http://localhost:8000/api/workers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 6.2 Yangi ishchi qo'shish
```bash
curl -X POST http://localhost:8000/api/workers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangi Ishchi",
    "info": "Ishchi haqida ma'\''lumot",
    "phone": "+998901234573",
    "image": "worker.jpg",
    "salary": 2000000,
    "recorded_by": "Admin"
  }'
```

### 6.3 Ishchini yangilash
```bash
curl -X POST http://localhost:8000/api/workers/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangilangan Ishchi",
    "salary": 2500000
  }'
```

### 6.4 Ishchi maoshini to'lash
```bash
curl -X POST http://localhost:8000/api/workers/1/pays \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 500000,
    "recorded_by": "Admin"
  }'
```

---

## 7. OPTOMCHILAR VA SAVDO (SALES)

### 7.1 Barcha optomchilarni olish
```bash
curl -X GET http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 7.2 Optomchilarni qidirish
```bash
curl -X GET "http://localhost:8000/api/optomchilar?q=Test" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 7.3 Yangi optomchi va savdo qo'shish - Muvaffaqiyatli
```bash
curl -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangi Optomchi",
    "phone": "+998901234575",
    "address": "Tashkent shahar",
    "sale_type": "optom",
    "created_by": "Admin",
    "items": [
      {
        "product_id": 1,
        "quantity": 20,
        "unit": "dona",
        "price": 120
      },
      {
        "product_id": null,
        "name": "Maxsus mahsulot",
        "quantity": 5,
        "unit": "blok",
        "price": 500
      }
    ]
  }'
```

### 7.4 Optomchi qo'shish - Validatsiya xatosi (telefon noyob emas)
```bash
curl -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangi Optomchi 2",
    "phone": "+998901234575",
    "address": "Samarqand shahar",
    "sale_type": "toychi",
    "created_by": "Admin",
    "items": [
      {
        "product_id": 1,
        "quantity": 10,
        "unit": "dona",
        "price": 130
      }
    ]
  }'
```
**Response:**
```json
{
  "message": "The phone has already been taken.",
  "errors": {
    "phone": ["The phone has already been taken."]
  }
}
```

### 7.5 Optomchi qo'shish - Items bo'sh
```bash
curl -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangi Optomchi 3",
    "phone": "+998901234576",
    "address": "Buxoro shahar",
    "sale_type": "optom",
    "created_by": "Admin",
    "items": []
  }'
```

### 7.6 Optomchini ID bo'yicha olish
```bash
curl -X GET http://localhost:8000/api/optomchilar/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 7.7 Optomchini yangilash
```bash
curl -X PUT http://localhost:8000/api/optomchilar/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yangilangan Optomchi",
    "address": "Yangilangan manzil",
    "sales": [
      {
        "id": 1,
        "total_sum": 5000
      }
    ],
    "sale_items": [
      {
        "id": 1,
        "quantity": 25,
        "unit": "dona",
        "price": 125
      }
    ]
  }'
```

### 7.8 Optomchini o'chirish
```bash
curl -X DELETE http://localhost:8000/api/optomchilar/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 7.9 Sale item'ni o'chirish
```bash
curl -X DELETE http://localhost:8000/api/optomchilar/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```
**Note:** Bu ID optomchi ID'si yoki sale_item ID'si bo'lishi mumkin

---

## 8. MAHSULOTLAR BILAN ISHLASH (SALE CONTROLLER)

### 8.1 Mahsulotlarni qidirish
```bash
curl -X GET "http://localhost:8000/api/getProducts?q=Olma" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 8.2 Mahsulot narxini olish
```bash
curl -X GET http://localhost:8000/api/product-price/1/optom/dona \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 8.3 Mahsulot narxini olish - toychi uchun
```bash
curl -X GET http://localhost:8000/api/product-price/1/toychi/blok \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### 8.4 Mahsulot narxini olish - mavjud bo'lmagan mahsulot
```bash
curl -X GET http://localhost:8000/api/product-price/999/optom/dona \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

---

## XATO HOLATLARI VA JAVOBLAR

### Authorization xatosi
```bash
curl -X GET http://localhost:8000/api/clients \
  -H "Accept: application/json"
```
**Response:** 401 Unauthorized

### Noto'g'ri token
```bash
curl -X GET http://localhost:8000/api/clients \
  -H "Authorization: Bearer invalid_token" \
  -H "Accept: application/json"
```

### Ma'lumot topilmagan
```bash
curl -X GET http://localhost:8000/api/clients/999 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```
**Response:**
```json
{
  "status": "error",
  "message": "Client not found"
}
```

---

## TEST JARAYONI

### 1. Tokenni olish
```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/login -H "Content-Type: application/json" -d '{"email":"admin","password":"1234"}' | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
echo "Token: $TOKEN"
```

### 2. Barcha endpointlarni ketma-ket test qilish
```bash
# Test script yaratish
cat > test_all_endpoints.sh << 'EOF'
#!/bin/bash

# Token olish
echo "1. Getting token..."
TOKEN=$(curl -s -X POST http://localhost:8000/api/login -H "Content-Type: application/json" -d '{"email":"admin","password":"1234"}' | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "Login failed!"
    exit 1
fi

echo "Token received: $TOKEN"

# Clients test
echo "2. Testing clients..."
curl -s -X GET http://localhost:8000/api/clients -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" | head -c 100
echo ""

# Products test  
echo "3. Testing products..."
curl -s -X GET http://localhost:8000/api/products -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" | head -c 100
echo ""

# Optomchilar test
echo "4. Testing optomchilar..."
curl -s -X GET http://localhost:8000/api/optomchilar -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" | head -c 100
echo ""

# Test optomchi qo'shish
echo "5. Testing optomchi creation..."
PHONE="+99890$(date +%s)"
curl -s -X POST http://localhost:8000/api/optomchilar \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test Optomchi $(date +%H%M%S)\",
    \"phone\": \"$PHONE\",
    \"address\": \"Test Address\",
    \"sale_type\": \"optom\",
    \"created_by\": \"Test User\",
    \"items\": [
      {
        \"product_id\": 1,
        \"quantity\": 5,
        \"unit\": \"dona\",
        \"price\": 120
      }
    ]
  }" | head -c 200
echo ""

echo "All tests completed!"
EOF

chmod +x test_all_endpoints.sh
./test_all_endpoints.sh
```

### 3. Ma'lumotlar bazasini tekshirish
```bash
# Optomchilar jadvalidagi ma'lumotlarni ko'rish
php artisan tinker --execute="echo json_encode(App\Models\Optom::all()->toArray(), JSON_PRETTY_PRINT);"

# Sales jadvalidagi ma'lumotlarni ko'rish  
php artisan tinker --execute="echo json_encode(App\Models\Sale::with(['optom', 'saleItems'])->get()->toArray(), JSON_PRETTY_PRINT);"
```

---

## FRONTEND UCHUN MISOLLAR (JavaScript)

### Login va token saqlash
```javascript
// Login function
async function login(email, password) {
  try {
    const response = await fetch('http://localhost:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: email,
        password: password
      })
    });
    
    const data = await response.json();
    
    if (data.status === 'success') {
      localStorage.setItem('auth_token', data.token);
      return { success: true, token: data.token };
    } else {
      return { success: false, message: data.message };
    }
  } catch (error) {
    return { success: false, message: 'Network error' };
  }
}

// API request with token
async function apiRequest(url, options = {}) {
  const token = localStorage.getItem('auth_token');
  
  const defaultOptions = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${token}`
    }
  };
  
  const finalOptions = {
    ...defaultOptions,
    ...options,
    headers: {
      ...defaultOptions.headers,
      ...options.headers
    }
  };
  
  return fetch(url, finalOptions);
}

// Optomchi qo'shish example
async function addOptomchi(optomchiData) {
  try {
    const response = await apiRequest('http://localhost:8000/api/optomchilar', {
      method: 'POST',
      body: JSON.stringify(optomchiData)
    });
    
    return await response.json();
  } catch (error) {
    console.error('Error adding optomchi:', error);
    return { success: false, message: 'Network error' };
  }
}

// Usage
const optomchiData = {
  name: "Frontend Test Optomchi",
  phone: "+998901234580",
  address: "Frontend Test Address", 
  sale_type: "optom",
  created_by: "Frontend User",
  items: [
    {
      product_id: 1,
      quantity: 15,
      unit: "dona",
      price: 120
    }
  ]
};

addOptomchi(optomchiData).then(result => {
  console.log('Result:', result);
});
```

---

Bu faylda barcha API endpointlar uchun to'liq test misollar va har xil holatlar ko'rsatilgan. Har bir endpoint uchun muvaffaqiyatli va muvaffaqiyatsiz holatlar, validatsiya xatolari va to'g'ri javoblar namunalari berilgan.
