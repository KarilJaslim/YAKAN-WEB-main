# Ngrok Setup Instructions

## Quick Setup at School

Copy and paste this prompt to the agent:

```
Please help me setup ngrok for my Laravel backend:

1. Start the Laravel server on port 8000 in YAKAN-WEB
2. Start ngrok tunnel on port 8000
3. Get the ngrok URL and update the mobile app config

The Laravel backend is in: C:\xampp\htdocs\YAKAN-WEB
The mobile app is in: C:\xampp\htdocs\YAKAN-main-main
The config file is: src/config/config.js
```

---

## Manual Steps (if needed)

### Terminal 1 - Start Laravel Backend
```powershell
cd C:\xampp\htdocs\YAKAN-WEB
php artisan serve --host=0.0.0.0 --port=8000
```

### Terminal 2 - Start Ngrok
```powershell
ngrok http 127.0.0.1:8000
```

### Terminal 3 - Update Mobile Config
1. Copy the ngrok URL (looks like: `https://xxxx-xxxx-xxxx.ngrok-free.app`)
2. Edit `C:\xampp\htdocs\YAKAN-main-main\src\config\config.js`
3. Change the `API_URL` to your ngrok URL + `/api/v1`
   ```javascript
   API_URL: 'https://your-ngrok-url.ngrok-free.app/api/v1'
   ```

### Terminal 4 - Start Mobile App
```powershell
cd C:\xampp\htdocs\YAKAN-main-main
npm start
```

---

## What's Already Fixed

✅ Orders routes are now public (no authentication needed)
✅ Price display crashes fixed (parseFloat added)
✅ Order timeline polling works (updates every 8 seconds)
✅ All database migrations completed
✅ Order creation with tracking numbers and references

---

## Testing After Setup

1. Open mobile app on your phone via Expo Go
2. Add a product to cart
3. Go to checkout
4. Submit order
5. Check OrderDetails screen - timeline should update automatically
6. Check admin dashboard at: http://localhost:8000/admin/orders (or ngrok URL)
