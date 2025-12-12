#!/usr/bin/env pwsh
# API Testing Script for Order Notification System
# Run with: powershell -File test_api.ps1

Write-Host "==========================================" -ForegroundColor Green
Write-Host "🧪 YAKAN ORDER SYSTEM - API TESTING" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""

$API_BASE = "http://127.0.0.1:8000/api/v1"

# Test 1: Products
Write-Host "1️⃣ Testing Products Endpoint..." -ForegroundColor Yellow
try {
    $products = Invoke-WebRequest -Uri "$API_BASE/products" -UseBasicParsing | ConvertFrom-Json
    if ($products.success) {
        Write-Host "✅ Products endpoint working" -ForegroundColor Green
        $product = $products.data.data[0]
        Write-Host "   Sample: $($product.name) - ₱$($product.price)" -ForegroundColor Gray
    }
} catch {
    Write-Host "❌ Products endpoint failed: $_" -ForegroundColor Red
}
Write-Host ""

# Test 2: Create Order
Write-Host "2️⃣ Testing Order Creation..." -ForegroundColor Yellow
try {
    $body = @{
        total_amount = 250
        payment_method = "gcash"
        delivery_type = "delivery"
        delivery_address = "123 Test Street, Cotabato City"
        customer_notes = "Test order"
        items = @(
            @{ product_id = 10; quantity = 2 }
        )
    } | ConvertTo-Json
    
    $order = Invoke-WebRequest -Uri "$API_BASE/orders" -Method POST -ContentType "application/json" -Body $body -UseBasicParsing | ConvertFrom-Json
    if ($order.success) {
        Write-Host "✅ Order created successfully" -ForegroundColor Green
        $orderId = $order.data.id
        Write-Host "   Order ID: $orderId" -ForegroundColor Gray
    } else {
        Write-Host "❌ Order creation failed: $($order.message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Order creation error: $_" -ForegroundColor Red
}
Write-Host ""

# Test 3: Get Orders
Write-Host "3️⃣ Testing Get Orders..." -ForegroundColor Yellow
try {
    $orders = Invoke-WebRequest -Uri "$API_BASE/orders" -UseBasicParsing | ConvertFrom-Json
    if ($orders.success) {
        Write-Host "✅ Get orders working" -ForegroundColor Green
        $count = $orders.data.Count
        Write-Host "   Total orders: $count" -ForegroundColor Gray
    }
} catch {
    Write-Host "❌ Get orders failed: $_" -ForegroundColor Red
}
Write-Host ""

# Test 4: Get Specific Order
if ($orderId) {
    Write-Host "4️⃣ Testing Get Specific Order (ID: $orderId)..." -ForegroundColor Yellow
    try {
        $order = Invoke-WebRequest -Uri "$API_BASE/orders/$orderId" -UseBasicParsing | ConvertFrom-Json
        if ($order.success) {
            Write-Host "✅ Get order details working" -ForegroundColor Green
            Write-Host "   Amount: ₱$($order.data.total_amount)" -ForegroundColor Gray
        }
    } catch {
        Write-Host "❌ Get order details failed: $_" -ForegroundColor Red
    }
    Write-Host ""
}

# Test 5: Login
Write-Host "5️⃣ Testing Login..." -ForegroundColor Yellow
try {
    $body = @{
        email = "test@example.com"
        password = "test123"
    } | ConvertTo-Json
    
    $login = Invoke-WebRequest -Uri "$API_BASE/login" -Method POST -ContentType "application/json" -Body $body -UseBasicParsing | ConvertFrom-Json
    if ($login.success) {
        Write-Host "✅ Login working" -ForegroundColor Green
        $token = $login.data.token
        Write-Host "   User: $($login.data.user.name)" -ForegroundColor Gray
        Write-Host "   Token: $($token.Substring(0, 30))..." -ForegroundColor Gray
    }
} catch {
    Write-Host "❌ Login failed: $_" -ForegroundColor Red
}
Write-Host ""

# Summary
Write-Host "==========================================" -ForegroundColor Green
Write-Host "✅ API TESTING COMPLETE" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""
Write-Host "✨ All endpoints tested!" -ForegroundColor Green
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  1. Set up WebSocket for real-time notifications"
Write-Host "  2. Configure payment gateway integration"
Write-Host "  3. Integrate React Native app"
Write-Host "  4. Set up email notifications"
