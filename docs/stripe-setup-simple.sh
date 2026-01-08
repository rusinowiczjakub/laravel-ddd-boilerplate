#!/bin/bash

# Simple Stripe Setup Script
# This script creates products and prices in Stripe using Stripe CLI
# No JSON parsing required - uses grep/awk instead

echo "🚀 Creating Stripe products for NotifyHub..."
echo ""

# Check if Stripe CLI is installed
if ! command -v stripe &> /dev/null; then
    echo "❌ Stripe CLI not found. Install it first:"
    echo "   brew install stripe/stripe-cli/stripe"
    exit 1
fi

echo "✅ Stripe CLI found (version $(stripe --version))"
echo ""

# Make sure you're in test mode!
echo "⚠️  Make sure you're logged into Stripe CLI in TEST MODE"
echo "Run: stripe login"
echo ""
read -p "Press enter to continue..."
echo ""

# Create Starter plan
echo "Creating Starter plan..."
stripe products create \
  --name="Starter" \
  --description="For small teams - 10k events/month" | tee /tmp/starter_product.txt

STARTER_PRODUCT_ID=$(grep -o 'prod_[a-zA-Z0-9]*' /tmp/starter_product.txt | head -1)
echo "   Product ID: $STARTER_PRODUCT_ID"
echo ""

if [ -z "$STARTER_PRODUCT_ID" ]; then
    echo "❌ Failed to get Starter product ID"
    exit 1
fi

# Monthly: $19/mo
echo "   Creating monthly price ($19/mo)..."
stripe prices create \
  --product="$STARTER_PRODUCT_ID" \
  --currency=usd \
  --unit-amount=1900 \
  --recurring.interval=month | tee /tmp/starter_price_monthly.txt

STARTER_PRICE_MONTHLY_ID=$(grep -o 'price_[a-zA-Z0-9]*' /tmp/starter_price_monthly.txt | head -1)
echo "   ✅ Monthly: $STARTER_PRICE_MONTHLY_ID"

# Yearly: $180/year
echo "   Creating yearly price ($180/year)..."
stripe prices create \
  --product="$STARTER_PRODUCT_ID" \
  --currency=usd \
  --unit-amount=18000 \
  --recurring.interval=year | tee /tmp/starter_price_yearly.txt

STARTER_PRICE_YEARLY_ID=$(grep -o 'price_[a-zA-Z0-9]*' /tmp/starter_price_yearly.txt | head -1)
echo "   ✅ Yearly: $STARTER_PRICE_YEARLY_ID"
echo ""

# Create Pro plan
echo "Creating Pro plan..."
stripe products create \
  --name="Pro" \
  --description="For growing businesses - 100k events/month" | tee /tmp/pro_product.txt

PRO_PRODUCT_ID=$(grep -o 'prod_[a-zA-Z0-9]*' /tmp/pro_product.txt | head -1)
echo "   Product ID: $PRO_PRODUCT_ID"
echo ""

if [ -z "$PRO_PRODUCT_ID" ]; then
    echo "❌ Failed to get Pro product ID"
    exit 1
fi

# Monthly: $49/mo
echo "   Creating monthly price ($49/mo)..."
stripe prices create \
  --product="$PRO_PRODUCT_ID" \
  --currency=usd \
  --unit-amount=4900 \
  --recurring.interval=month | tee /tmp/pro_price_monthly.txt

PRO_PRICE_MONTHLY_ID=$(grep -o 'price_[a-zA-Z0-9]*' /tmp/pro_price_monthly.txt | head -1)
echo "   ✅ Monthly: $PRO_PRICE_MONTHLY_ID"

# Yearly: $468/year
echo "   Creating yearly price ($468/year)..."
stripe prices create \
  --product="$PRO_PRODUCT_ID" \
  --currency=usd \
  --unit-amount=46800 \
  --recurring.interval=year | tee /tmp/pro_price_yearly.txt

PRO_PRICE_YEARLY_ID=$(grep -o 'price_[a-zA-Z0-9]*' /tmp/pro_price_yearly.txt | head -1)
echo "   ✅ Yearly: $PRO_PRICE_YEARLY_ID"
echo ""

# Create Early Bird coupons
echo "Creating Early Bird coupons..."

echo "   Creating EARLYBIRD_STARTER coupon..."
stripe coupons create \
  --id="EARLYBIRD_STARTER" \
  --percent-off=52.63 \
  --duration=forever \
  --max-redemptions=15 \
  --name="Early Bird Starter" 2>&1 | grep -q "already exists" && echo "   ⚠️  Already exists" || echo "   ✅ Created"

echo "   Creating EARLYBIRD_PRO coupon..."
stripe coupons create \
  --id="EARLYBIRD_PRO" \
  --percent-off=40.82 \
  --duration=forever \
  --max-redemptions=20 \
  --name="Early Bird Pro" 2>&1 | grep -q "already exists" && echo "   ⚠️  Already exists" || echo "   ✅ Created"

echo ""

# Clean up temp files
rm -f /tmp/starter_product.txt /tmp/starter_price_monthly.txt /tmp/starter_price_yearly.txt
rm -f /tmp/pro_product.txt /tmp/pro_price_monthly.txt /tmp/pro_price_yearly.txt

# Output .env variables
echo "========================================="
echo "✅ All done! Add these to your .env file:"
echo "========================================="
echo ""
echo "# Monthly prices"
echo "STRIPE_PRICE_STARTER=$STARTER_PRICE_MONTHLY_ID"
echo "STRIPE_PRICE_PRO=$PRO_PRICE_MONTHLY_ID"
echo ""
echo "# Yearly prices"
echo "STRIPE_PRICE_STARTER_YEARLY=$STARTER_PRICE_YEARLY_ID"
echo "STRIPE_PRICE_PRO_YEARLY=$PRO_PRICE_YEARLY_ID"
echo ""
echo "🎉 Don't forget to add your Stripe keys:"
echo "STRIPE_KEY=pk_test_..."
echo "STRIPE_SECRET=sk_test_..."
echo ""
