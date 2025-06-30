<?php

/**
 * Dual API Demo
 *
 * This demonstrates both the legacy and modern resource-based APIs
 * working side by side during the transition period.
 */

require_once __DIR__.'/../vendor/autoload.php';

use JordanPartridge\StravaClient\Connector;
use JordanPartridge\StravaClient\StravaClientV2;

// Initialize the client
$connector = new Connector;
$client = new StravaClientV2($connector);

// Set authentication tokens
$client->setToken('your-access-token', 'your-refresh-token');

echo "=== Strava Client Dual API Demo ===\n\n";

// LEGACY API - Still works exactly as before
echo "📚 LEGACY API (Deprecated but functional):\n";
echo "┌─────────────────────────────────────────┐\n";
echo "│ Activities via legacy method            │\n";
echo "└─────────────────────────────────────────┘\n";
echo "\$activities = \$client->activityForAthlete(['page' => 1]);\n";
echo "\$activity = \$client->getActivity(123);\n\n";

echo "┌─────────────────────────────────────────┐\n";
echo "│ Webhooks via legacy method              │\n";
echo "└─────────────────────────────────────────┘\n";
echo "\$subscription = \$client->createWebhookSubscription();\n";
echo "\$subscriptions = \$client->viewWebhookSubscriptions();\n";
echo "\$client->deleteWebhookSubscription(123);\n\n";

// MODERN RESOURCE-BASED API
echo "🚀 MODERN RESOURCE-BASED API (Recommended):\n";
echo "┌─────────────────────────────────────────┐\n";
echo "│ Activities via resource pattern         │\n";
echo "└─────────────────────────────────────────┘\n";
echo "\$activities = \$client->activities()->list(['page' => 1]);\n";
echo "\$activity = \$client->activities()->get(123);\n\n";

echo "┌─────────────────────────────────────────┐\n";
echo "│ Webhooks via resource pattern           │\n";
echo "└─────────────────────────────────────────┘\n";
echo "\$subscription = \$client->webhooks()->create();\n";
echo "\$subscriptions = \$client->webhooks()->list();\n";
echo "\$client->webhooks()->delete(123);\n";
echo "\$exists = \$client->webhooks()->exists();\n";
echo "\$first = \$client->webhooks()->first();\n\n";

echo "┌─────────────────────────────────────────┐\n";
echo "│ Token management (both styles)          │\n";
echo "└─────────────────────────────────────────┘\n";
echo "// Legacy style:\n";
echo "\$client->setToken('access', 'refresh');\n\n";
echo "// Modern style:\n";
echo "\$client->withTokens('access', 'refresh');\n\n";

echo "🎯 BENEFITS OF RESOURCE-BASED API:\n";
echo "• Type-safe method signatures\n";
echo "• Cleaner, more intuitive organization\n";
echo "• Extensible for new endpoints\n";
echo "• Better IDE autocompletion\n";
echo "• Future-proof architecture\n\n";

echo "📅 MIGRATION TIMELINE:\n";
echo "• v0.6.0: Introduce resource pattern (both APIs work)\n";
echo "• v0.7.0: Add deprecation warnings to legacy methods\n";
echo "• v1.0.0: Remove legacy methods entirely\n\n";

echo "✨ Ready to explore real-time Strava integration!\n";
