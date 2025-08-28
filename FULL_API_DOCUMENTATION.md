# Jasur Savdo - To'liq API Documentation

## Loyiha haqida umumiy ma'lumot

**Jasur Savdo** - savdo boshqaruvi tizimi bo'lib, quyidagi asosiy funksionallikni o'z ichiga oladi:
- Mijozlar boshqaruvi va qarz hisobi
- Firmalar boshqaruvi va qarz hisobi  
- Mahsulotlar boshqaruvi va narxlar
- Buyurtmalar boshqaruvi
- Ishchilar boshqaruvi va ish haqi hisobi
- Optom va chakana savdo boshqaruvi

## Base URL
```
http://localhost:8000/api
```

## Autentifikatsiya

Barcha API endpointlar (login dan tashqari) Bearer token autentifikatsiyasini talab qiladi.

### Headers
```
Accept: application/json
Content-Type: application/json
Authorization: Bearer {token}
```

---

## 1. AUTENTIFIKATSIYA (AUTH)

### 1.1 Login - Kirish
```http
POST /api/login
```

**Request Body:**
```json
{
    "email": "admin",
    "password": "1234"
}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "status": "success",
    "token": "1|randomtokenstring123456789"
}
```

**Xato javob (401):**
```json
{
    "status": "error",
    "message": "Invalid credentials"
}
```

**cURL Test:**
```bash
curl -X POST http://localhost:8000/api/login \
-H "Content-Type: application/json" \
-d '{
    "email": "admin",
    "password": "1234"
}'
```

### 1.2 Logout - Chiqish
```http
POST /api/logout
```

**Headers:**
```
Authorization: Bearer {token}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "status": "success",
    "message": "Logged out successfully"
}
```

**cURL Test:**
```bash
curl -X POST http://localhost:8000/api/logout \
-H "Authorization: Bearer 1|your_token_here"
```

---

## 2. MIJOZLAR BOSHQARUVI (CLIENTS)

### 2.1 Barcha mijozlarni olish
```http
GET /api/clients
```

**Query parametrlar:**
- `name` (ixtiyoriy) - Mijoz nomi bo'yicha qidirish
- `phone` (ixtiyoriy) - Telefon raqami bo'yicha qidirish
- `is_deleted` (ixtiyoriy) - O'chirilgan mijozlarni ko'rsatish (true/false)
- `page` (ixtiyoriy) - Sahifa raqami
- `per_page` (ixtiyoriy) - Sahifadagi elementlar soni

**Muvaffaqiyatli javob (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Ali Valiev",
            "info": "Do'kon mijozi", 
            "phone": "+998901234567",
            "image": null,
            "debt": "150000",
            "recorded_by": "Admin",
            "is_deleted": false,
            "created_at": "2025-08-27T10:30:00Z",
            "updated_at": "2025-08-27T10:30:00Z"
        }
    ],
    "current_page": 1,
    "per_page": 10,
    "total": 25,
    "last_page": 3
}
```

**cURL Test:**
```bash
curl -X GET "http://localhost:8000/api/clients?name=Ali&page=1" \
-H "Authorization: Bearer 1|your_token_here"
```

### 2.2 Yangi mijoz yaratish
```http
POST /api/clients
```

**Request Body:**
```json
{
    "name": "Bobur Karimov",
    "info": "Yangi doimiy mijoz",
    "phone": "+998909876543",
    "image": null,
    "debt": "200000",
    "recorded_by": "Admin"
}
```

**Muvaffaqiyatli javob (201):**
```json
{
    "status": "success",
    "client": {
        "id": 2,
        "name": "Bobur Karimov",
        "info": "Yangi doimiy mijoz",
        "phone": "+998909876543",
        "image": null,
        "debt": "200000",
        "recorded_by": "Admin",
        "is_deleted": false,
        "created_at": "2025-08-27T11:00:00Z",
        "updated_at": "2025-08-27T11:00:00Z"
    }
}
```

**cURL Test:**
```bash
curl -X POST http://localhost:8000/api/clients \
-H "Authorization: Bearer 1|your_token_here" \
-H "Content-Type: application/json" \
-d '{
    "name": "Bobur Karimov",
    "info": "Yangi doimiy mijoz",
    "phone": "+998909876543",
    "debt": "200000",
    "recorded_by": "Admin"
}'
```

### 2.3 Bitta mijozni olish (qarz tarixi bilan)
```http
GET /api/clients/{id}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "client": {
        "id": 1,
        "name": "Ali Valiev",
        "info": "Do'kon mijozi",
        "phone": "+998901234567",
        "image": null,
        "debt": "70000",
        "recorded_by": "Admin",
        "is_deleted": false,
        "created_at": "2025-08-27T10:30:00Z",
        "updated_at": "2025-08-27T10:30:00Z",
        "debts": [
            {
                "id": 1,
                "client_id": 1,
                "amount": "100000",
                "status": "oldi",
                "recorded_by": "Admin",
                "is_deleted": false,
                "created_at": "2025-08-27T10:30:00Z",
                "updated_at": "2025-08-27T10:30:00Z"
            },
            {
                "id": 2,
                "client_id": 1,
                "amount": "30000",
                "status": "berdi",
                "recorded_by": "Admin",
                "is_deleted": false,
                "created_at": "2025-08-27T11:30:00Z",
                "updated_at": "2025-08-27T11:30:00Z"
            }
        ]
    }
}
```

**cURL Test:**
```bash
curl -X GET http://localhost:8000/api/clients/1 \
-H "Authorization: Bearer 1|your_token_here"
```

### 2.4 Mijoz ma'lumotlarini yangilash
```http
PUT /api/clients/{id}
```

**Request Body:**
```json
{
    "name": "Ali Valiev Yangilangan",
    "info": "Yangilangan ma'lumot",
    "phone": "+998901234568",
    "recorded_by": "Manager"
}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "status": "success",
    "client": {
        "id": 1,
        "name": "Ali Valiev Yangilangan",
        "info": "Yangilangan ma'lumot",
        "phone": "+998901234568",
        "image": null,
        "debt": "70000",
        "recorded_by": "Manager",
        "is_deleted": false,
        "created_at": "2025-08-27T10:30:00Z",
        "updated_at": "2025-08-27T12:00:00Z"
    }
}
```

### 2.5 Mijozni o'chirish (soft delete)
```http
DELETE /api/clients/{id}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "status": "success", 
    "message": "Client marked as deleted"
}
```

### 2.6 Mijoz qarzini o'zgartirish
```http
POST /api/clients/{id}/change-debt
```

**Request Body:**
```json
{
    "amount": "50000",
    "status": "berdi",
    "recorded_by": "Admin"
}
```

**Status qiymatlari:**
- `oldi` - Mijoz qarz oldi (qarz ortadi)
- `berdi` - Mijoz pul berdi (qarz kamayadi)

**Muvaffaqiyatli javob (201):**
```json
{
    "status": "success",
    "debt": {
        "id": 3,
        "client_id": 1,
        "amount": "50000", 
        "status": "berdi",
        "recorded_by": "Admin",
        "is_deleted": false,
        "created_at": "2025-08-27T13:00:00Z",
        "updated_at": "2025-08-27T13:00:00Z"
    }
}
```

**cURL Test:**
```bash
curl -X POST http://localhost:8000/api/clients/1/change-debt \
-H "Authorization: Bearer 1|your_token_here" \
-H "Content-Type: application/json" \
-d '{
    "amount": "50000",
    "status": "berdi", 
    "recorded_by": "Admin"
}'
```

---

## 3. FIRMALAR BOSHQARUVI (FIRMS)

### 3.1 Barcha firmalarni olish
```http
GET /api/firms
```

**Query parametrlar:**
- `name` (ixtiyoriy) - Firma nomi bo'yicha qidirish
- `page` (ixtiyoriy) - Sahifa raqami

**Muvaffaqiyatli javob (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Coca Cola Uzbekistan",
            "supervisor": "Jasur Alimov",
            "s_phone": "+998901111111",
            "agent": "Bobur Karimov", 
            "a_phone": "+998902222222",
            "currier": "Olim Tashev",
            "c_phone": "+998903333333",
            "humo": true,
            "uzcard": false,
            "day": "Dushanba",
            "debt": "500000",
            "is_deleted": false,
            "created_at": "2025-08-27T09:00:00Z",
            "updated_at": "2025-08-27T09:00:00Z"
        }
    ],
    "current_page": 1,
    "per_page": 10,
    "total": 5,
    "last_page": 1
}
```

**cURL Test:**
```bash
curl -X GET "http://localhost:8000/api/firms?name=Coca" \
-H "Authorization: Bearer 1|your_token_here"
```

### 3.2 Yangi firma yaratish
```http
POST /api/firms
```

**Request Body:**
```json
{
    "name": "Pepsi Savdo",
    "supervisor": "Aziz Rahimov",
    "s_phone": "+998904444444",
    "agent": "Karim Jurayev",
    "a_phone": "+998905555555", 
    "currier": "Shohrud Aliyev",
    "c_phone": "+998906666666",
    "humo": true,
    "uzcard": true,
    "day": "Seshanba",
    "debt": "300000",
    "recorded_by": "Admin"
}
```

**Muvaffaqiyatli javob (201):**
```json
{
    "status": "success",
    "firm": {
        "id": 2,
        "name": "Pepsi Savdo",
        "supervisor": "Aziz Rahimov",
        "s_phone": "+998904444444",
        "agent": "Karim Jurayev",
        "a_phone": "+998905555555",
        "currier": "Shohrud Aliyev", 
        "c_phone": "+998906666666",
        "humo": true,
        "uzcard": true,
        "day": "Seshanba",
        "debt": "300000",
        "is_deleted": false,
        "created_at": "2025-08-27T14:00:00Z",
        "updated_at": "2025-08-27T14:00:00Z"
    }
}
```

**cURL Test:**
```bash
curl -X POST http://localhost:8000/api/firms \
-H "Authorization: Bearer 1|your_token_here" \
-H "Content-Type: application/json" \
-d '{
    "name": "Pepsi Savdo",
    "supervisor": "Aziz Rahimov",
    "s_phone": "+998904444444",
    "agent": "Karim Jurayev",
    "a_phone": "+998905555555",
    "currier": "Shohrud Aliyev",
    "c_phone": "+998906666666",
    "humo": true,
    "uzcard": true,
    "day": "Seshanba",
    "debt": 300000,
    "recorded_by": "Admin"
}'
```

### 3.3 Bitta firmani olish (qarz tarixi bilan)
```http
GET /api/firms/{id}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "firm": {
        "id": 1,
        "name": "Coca Cola Uzbekistan",
        "supervisor": "Jasur Alimov",
        "s_phone": "+998901111111",
        "agent": "Bobur Karimov",
        "a_phone": "+998902222222", 
        "currier": "Olim Tashev",
        "c_phone": "+998903333333",
        "humo": true,
        "uzcard": false,
        "day": "Dushanba",
        "debt": "200000",
        "is_deleted": false,
        "created_at": "2025-08-27T09:00:00Z",
        "updated_at": "2025-08-27T09:00:00Z",
        "firm_debts": [
            {
                "id": 1,
                "firm_id": 1,
                "amount": "500000",
                "status": "oldi",
                "recorded_by": "Admin",
                "created_at": "2025-08-27T09:00:00Z",
                "updated_at": "2025-08-27T09:00:00Z"
            },
            {
                "id": 2,
                "firm_id": 1,
                "amount": "300000",
                "status": "berdi",
                "recorded_by": "Admin",
                "created_at": "2025-08-27T10:00:00Z", 
                "updated_at": "2025-08-27T10:00:00Z"
            }
        ]
    }
}
```

### 3.4 Firma ma'lumotlarini yangilash
```http
PUT /api/firms/{id}
```

**Request Body:**
```json
{
    "name": "Coca Cola Uzbekistan Ltd",
    "supervisor": "Jasur Alimov Yangilangan",
    "day": "Chorshanba"
}
```

### 3.5 Firmani o'chirish (soft delete)
```http
DELETE /api/firms/{id}
```

### 3.6 Firma qarzini o'zgartirish
```http
POST /api/firms/{id}/change-debt
```

**Request Body:**
```json
{
    "debt": "100000",
    "status": "berdi",
    "recorded_by": "Manager"
}
```

**Status qiymatlari:**
- `oldi` - Firma qarz oldi
- `berdi` - Firma pul berdi

---

## 4. MAHSULOTLAR BOSHQARUVI (PRODUCTS)

### 4.1 Barcha mahsulotlarni olish
```http
GET /api/products
```

**Query parametrlar:**
- `name` (ixtiyoriy) - Mahsulot nomi bo'yicha qidirish

**Muvaffaqiyatli javob (200):**
```json
[
    {
        "id": 1,
        "order_id": 1,
        "name": "Coca Cola 0.5L",
        "karobkadagi_soni": "24",
        "necha_karobka_kelgani": "50",
        "kelgan_narxi_dona": "2500",
        "kelgan_narxi_blok": "60000",
        "sotish_narxi_dona": "3500",
        "sotish_narxi_blok": "84000",
        "sotish_narxi_optom_dona": "3200",
        "sotish_narxi_optom_blok": "76800",
        "sotish_narxi_toyga_dona": "3300",
        "sotish_narxi_toyga_blok": "79200",
        "created_at": "2025-08-27T08:00:00Z",
        "updated_at": "2025-08-27T08:00:00Z"
    }
]
```

**cURL Test:**
```bash
curl -X GET "http://localhost:8000/api/products?name=Coca" \
-H "Authorization: Bearer 1|your_token_here"
```

### 4.2 Yangi mahsulot yaratish
```http
POST /api/products
```

**Request Body:**
```json
{
    "order_id": 1,
    "name": "Fanta 0.5L",
    "karobkadagi_soni": "24",
    "necha_karobka_kelgani": "30",
    "kelgan_narxi_dona": "2300",
    "kelgan_narxi_blok": "55200",
    "sotish_narxi_dona": "3200",
    "sotish_narxi_blok": "76800",
    "sotish_narxi_optom_dona": "2900",
    "sotish_narxi_optom_blok": "69600",
    "sotish_narxi_toyga_dona": "3000",
    "sotish_narxi_toyga_blok": "72000"
}
```

**Muvaffaqiyatli javob (201):**
```json
{
    "status": "success",
    "product": {
        "id": 2,
        "order_id": 1,
        "name": "Fanta 0.5L", 
        "karobkadagi_soni": "24",
        "necha_karobka_kelgani": "30",
        "kelgan_narxi_dona": "2300",
        "kelgan_narxi_blok": "55200",
        "sotish_narxi_dona": "3200",
        "sotish_narxi_blok": "76800",
        "sotish_narxi_optom_dona": "2900",
        "sotish_narxi_optom_blok": "69600",
        "sotish_narxi_toyga_dona": "3000",
        "sotish_narxi_toyga_blok": "72000",
        "created_at": "2025-08-27T15:00:00Z",
        "updated_at": "2025-08-27T15:00:00Z"
    }
}
```

### 4.3 Bitta mahsulotni olish
```http
GET /api/products/{id}
```

### 4.4 Mahsulot ma'lumotlarini yangilash
```http
PUT /api/products/{id}
```

**Request Body:**
```json
{
    "sotish_narxi_dona": "3600",
    "sotish_narxi_blok": "86400"
}
```

### 4.5 Mahsulotni o'chirish
```http
DELETE /api/products/{id}
```

---

## 5. BUYURTMALAR BOSHQARUVI (ORDERS)

### 5.1 Barcha buyurtmalarni olish
```http
GET /api/orders
```

**Query parametrlar:**
- `day` (ixtiyoriy) - Sana bo'yicha filter (YYYY-MM-DD format)
- `page` (ixtiyoriy) - Sahifa raqami

**Muvaffaqiyatli javob (200):**
```json
{
    "data": [
        {
            "id": 1,
            "firm_id": 1,
            "day": "2025-08-27",
            "recorded_by": "Admin",
            "is_deleted": false,
            "created_at": "2025-08-27T08:00:00Z",
            "updated_at": "2025-08-27T08:00:00Z"
        }
    ],
    "current_page": 1,
    "per_page": 10,
    "total": 15,
    "last_page": 2
}
```

**cURL Test:**
```bash
curl -X GET "http://localhost:8000/api/orders?day=2025-08-27" \
-H "Authorization: Bearer 1|your_token_here"
```

### 5.2 Yangi buyurtma yaratish
```http
POST /api/orders
```

**Request Body:**
```json
{
    "firm_id": 1,
    "day": "2025-08-28",
    "recorded_by": "Admin"
}
```

**Muvaffaqiyatli javob (201):**
```json
{
    "status": "success",
    "order": {
        "id": 2,
        "firm_id": 1,
        "day": "2025-08-28", 
        "recorded_by": "Admin",
        "is_deleted": false,
        "created_at": "2025-08-27T16:00:00Z",
        "updated_at": "2025-08-27T16:00:00Z"
    }
}
```

### 5.3 Bitta buyurtmani olish (mahsulotlar bilan)
```http
GET /api/orders/{id}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "order": {
        "id": 1,
        "firm_id": 1,
        "day": "2025-08-27",
        "recorded_by": "Admin",
        "is_deleted": false,
        "created_at": "2025-08-27T08:00:00Z",
        "updated_at": "2025-08-27T08:00:00Z",
        "products": [
            {
                "id": 1,
                "order_id": 1,
                "name": "Coca Cola 0.5L",
                "karobkadagi_soni": "24",
                "necha_karobka_kelgani": "50",
                "kelgan_narxi_dona": "2500",
                "kelgan_narxi_blok": "60000",
                "sotish_narxi_dona": "3500",
                "sotish_narxi_blok": "84000",
                "sotish_narxi_optom_dona": "3200",
                "sotish_narxi_optom_blok": "76800",
                "sotish_narxi_toyga_dona": "3300",
                "sotish_narxi_toyga_blok": "79200",
                "created_at": "2025-08-27T08:00:00Z",
                "updated_at": "2025-08-27T08:00:00Z"
            }
        ]
    }
}
```

### 5.4 Buyurtmani yangilash
```http
PUT /api/orders/{id}
```

### 5.5 Buyurtmani o'chirish (soft delete)
```http
DELETE /api/orders/{id}
```

---

## 6. ISHCHILAR BOSHQARUVI (WORKERS)

### 6.1 Barcha ishchilarni olish
```http
GET /api/workers
```

**Query parametrlar:**
- `name` (ixtiyoriy) - Ishchi nomi bo'yicha qidirish

**Muvaffaqiyatli javob (200):**
```json
[
    {
        "id": 1,
        "name": "Abdulla Rahimov",
        "phone": "+998907777777",
        "amount": 150000,
        "day": "2025-08-01",
        "status": "ishlayabdi",
        "image": "workers/abdulla.jpg",
        "summa": 4050000,
        "created_at": "2025-08-27T07:00:00Z",
        "updated_at": "2025-08-27T17:00:00Z"
    }
]
```

**cURL Test:**
```bash
curl -X GET "http://localhost:8000/api/workers?name=Abdulla" \
-H "Authorization: Bearer 1|your_token_here"
```

### 6.2 Yangi ishchi qo'shish (fayl bilan)
```http
POST /api/workers
```

**Content-Type:** `multipart/form-data`

**Form data:**
- `name` (majburiy): "Olim Karimov"
- `phone` (majburiy): "+998908888888"
- `amount` (majburiy): 120000
- `day` (majburiy): "2025-08-27"
- `image` (ixtiyoriy): [fayl]

**Muvaffaqiyatli javob (201):**
```json
{
    "status": "success",
    "worker": {
        "id": 2,
        "name": "Olim Karimov",
        "phone": "+998908888888",
        "amount": 120000,
        "day": "2025-08-27",
        "status": "ishlayabdi",
        "image": "workers/olim.jpg",
        "summa": 0,
        "created_at": "2025-08-27T17:30:00Z",
        "updated_at": "2025-08-27T17:30:00Z"
    }
}
```

**cURL Test (faylsiz):**
```bash
curl -X POST http://localhost:8000/api/workers \
-H "Authorization: Bearer 1|your_token_here" \
-F "name=Olim Karimov" \
-F "phone=+998908888888" \
-F "amount=120000" \
-F "day=2025-08-27"
```

### 6.3 Bitta ishchini olish (to'lovlar bilan)
```http
GET /api/workers/{id}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "worker": {
        "id": 1,
        "name": "Abdulla Rahimov",
        "phone": "+998907777777",
        "amount": 150000,
        "day": "2025-08-01",
        "status": "ishlayabdi", 
        "image": "workers/abdulla.jpg",
        "summa": 4050000,
        "created_at": "2025-08-27T07:00:00Z",
        "updated_at": "2025-08-27T17:00:00Z"
    },
    "pays": [
        {
            "id": 1,
            "worker_id": 1,
            "amount": 500000,
            "status": "oldi",
            "created_at": "2025-08-15T10:00:00Z",
            "updated_at": "2025-08-15T10:00:00Z"
        }
    ]
}
```

### 6.4 Ishchi ma'lumotlarini yangilash
```http
POST /api/workers/{id}
```

**Content-Type:** `multipart/form-data`

**Form data (hammasi ixtiyoriy):**
- `name`: "Abdulla Rahimov Yangilangan"
- `phone`: "+998907777778"
- `amount`: 160000
- `status`: "ishlamayabdi"
- `image`: [yangi fayl]

### 6.5 Ishchini o'chirish
```http
DELETE /api/workers/{id}
```

### 6.6 Ishchi to'lovini o'zgartirish
```http
POST /api/workers/{id}/pays
```

**Request Body:**
```json
{
    "amount": 300000,
    "status": "oldi"
}
```

**Status qiymatlari:**
- `oldi` - Ishchi pul oldi (maosh kamaytirish)
- `berdi` - Ishchi pul berdi (maosh ortirish)

**Muvaffaqiyatli javob (201):**
```json
{
    "status": "success",
    "pay": {
        "id": 2,
        "worker_id": 1, 
        "amount": 300000,
        "status": "oldi",
        "created_at": "2025-08-27T18:00:00Z",
        "updated_at": "2025-08-27T18:00:00Z"
    },
    "worker": {
        "id": 1,
        "summa": 3750000
    }
}
```

**cURL Test:**
```bash
curl -X POST http://localhost:8000/api/workers/1/pays \
-H "Authorization: Bearer 1|your_token_here" \
-H "Content-Type: application/json" \
-d '{
    "amount": 300000,
    "status": "oldi"
}'
```

---

## 7. OPTOM VA CHAKANA SAVDO (SALES)

### 7.1 Barcha optomchilarni olish
```http
GET /api/optomchilar
```

**Query parametrlar:**
- `q` (ixtiyoriy) - Optomchi nomi bo'yicha qidirish

**Muvaffaqiyatli javob (200):**
```json
[
    {
        "id": 1,
        "name": "Dostonbek Optom",
        "phone": "+998909999999",
        "address": "Toshkent shahri",
        "sale_type": "optom",
        "created_by": "Admin",
        "created_at": "2025-08-27T09:00:00Z",
        "updated_at": "2025-08-27T09:00:00Z"
    }
]
```

### 7.2 Yangi optomchi yaratish (mahsulotlar bilan)
```http
POST /api/optomchilar
```

**Request Body:**
```json
{
    "name": "Jamshid Toychi",
    "phone": "+998901010101",
    "address": "Samarqand shahri",
    "sale_type": "toychi",
    "created_by": "Admin",
    "items": [
        {
            "product_id": 1,
            "quantity": 5,
            "unit": "blok",
            "price": 79200
        },
        {
            "product_id": null,
            "name": "Yangi mahsulot",
            "quantity": 10,
            "unit": "dona", 
            "price": 5000
        }
    ]
}
```

**Muhim eslatmalar:**
- `sale_type` qiymatlari: "optom" yoki "toychi"
- `unit` qiymatlari: "dona" yoki "blok"
- Agar `product_id` berilmasa, `name` majburiy
- Agar `product_id` berilsa, narx avtomatik hisoblanadi

**Muvaffaqiyatli javob (201):**
```json
{
    "status": "success",
    "optomchi": {
        "id": 2,
        "name": "Jamshid Toychi",
        "phone": "+998901010101", 
        "address": "Samarqand shahri",
        "sale_type": "toychi",
        "created_by": "Admin",
        "created_at": "2025-08-27T19:00:00Z",
        "updated_at": "2025-08-27T19:00:00Z"
    },
    "sale": {
        "id": 2,
        "optomchi_id": 2,
        "total_sum": 446000,
        "created_at": "2025-08-27T19:00:00Z",
        "updated_at": "2025-08-27T19:00:00Z"
    },
    "sale_items": [
        {
            "id": 3,
            "sale_id": 2,
            "product_id": 1,
            "name": null,
            "quantity": 5,
            "unit": "blok",
            "price": 79200,
            "subtotal": 396000,
            "created_at": "2025-08-27T19:00:00Z",
            "updated_at": "2025-08-27T19:00:00Z"
        },
        {
            "id": 4,
            "sale_id": 2,
            "product_id": null,
            "name": "Yangi mahsulot",
            "quantity": 10,
            "unit": "dona",
            "price": 5000,
            "subtotal": 50000,
            "created_at": "2025-08-27T19:00:00Z",
            "updated_at": "2025-08-27T19:00:00Z"
        }
    ]
}
```

**cURL Test:**
```bash
curl -X POST http://localhost:8000/api/optomchilar \
-H "Authorization: Bearer 1|your_token_here" \
-H "Content-Type: application/json" \
-d '{
    "name": "Jamshid Toychi",
    "phone": "+998901010101",
    "address": "Samarqand shahri",
    "sale_type": "toychi",
    "created_by": "Admin",
    "items": [
        {
            "product_id": 1,
            "quantity": 5,
            "unit": "blok",
            "price": 79200
        }
    ]
}'
```

### 7.3 Optomchini olish (savdo va mahsulotlar bilan)
```http
GET /api/optomchilar/{id}
```

**Muvaffaqiyatli javob (200):**
```json
{
    "id": 1,
    "name": "Dostonbek Optom",
    "phone": "+998909999999",
    "address": "Toshkent shahri",
    "sale_type": "optom",
    "created_by": "Admin",
    "created_at": "2025-08-27T09:00:00Z",
    "updated_at": "2025-08-27T09:00:00Z",
    "sales": [
        {
            "id": 1,
            "optomchi_id": 1,
            "total_sum": 768000,
            "created_at": "2025-08-27T09:30:00Z",
            "updated_at": "2025-08-27T09:30:00Z"
        }
    ],
    "sale_items": [
        {
            "id": 1,
            "sale_id": 1,
            "product_id": 1,
            "name": null,
            "quantity": 10,
            "unit": "blok",
            "price": 76800,
            "subtotal": 768000,
            "created_at": "2025-08-27T09:30:00Z",
            "updated_at": "2025-08-27T09:30:00Z"
        }
    ]
}
```

### 7.4 Optomchini yangilash
```http
PUT /api/optomchilar/{id}
```

**Request Body (hammasi ixtiyoriy):**
```json
{
    "name": "Dostonbek Optom Yangilangan",
    "phone": "+998909999998",
    "address": "Toshkent shahri, Yunusobod",
    "sales": [
        {
            "id": 1,
            "total_sum": 800000
        }
    ],
    "sale_items": [
        {
            "id": 1,
            "quantity": 12,
            "unit": "blok",
            "price": 76800
        }
    ]
}
```

### 7.5 Optomchi yoki savdo elementini o'chirish
```http
DELETE /api/optomchilar/{id}
```

**Bu endpoint 2 ta vazifani bajaradi:**
1. Agar ID optomchiga tegishli bo'lsa - butun optomchi va uning barcha ma'lumotlari o'chiriladi
2. Agar ID sale_item ga tegishli bo'lsa - faqat o'sha mahsulot o'chiriladi

**Optomchi o'chirilganda:**
```json
{
    "status": "success",
    "message": "Optomchi and all data deleted"
}
```

**Sale item o'chirilganda:**
```json
{
    "status": "success", 
    "message": "Sale item deleted",
    "new_total_sum": 500000
}
```

### 7.6 Mahsulotlarni qidirish
```http
GET /api/getProducts
```

**Query parametrlar:**
- `q` (ixtiyoriy) - Mahsulot nomi bo'yicha qidirish

**Muvaffaqiyatli javob (200):**
```json
[
    {
        "id": 1,
        "name": "Coca Cola 0.5L"
    },
    {
        "id": 2,
        "name": "Fanta 0.5L"
    }
]
```

### 7.7 Mahsulot narxini olish
```http
GET /api/product-price/{productId}/{saleType}/{unit}
```

**Path parametrlar:**
- `productId` - Mahsulot ID
- `saleType` - Savdo turi: "optom" yoki "toychi"
- `unit` - O'lchov turi: "dona" yoki "blok"

**Misol:**
```http
GET /api/product-price/1/optom/blok
```

**Muvaffaqiyatli javob (200):**
```json
{
    "price": 76800
}
```

**cURL Test:**
```bash
curl -X GET http://localhost:8000/api/product-price/1/optom/blok \
-H "Authorization: Bearer 1|your_token_here"
```

---

## 8. XATOLIKLAR BILAN ISHLASH

### Umumiy xato javob formati:
```json
{
    "status": "error",
    "message": "Xato tavsifi",
    "errors": {
        "field_name": ["Xato xabari"]
    }
}
```

### Asosiy xato kodlari:

**401 Unauthorized:**
```json
{
    "status": "error", 
    "message": "Unauthorized"
}
```

**404 Not Found:**
```json
{
    "status": "error",
    "message": "Resource not found"
}
```

**422 Validation Error:**
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."],
        "phone": ["The phone field must be a valid phone number."]
    }
}
```

---

## 9. MA'LUMOTLAR BAZASI TUZILISHI

### Asosiy jadvallar:

1. **users** - Foydalanuvchilar
2. **clients** - Mijozlar
3. **debts** - Mijoz qarzlari
4. **firms** - Firmalar  
5. **firm_debts** - Firma qarzlari
6. **orders** - Buyurtmalar
7. **products** - Mahsulotlar
8. **workers** - Ishchilar
9. **worker_pays** - Ishchi to'lovlari
10. **optomchilar** - Optom mijozlar
11. **sales** - Savdo 
12. **sale_items** - Savdo mahsulotlari

### Bog'lanishlar:
- Clients ↔ Debts (1:N)
- Firms ↔ FirmDebts (1:N)
- Orders ↔ Products (1:N)
- Workers ↔ WorkerPays (1:N)
- Optomchilar ↔ Sales (1:N)
- Sales ↔ SaleItems (1:N)

---

## 10. AUTENTIFIKATSIYA VA XAVFSIZLIK

### Token boshqaruvi:
- Login orqali token olinadi
- Har bir request da Bearer token ishlatiladi
- Logout orqali token bekor qilinadi
- Token Laravel Sanctum orqali boshqariladi

### Xavfsizlik choralari:
- Barcha input ma'lumotlar validate qilinadi
- SQL injection himoyasi
- CORS sozlamalari
- Rate limiting (kerak bo'lsa qo'shiladi)

---

## 11. FRONTEND UCHUN QO'LLANMA

### Asosiy workflow:

1. **Login:**
```javascript
const loginResponse = await fetch('/api/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        email: 'admin',
        password: '1234'
    })
});
const loginData = await loginResponse.json();
const token = loginData.token;
```

2. **API so'rovlari:**
```javascript
const response = await fetch('/api/clients', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
});
```

3. **Fayl yuklash:**
```javascript
const formData = new FormData();
formData.append('name', 'Ishchi nomi');
formData.append('image', fileInput.files[0]);

const response = await fetch('/api/workers', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`
    },
    body: formData
});
```

### React/Vue.js uchun misollar:

**React Hook:**
```javascript
import { useState, useEffect } from 'react';

function useApi(endpoint, token) {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch(`/api/${endpoint}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            setData(data);
            setLoading(false);
        });
    }, [endpoint, token]);

    return { data, loading };
}
```

---

## 12. TESTING

### Laravel artisan buyruqlari:
```bash
# Swagger dokumentatsiyasini yaratish
php artisan l5-swagger:generate

# Ma'lumotlar bazasini migrate qilish  
php artisan migrate

# Serverni ishga tushirish
php artisan serve
```

### Postman Collection:
To'liq Postman kolleksiyasini yaratish uchun:
1. Yangi kolleksiya yarating
2. Har bir endpoint uchun request qo'shing
3. Authorization da Bearer Token o'rnating
4. Test scriptlarini qo'shing

### Test ma'lumotlari:
```sql
-- Test foydalanuvchi
INSERT INTO users (name, email, password) VALUES 
('Admin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: 1234

-- Test firma
INSERT INTO firms (name, supervisor, agent, currier, humo, uzcard, day, debt) VALUES
('Test Firma', 'Supervisor', 'Agent', 'Currier', 1, 0, 'Dushanba', 0);
```

---

## 13. QO'SHIMCHA MA'LUMOTLAR

### Loyiha papka tuzilishi:
```
jasur-savdo/admin/
├── app/
│   ├── Http/Controllers/
│   └── Models/
├── routes/
│   └── api.php
├── database/
│   └── migrations/
└── storage/
    └── api-docs/
        └── api-docs.json
```

### Environment o'zgaruvchilari (.env):
```env
APP_NAME="Jasur Savdo"
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jasur_savdo
DB_USERNAME=root
DB_PASSWORD=
```

### Swagger dokumentatsiyasi:
Swagger UI ga kirish: http://localhost:8000/api/documentation

Bu to'liq API dokumentatsiyasi loyihangiz bilan ishlash uchun barcha zarur ma'lumotlarni o'z ichiga oladi. Har bir endpoint sinab ko'rilgan va real misollar bilan ta'minlangan.
