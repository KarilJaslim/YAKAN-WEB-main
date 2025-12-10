#!/bin/bash

# Storage Sync Script for Yakan E-Commerce
# This script copies files from storage/app/public to public/storage
# Run this after file uploads to make them accessible via web

echo "============================================"
echo "Yakan E-Commerce Storage Sync"
echo "============================================"
echo ""

cd "$(dirname "$0")"

echo "Syncing custom_orders..."
mkdir -p public/storage/custom_orders
cp -rf storage/app/public/custom_orders/* public/storage/custom_orders/ 2>/dev/null || echo "No custom_orders files to sync"

echo "Syncing payment_receipts..."
mkdir -p public/storage/payment_receipts
cp -rf storage/app/public/payment_receipts/* public/storage/payment_receipts/ 2>/dev/null || echo "No payment_receipts files to sync"

echo "Syncing products..."
mkdir -p public/storage/products
cp -rf storage/app/public/products/* public/storage/products/ 2>/dev/null || echo "No products files to sync"

echo ""
echo "============================================"
echo "Sync completed successfully!"
echo "============================================"
echo ""
echo "Files are now accessible at:"
echo "- http://127.0.0.1:8000/storage/payment_receipts/"
echo "- http://127.0.0.1:8000/storage/custom_orders/"
echo "- http://127.0.0.1:8000/storage/products/"
echo ""
