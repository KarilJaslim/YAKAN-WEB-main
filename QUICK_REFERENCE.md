# 🚀 Quick Reference - Order System Commands

## 📌 Server Management

### Start PHP Development Server
```bash
cd C:\xampp\htdocs\YAKAN-WEB-main
php -S 127.0.0.1:8000 -t public
```
**Result:** API accessible at `http://127.0.0.1:8000/api/v1`

### Stop Server
```
Ctrl+C in terminal
```

---

## 📱 API Testing Commands

### 1. Test Products (Verify API Working)
```bash
curl http://127.0.0.1:8000/api/v1/products -s | ConvertFrom-Json
```

### 2. Create Order
```bash
curl -X POST http://127.0.0.1:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d "@test_order.json"
```

### 3. Get All Orders
```bash
curl http://127.0.0.1:8000/api/v1/orders
```

### 4. Get Specific Order
```bash
curl http://127.0.0.1:8000/api/v1/orders/1
```

### 5. Update Order Status (Requires Auth Token)
```bash
curl -X PATCH http://127.0.0.1:8000/api/v1/orders/1/status \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status":"processing"}'
```

---

## 🗄️ Database Commands

### Check Orders Table Exists
```bash
php artisan tinker
> Schema::hasTable('orders')
> exit
```

### View Orders
```bash
php artisan tinker
> Order::all()
> exit
```

### View Specific Order
```bash
php artisan tinker
> Order::with('items')->find(1)
> exit
```

### Clear Database (⚠️ CAUTION)
```bash
php artisan migrate:refresh
```

---

## 📋 File Locations

**Copied Files:**
- `app/Models/Order.php`
- `app/Models/OrderItem.php`
- `app/Http/Controllers/OrderController.php`
- `app/Events/OrderCreated.php`
- `app/Events/OrderStatusChanged.php`
- `database/migrations/2024_12_11_create_orders_table.php`

**Updated Files:**
- `routes/api.php` - Added order routes

**Documentation:**
- `ORDER_SYSTEM_COMPLETE.md` - Full documentation
- `SETUP_COMPLETE.md` - Setup guide
- `COPY_FILES_GUIDE.md` - Files copied reference
- `QUICK_REFERENCE.md` - This file

**Test Files:**
- `test_order.json` - Sample order for testing

---

## 🧪 Test Order JSON

File: `test_order.json`

```json
{
  "total_amount": 250,
  "payment_method": "gcash",
  "delivery_type": "delivery",
  "delivery_address": "123 Test Street, Cotabato City",
  "customer_notes": "Please deliver in the morning",
  "items": [
    {
      "product_id": 10,
      "quantity": 2
    }
  ]
}
```

**Note:** Price is auto-calculated from product data

---

## 🔗 API Endpoints

### Public (No Auth Required)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/products` | List all products |
| GET | `/api/v1/products/{id}` | Get product details |
| GET | `/api/v1/products/search?q=...` | Search products |
| POST | `/api/v1/orders` | Create order |
| GET | `/api/v1/orders` | Get orders list |
| GET | `/api/v1/orders/{id}` | Get order details |

### Protected (Requires Auth Token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/login` | User login |
| POST | `/api/v1/logout` | User logout |
| GET | `/api/v1/user` | Get current user |
| PATCH | `/api/v1/orders/{id}/status` | Update order status |
| GET | `/api/v1/admin/orders` | Get all orders (admin) |

---

## 🔍 Debugging

### View Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Run Artisan Commands
```bash
php artisan command:name

# Examples:
php artisan migrate              # Run migrations
php artisan tinker              # Interactive shell
php artisan make:model ModelName # Create new model
```

### Check Database
```bash
mysql -u root -p yakan
SELECT * FROM orders;
SELECT * FROM order_items;
```

---

## ✅ Checklist for Production

- [ ] Server running and API responding
- [ ] Products endpoint returns data
- [ ] Order creation works
- [ ] Database stores orders correctly
- [ ] Events firing (check logs)
- [ ] WebSocket server configured
- [ ] Email notifications setup
- [ ] React Native app integrated
- [ ] Admin dashboard connected
- [ ] Payment gateway integrated

---

## 💡 Tips & Tricks

### Quick API Test
```bash
# Test in PowerShell
$payload = @{
    total_amount = 250
    payment_method = "gcash"
    delivery_type = "delivery"
    delivery_address = "123 Test St"
    items = @(@{ product_id = 10; quantity = 2 })
} | ConvertTo-Json

Invoke-WebRequest -Uri http://127.0.0.1:8000/api/v1/orders `
  -Method POST `
  -Headers @{'Content-Type'='application/json'} `
  -Body $payload
```

### Monitor Requests in Real-time
```bash
# In another terminal
while($true) { 
  Get-Content storage/logs/laravel.log -Tail 5
  Start-Sleep -Seconds 1
}
```

### Check Server Status
```bash
netstat -an | findstr 8000
```

---

## 🚀 Production Deployment

### Before Going Live
1. Set up environment variables (.env)
2. Configure WebSocket server
3. Set up SSL/TLS certificate
4. Configure payment gateway
5. Set up email notifications
6. Test entire workflow
7. Back up database
8. Monitor logs

### Deployment Steps
```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Run migrations
php artisan migrate --force

# 4. Clear caches
php artisan optimize

# 5. Start server (production)
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 📞 Support

**Issues?** Check:
1. `storage/logs/laravel.log` for errors
2. Database connection in `.env`
3. Server is running on correct port
4. API routes are configured
5. Models and migrations are in place

**All files documented in:** `ORDER_SYSTEM_COMPLETE.md`
