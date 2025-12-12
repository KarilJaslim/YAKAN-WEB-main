# 🧪 Testing Checklist & Commands

## Prerequisites

Make sure you have:
- ✅ PHP server running on port 8000
- ✅ MySQL database running
- ✅ Laravel app configured
- ✅ All 6 files copied

---

## 🚀 START THE SERVER

### Option 1: PHP Built-in Server
```bash
cd C:\xampp\htdocs\YAKAN-WEB-main
php -S 127.0.0.1:8000 -t public
```

### Option 2: XAMPP Apache
1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Navigate to `http://localhost/YAKAN-WEB-main/public/api/v1/products`

### Verify Server Running
```bash
curl http://127.0.0.1:8000/api/v1/products
```
Should return JSON with products data.

---

## 📋 TEST CHECKLIST

### ✅ Test 1: Products Endpoint
**Command:**
```bash
curl http://127.0.0.1:8000/api/v1/products
```

**Expected Result:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 10,
        "name": "test1",
        "price": 75,
        "stock": 20
      }
      // ... more products
    ]
  }
}
```

**Pass Criteria:** ✅ Returns JSON with `"success": true`

---

### ✅ Test 2: Create Order
**Command:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d @test_order.json
```

**Expected Result:**
```json
{
  "success": true,
  "message": "Order created successfully. Admin will be notified.",
  "data": {
    "id": 1,
    "total_amount": 250,
    "status": "pending",
    "payment_status": "pending",
    "created_at": "2025-12-11T21:00:00Z"
  }
}
```

**Pass Criteria:** ✅ Returns `"success": true` with order data

**Save the Order ID from response for Test 3**

---

### ✅ Test 3: Get All Orders
**Command:**
```bash
curl http://127.0.0.1:8000/api/v1/orders
```

**Expected Result:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "total_amount": 250,
      "status": "pending",
      "payment_status": "pending"
    }
    // ... more orders
  ]
}
```

**Pass Criteria:** ✅ Returns list of orders

---

### ✅ Test 4: Get Specific Order
**Command:** (Replace `1` with actual order ID from Test 2)
```bash
curl http://127.0.0.1:8000/api/v1/orders/1
```

**Expected Result:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "total_amount": 250,
    "payment_method": "gcash",
    "delivery_address": "123 Test Street, Cotabato City",
    "items": [
      {
        "product_id": 10,
        "product_name": "test1",
        "quantity": 2,
        "price": 75,
        "total": 150
      }
    ]
  }
}
```

**Pass Criteria:** ✅ Returns order with items

---

### ✅ Test 5: Login (Get Auth Token)
**Command:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test123"}'
```

**Expected Result:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 4,
      "name": "Test User",
      "email": "test@example.com",
      "role": "user"
    },
    "token": "1|73uyUN4T1xS9AMfr3lIKTiAbEssn8oMJg81KN0cTa375fde5"
  }
}
```

**Pass Criteria:** ✅ Returns user data and token

**Save the token for Test 6**

---

### ✅ Test 6: Get Current User (Requires Auth)
**Command:** (Replace token with actual token from Test 5)
```bash
curl http://127.0.0.1:8000/api/v1/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected Result:**
```json
{
  "success": true,
  "data": {
    "id": 4,
    "name": "Test User",
    "email": "test@example.com",
    "role": "user"
  }
}
```

**Pass Criteria:** ✅ Returns authenticated user data

---

### ✅ Test 7: Update Order Status (Admin - Requires Auth)
**Command:** (Replace token and order ID)
```bash
curl -X PATCH http://127.0.0.1:8000/api/v1/orders/1/status \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"status":"processing"}'
```

**Expected Result:**
```json
{
  "success": true,
  "message": "Order status updated",
  "data": {
    "id": 1,
    "status": "processing"
  }
}
```

**Pass Criteria:** ✅ Order status updated successfully

---

## 🧪 Automated Testing

### Run PowerShell Test Script
```bash
powershell -File test_api.ps1
```

This will automatically test all endpoints and show results.

---

## 📊 Results Summary

| Test # | Endpoint | Method | Auth | Status |
|--------|----------|--------|------|--------|
| 1 | `/products` | GET | No | ✅ |
| 2 | `/orders` | POST | No | ✅ |
| 3 | `/orders` | GET | No | ✅ |
| 4 | `/orders/{id}` | GET | No | ✅ |
| 5 | `/login` | POST | No | ✅ |
| 6 | `/user` | GET | Yes | ✅ |
| 7 | `/orders/{id}/status` | PATCH | Yes | ✅ |

---

## ❌ Troubleshooting

### Error: Connection refused
**Issue:** Server not running
**Solution:** 
```bash
cd C:\xampp\htdocs\YAKAN-WEB-main
php -S 127.0.0.1:8000 -t public
```

### Error: Table 'orders' doesn't exist
**Issue:** Database not migrated
**Solution:**
```bash
php artisan migrate
```

### Error: Invalid credentials
**Issue:** Test user doesn't exist
**Solution:**
```bash
php artisan tinker
> User::create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => bcrypt('test123')])
> exit
```

### Error: JSON parse error
**Issue:** API not returning valid JSON
**Solution:**
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database connection in `.env`
3. Clear cache: `php artisan cache:clear`

### Error: 422 Validation Error
**Issue:** Missing required fields
**Solution:** Check request body matches expected format in test commands

---

## 🎯 Success Indicators

✅ **All tests passing means:**
- API is working correctly
- Database tables created
- Routes configured
- Controllers responding
- Authentication working
- Orders being saved

---

## 🚀 Next Steps After Testing

### If All Tests Pass:
1. ✅ Set up WebSocket for real-time notifications
2. ✅ Configure email notifications
3. ✅ Integrate React Native app
4. ✅ Set up payment gateway
5. ✅ Deploy to production

### Recommended Order:
1. **WebSocket Setup** (for real-time notifications)
2. **Email Notifications** (order confirmations)
3. **React Native Integration** (mobile app)
4. **Payment Gateway** (GCash, Bank Transfer)

---

## 📝 Notes

- Test user credentials: `test@example.com` / `test123`
- Test order JSON: `test_order.json` in project root
- All APIs return JSON format
- Errors include detailed error messages in `message` field
- Check `storage/logs/laravel.log` for debugging

---

## ✨ You're Ready!

Once all tests pass, your order notification system is fully operational and ready for production use!
