<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Workflows\Infrastructure\Models\WorkflowTemplateModel;

final class WorkflowTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Abandoned Cart',
                'description' => 'Send reminder emails when users add items to cart but don\'t complete purchase',
                'category' => 'ecommerce',
                'trigger_event' => 'cart.created',
                'correlation_field' => 'user_id',
                'is_featured' => true,
                'steps' => [
                    [
                        'id' => '00000000-0000-4000-8000-000000000001',
                        'type' => 'wait_for_event',
                        'label' => 'Wait for Purchase',
                        'events' => [['event' => 'order.created', 'next' => null]],
                        'timeout' => '30m',
                        'onTimeout' => '00000000-0000-4000-8000-000000000002',
                    ],
                    [
                        'id' => '00000000-0000-4000-8000-000000000002',
                        'type' => 'channel',
                        'label' => 'Send Reminder',
                        'channel' => 'email',
                        'recipient' => '{{email}}',
                        'next' => null,
                    ],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Welcome Series',
                'description' => 'Onboarding email sequence for new users over 7 days',
                'category' => 'onboarding',
                'trigger_event' => 'user.registered',
                'correlation_field' => null,
                'is_featured' => true,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000011', 'type' => 'channel', 'label' => 'Welcome Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000012'],
                    ['id' => '00000000-0000-4000-8000-000000000012', 'type' => 'delay', 'label' => 'Wait 2 Days', 'duration' => '2d', 'next' => '00000000-0000-4000-8000-000000000013'],
                    ['id' => '00000000-0000-4000-8000-000000000013', 'type' => 'channel', 'label' => 'Tips Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000014'],
                    ['id' => '00000000-0000-4000-8000-000000000014', 'type' => 'delay', 'label' => 'Wait 5 Days', 'duration' => '5d', 'next' => '00000000-0000-4000-8000-000000000015'],
                    ['id' => '00000000-0000-4000-8000-000000000015', 'type' => 'channel', 'label' => 'Check-in Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Order Confirmation',
                'description' => 'Send order confirmation via email and SMS for high-value orders',
                'category' => 'transactional',
                'trigger_event' => 'order.paid',
                'correlation_field' => null,
                'is_featured' => true,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000021', 'type' => 'channel', 'label' => 'Confirmation Email', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => '00000000-0000-4000-8000-000000000022'],
                    ['id' => '00000000-0000-4000-8000-000000000022', 'type' => 'condition', 'label' => 'High Value?', 'logic' => 'all', 'conditions' => [['field' => 'data.total', 'operator' => 'gt', 'value' => 500]], 'on_true' => '00000000-0000-4000-8000-000000000023', 'on_false' => null],
                    ['id' => '00000000-0000-4000-8000-000000000023', 'type' => 'channel', 'label' => 'VIP SMS', 'channel' => 'sms', 'recipient' => '{{customer_phone}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Payment Retry',
                'description' => 'Notify users when their payment fails and wait for successful retry',
                'category' => 'transactional',
                'trigger_event' => 'payment.failed',
                'correlation_field' => 'data.subscription_id',
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000031', 'type' => 'channel', 'label' => 'Payment Failed Email', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => '00000000-0000-4000-8000-000000000032'],
                    ['id' => '00000000-0000-4000-8000-000000000032', 'type' => 'wait_for_event', 'label' => 'Wait for Retry', 'events' => [['event' => 'payment.succeeded', 'next' => '00000000-0000-4000-8000-000000000033']], 'timeout' => '3d', 'onTimeout' => '00000000-0000-4000-8000-000000000034'],
                    ['id' => '00000000-0000-4000-8000-000000000033', 'type' => 'channel', 'label' => 'Payment Success', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                    ['id' => '00000000-0000-4000-8000-000000000034', 'type' => 'channel', 'label' => 'Final Warning', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Re-engagement Campaign',
                'description' => 'Re-engage inactive users with personalized offers',
                'category' => 'engagement',
                'trigger_event' => 'user.inactive',
                'correlation_field' => 'user_id',
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000041', 'type' => 'channel', 'label' => 'Miss You Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000042'],
                    ['id' => '00000000-0000-4000-8000-000000000042', 'type' => 'wait_for_event', 'label' => 'Wait for Activity', 'events' => [['event' => 'user.active', 'next' => null]], 'timeout' => '7d', 'onTimeout' => '00000000-0000-4000-8000-000000000043'],
                    ['id' => '00000000-0000-4000-8000-000000000043', 'type' => 'channel', 'label' => 'Special Offer', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Trial Expiry Reminder',
                'description' => 'Remind users when their trial is about to expire and convert to paid',
                'category' => 'engagement',
                'trigger_event' => 'trial.expiring_soon',
                'correlation_field' => 'data.user_id',
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000051', 'type' => 'channel', 'label' => 'Trial Expiring Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000052'],
                    ['id' => '00000000-0000-4000-8000-000000000052', 'type' => 'wait_for_event', 'label' => 'Wait for Upgrade', 'events' => [['event' => 'subscription.created', 'next' => '00000000-0000-4000-8000-000000000053']], 'timeout' => '3d', 'onTimeout' => '00000000-0000-4000-8000-000000000054'],
                    ['id' => '00000000-0000-4000-8000-000000000053', 'type' => 'channel', 'label' => 'Thank You', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                    ['id' => '00000000-0000-4000-8000-000000000054', 'type' => 'channel', 'label' => 'Final Offer', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],

            // ============ TRANSACTIONAL TEMPLATES ============

            [
                'id' => Str::uuid()->toString(),
                'name' => 'Email Verification',
                'description' => 'Send verification email when user registers or changes email address',
                'category' => 'transactional',
                'trigger_event' => 'user.email_verification_requested',
                'correlation_field' => null,
                'is_featured' => true,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000061', 'type' => 'channel', 'label' => 'Verification Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Password Reset',
                'description' => 'Send password reset link when user requests it',
                'category' => 'transactional',
                'trigger_event' => 'user.password_reset_requested',
                'correlation_field' => null,
                'is_featured' => true,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000071', 'type' => 'channel', 'label' => 'Reset Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Invoice & Receipt',
                'description' => 'Send invoice/receipt after successful payment',
                'category' => 'transactional',
                'trigger_event' => 'payment.completed',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000081', 'type' => 'channel', 'label' => 'Invoice Email', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Shipping Notification',
                'description' => 'Notify customer when their order has been shipped with tracking info',
                'category' => 'transactional',
                'trigger_event' => 'order.shipped',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000091', 'type' => 'channel', 'label' => 'Shipping Email', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => '00000000-0000-4000-8000-000000000092'],
                    ['id' => '00000000-0000-4000-8000-000000000092', 'type' => 'channel', 'label' => 'SMS Tracking', 'channel' => 'sms', 'recipient' => '{{customer_phone}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Order Delivered',
                'description' => 'Confirm delivery and ask for review',
                'category' => 'transactional',
                'trigger_event' => 'order.delivered',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000101', 'type' => 'channel', 'label' => 'Delivery Confirmation', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => '00000000-0000-4000-8000-000000000102'],
                    ['id' => '00000000-0000-4000-8000-000000000102', 'type' => 'delay', 'label' => 'Wait 3 Days', 'duration' => '3d', 'next' => '00000000-0000-4000-8000-000000000103'],
                    ['id' => '00000000-0000-4000-8000-000000000103', 'type' => 'channel', 'label' => 'Ask for Review', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'New Device Login Alert',
                'description' => 'Alert user when login from new device or location is detected',
                'category' => 'transactional',
                'trigger_event' => 'user.new_device_login',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000111', 'type' => 'channel', 'label' => 'Security Alert', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000112'],
                    ['id' => '00000000-0000-4000-8000-000000000112', 'type' => 'channel', 'label' => 'SMS Alert', 'channel' => 'sms', 'recipient' => '{{phone}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Account Locked',
                'description' => 'Notify user when account is locked due to suspicious activity',
                'category' => 'transactional',
                'trigger_event' => 'user.account_locked',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000121', 'type' => 'channel', 'label' => 'Account Locked Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000122'],
                    ['id' => '00000000-0000-4000-8000-000000000122', 'type' => 'channel', 'label' => 'Account Locked SMS', 'channel' => 'sms', 'recipient' => '{{phone}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Subscription Renewed',
                'description' => 'Confirm subscription renewal and send receipt',
                'category' => 'transactional',
                'trigger_event' => 'subscription.renewed',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000131', 'type' => 'channel', 'label' => 'Renewal Confirmation', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Subscription Cancelled',
                'description' => 'Confirm cancellation and offer win-back',
                'category' => 'transactional',
                'trigger_event' => 'subscription.cancelled',
                'correlation_field' => 'data.user_id',
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000141', 'type' => 'channel', 'label' => 'Cancellation Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000142'],
                    ['id' => '00000000-0000-4000-8000-000000000142', 'type' => 'delay', 'label' => 'Wait 7 Days', 'duration' => '7d', 'next' => '00000000-0000-4000-8000-000000000143'],
                    ['id' => '00000000-0000-4000-8000-000000000143', 'type' => 'channel', 'label' => 'Win-back Offer', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Two-Factor Authentication Code',
                'description' => 'Send 2FA verification code via SMS or email',
                'category' => 'transactional',
                'trigger_event' => 'user.2fa_requested',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000151', 'type' => 'channel', 'label' => '2FA Code', 'channel' => 'sms', 'recipient' => '{{phone}}', 'next' => null],
                ],
            ],

            // ============ ECOMMERCE TEMPLATES ============

            [
                'id' => Str::uuid()->toString(),
                'name' => 'Back in Stock',
                'description' => 'Notify customers when a product they wanted is back in stock',
                'category' => 'ecommerce',
                'trigger_event' => 'product.back_in_stock',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000161', 'type' => 'channel', 'label' => 'Back in Stock Email', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Price Drop Alert',
                'description' => 'Alert customers when a wishlisted product goes on sale',
                'category' => 'ecommerce',
                'trigger_event' => 'product.price_dropped',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000171', 'type' => 'channel', 'label' => 'Price Drop Email', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Review Request',
                'description' => 'Request product review after purchase with follow-up',
                'category' => 'ecommerce',
                'trigger_event' => 'order.delivered',
                'correlation_field' => 'data.order_id',
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000181', 'type' => 'delay', 'label' => 'Wait 5 Days', 'duration' => '5d', 'next' => '00000000-0000-4000-8000-000000000182'],
                    ['id' => '00000000-0000-4000-8000-000000000182', 'type' => 'channel', 'label' => 'Review Request', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => '00000000-0000-4000-8000-000000000183'],
                    ['id' => '00000000-0000-4000-8000-000000000183', 'type' => 'wait_for_event', 'label' => 'Wait for Review', 'events' => [['event' => 'review.submitted', 'next' => '00000000-0000-4000-8000-000000000184']], 'timeout' => '7d', 'onTimeout' => '00000000-0000-4000-8000-000000000185'],
                    ['id' => '00000000-0000-4000-8000-000000000184', 'type' => 'channel', 'label' => 'Thank You', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                    ['id' => '00000000-0000-4000-8000-000000000185', 'type' => 'channel', 'label' => 'Gentle Reminder', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Refund Processed',
                'description' => 'Confirm refund has been processed',
                'category' => 'ecommerce',
                'trigger_event' => 'order.refunded',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000191', 'type' => 'channel', 'label' => 'Refund Confirmation', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                ],
            ],

            // ============ ONBOARDING TEMPLATES ============

            [
                'id' => Str::uuid()->toString(),
                'name' => 'Feature Adoption',
                'description' => 'Guide users to key features they haven\'t used yet',
                'category' => 'onboarding',
                'trigger_event' => 'user.registered',
                'correlation_field' => 'data.user_id',
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000201', 'type' => 'delay', 'label' => 'Wait 1 Day', 'duration' => '1d', 'next' => '00000000-0000-4000-8000-000000000202'],
                    ['id' => '00000000-0000-4000-8000-000000000202', 'type' => 'channel', 'label' => 'Feature #1 Tip', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000203'],
                    ['id' => '00000000-0000-4000-8000-000000000203', 'type' => 'wait_for_event', 'label' => 'Wait for Usage', 'events' => [['event' => 'feature.used', 'conditions' => ['logic' => 'all', 'conditions' => [['field' => 'data.feature', 'operator' => 'eq', 'value' => 'feature_1']]], 'next' => '00000000-0000-4000-8000-000000000204']], 'timeout' => '3d', 'onTimeout' => '00000000-0000-4000-8000-000000000205'],
                    ['id' => '00000000-0000-4000-8000-000000000205', 'type' => 'channel', 'label' => 'Feature #1 Reminder', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => '00000000-0000-4000-8000-000000000204'],
                    ['id' => '00000000-0000-4000-8000-000000000204', 'type' => 'channel', 'label' => 'Feature #2 Tip', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Team Invitation',
                'description' => 'Send team invitation email to new members',
                'category' => 'onboarding',
                'trigger_event' => 'team.member_invited',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000211', 'type' => 'channel', 'label' => 'Invitation Email', 'channel' => 'email', 'recipient' => '{{invitee_email}}', 'next' => null],
                ],
            ],

            // ============ ENGAGEMENT TEMPLATES ============

            [
                'id' => Str::uuid()->toString(),
                'name' => 'Weekly Digest',
                'description' => 'Send weekly activity summary to users',
                'category' => 'engagement',
                'trigger_event' => 'digest.weekly_ready',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000221', 'type' => 'channel', 'label' => 'Weekly Digest', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Milestone Celebration',
                'description' => 'Celebrate user milestones (100 orders, 1 year anniversary, etc.)',
                'category' => 'engagement',
                'trigger_event' => 'user.milestone_reached',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000231', 'type' => 'channel', 'label' => 'Celebration Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'NPS Survey',
                'description' => 'Send Net Promoter Score survey after key interactions',
                'category' => 'engagement',
                'trigger_event' => 'support.ticket_resolved',
                'correlation_field' => 'data.ticket_id',
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000241', 'type' => 'delay', 'label' => 'Wait 1 Day', 'duration' => '1d', 'next' => '00000000-0000-4000-8000-000000000242'],
                    ['id' => '00000000-0000-4000-8000-000000000242', 'type' => 'channel', 'label' => 'NPS Survey', 'channel' => 'email', 'recipient' => '{{customer_email}}', 'next' => null],
                ],
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Birthday Greeting',
                'description' => 'Send birthday wishes with special offer',
                'category' => 'engagement',
                'trigger_event' => 'user.birthday',
                'correlation_field' => null,
                'is_featured' => false,
                'steps' => [
                    ['id' => '00000000-0000-4000-8000-000000000251', 'type' => 'channel', 'label' => 'Birthday Email', 'channel' => 'email', 'recipient' => '{{email}}', 'next' => null],
                ],
            ],
        ];

        foreach ($templates as $template) {
            WorkflowTemplateModel::updateOrCreate(
                ['name' => $template['name']],
                [
                    'id' => $template['id'],
                    'description' => $template['description'],
                    'category' => $template['category'],
                    'trigger_event' => $template['trigger_event'],
                    'correlation_field' => $template['correlation_field'],
                    'steps' => $template['steps'],
                    'is_featured' => $template['is_featured'],
                    'usage_count' => 0,
                ]
            );
        }
    }
}
