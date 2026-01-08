#!/bin/bash

# Quick Stripe Setup Script
# This script creates products and prices in Stripe using Stripe CLI

echo "🚀 Creating Stripe products for NotifyHub..."
echo ""

# Check if Stripe CLI is installed
if ! command -v stripe &> /dev/null; then
    echo "❌ Stripe CLI not found. Install it first:"
    echo "   brew install stripe/stripe-cli/stripe"
    exit 1
fi

# Check if jq is installed
if ! command -v jq &> /dev/null; then
    echo "❌ jq not found. Install it first:"
    echo "   brew install jq"
    exit 1
fi

echo "✅ Dependencies OK (stripe, jq)"
echo ""

# Make sure you're in test mode!
echo "⚠️  Make sure you're logged into Stripe CLI in TEST MODE"
echo "Run: stripe login"
echo ""
read -p "Press enter to continue..."
echo ""

# Create Starter plan (monthly + yearly)
echo "Creating Starter plan..."
echo "Running: stripe products create --name=\"Starter\" ..."

STARTER_PRODUCT=$(stripe products create \
  --name="Starter" \
  --description="For small teams - 10k events/month" \
  -o json 2>&1)

RESULT=$?
if [ $RESULT -ne 0 ]; then
    echo "❌ Stripe command failed with exit code $RESULT"
    echo "Output: $STARTER_PRODUCT"
    exit 1
fi

if ! echo "$STARTER_PRODUCT" | jq -e . >/dev/null 2>&1; then
    echo "❌ Failed to create Starter product (invalid JSON). Output:"
    echo "$STARTER_PRODUCT"
    exit 1
fi

STARTER_PRODUCT_ID=$(echo "$STARTER_PRODUCT" | jq -r '.id')
echo "   Product ID: $STARTER_PRODUCT_ID"

# Monthly: $19/mo
echo "   Creating monthly price ($19/mo)..."
STARTER_PRICE_MONTHLY=$(stripe prices create \
  --product="$STARTER_PRODUCT_ID" \
  --currency=usd \
  --unit-amount=1900 \
  --recurring[interval]=month \
  -o json 2>&1)

if ! echo "$STARTER_PRICE_MONTHLY" | jq -e . >/dev/null 2>&1; then
    echo "❌ Failed to create Starter monthly price. Output:"
    echo "$STARTER_PRICE_MONTHLY"
    exit 1
fi

STARTER_PRICE_MONTHLY_ID=$(echo "$STARTER_PRICE_MONTHLY" | jq -r '.id')
echo "   ✅ Monthly: $STARTER_PRICE_MONTHLY_ID"

# Yearly: $15/mo = $180/year (20% off)
echo "   Creating yearly price ($180/year)..."
STARTER_PRICE_YEARLY=$(stripe prices create \
  --product="$STARTER_PRODUCT_ID" \
  --currency=usd \
  --unit-amount=18000 \
  --recurring[interval]=year \
  -o json 2>&1)

if ! echo "$STARTER_PRICE_YEARLY" | jq -e . >/dev/null 2>&1; then
    echo "❌ Failed to create Starter yearly price. Output:"
    echo "$STARTER_PRICE_YEARLY"
    exit 1
fi

STARTER_PRICE_YEARLY_ID=$(echo "$STARTER_PRICE_YEARLY" | jq -r '.id')
echo "   ✅ Yearly: $STARTER_PRICE_YEARLY_ID"
echo ""

# Create Pro plan (monthly + yearly)
echo "Creating Pro plan..."
PRO_PRODUCT=$(stripe products create \
  --name="Pro" \
  --description="For growing businesses - 100k events/month" \
  -o json 2>&1)

if ! echo "$PRO_PRODUCT" | jq -e . >/dev/null 2>&1; then
    echo "❌ Failed to create Pro product. Output:"
    echo "$PRO_PRODUCT"
    exit 1
fi

PRO_PRODUCT_ID=$(echo "$PRO_PRODUCT" | jq -r '.id')
echo "   Product ID: $PRO_PRODUCT_ID"

# Monthly: $49/mo
echo "   Creating monthly price ($49/mo)..."
PRO_PRICE_MONTHLY=$(stripe prices create \
  --product="$PRO_PRODUCT_ID" \
  --currency=usd \
  --unit-amount=4900 \
  --recurring[interval]=month \
  -o json 2>&1)

if ! echo "$PRO_PRICE_MONTHLY" | jq -e . >/dev/null 2>&1; then
    echo "❌ Failed to create Pro monthly price. Output:"
    echo "$PRO_PRICE_MONTHLY"
    exit 1
fi

PRO_PRICE_MONTHLY_ID=$(echo "$PRO_PRICE_MONTHLY" | jq -r '.id')
echo "   ✅ Monthly: $PRO_PRICE_MONTHLY_ID"

# Yearly: $39/mo = $468/year (20% off)
echo "   Creating yearly price ($468/year)..."
PRO_PRICE_YEARLY=$(stripe prices create \
  --product="$PRO_PRODUCT_ID" \
  --currency=usd \
  --unit-amount=46800 \
  --recurring[interval]=year \
  -o json 2>&1)

if ! echo "$PRO_PRICE_YEARLY" | jq -e . >/dev/null 2>&1; then
    echo "❌ Failed to create Pro yearly price. Output:"
    echo "$PRO_PRICE_YEARLY"
    exit 1
fi

PRO_PRICE_YEARLY_ID=$(echo "$PRO_PRICE_YEARLY" | jq -r '.id')
echo "   ✅ Yearly: $PRO_PRICE_YEARLY_ID"
echo ""

# Create Early Bird coupons
echo "Creating Early Bird coupons..."

# Check if coupon exists first
EXISTING_STARTER=$(stripe coupons retrieve EARLYBIRD_STARTER -o json 2>&1 || echo "null")
if echo "$EXISTING_STARTER" | jq -e '.id' >/dev/null 2>&1; then
    echo "   ⚠️  EARLYBIRD_STARTER already exists, skipping..."
else
    STARTER_COUPON=$(stripe coupons create \
      --id="EARLYBIRD_STARTER" \
      --percent-off=52.63 \
      --duration=forever \
      --max-redemptions=15 \
      --name="Early Bird Starter" \
      -o json 2>&1)

    if ! echo "$STARTER_COUPON" | jq -e . >/dev/null 2>&1; then
        echo "❌ Failed to create EARLYBIRD_STARTER coupon. Output:"
        echo "$STARTER_COUPON"
    else
        echo "   ✅ Early Bird Starter coupon created (52.63% off = $9/mo)"
    fi
fi

EXISTING_PRO=$(stripe coupons retrieve EARLYBIRD_PRO -o json 2>&1 || echo "null")
if echo "$EXISTING_PRO" | jq -e '.id' >/dev/null 2>&1; then
    echo "   ⚠️  EARLYBIRD_PRO already exists, skipping..."
else
    PRO_COUPON=$(stripe coupons create \
      --id="EARLYBIRD_PRO" \
      --percent-off=40.82 \
      --duration=forever \
      --max-redemptions=20 \
      --name="Early Bird Pro" \
      -o json 2>&1)

    if ! echo "$PRO_COUPON" | jq -e . >/dev/null 2>&1; then
        echo "❌ Failed to create EARLYBIRD_PRO coupon. Output:"
        echo "$PRO_COUPON"
    else
        echo "   ✅ Early Bird Pro coupon created (40.82% off = $29/mo)"
    fi
fi

# Output .env variables
echo ""
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
