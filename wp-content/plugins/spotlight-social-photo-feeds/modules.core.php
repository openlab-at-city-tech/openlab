<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use RebelCode\Spotlight\Instagram\Modules\Dev\DevModule;

return [
    'wp' => new WordPressModule(),
    'atlas' => new AtlasModule(),
    'admin' => new AdminModule(),
    'config' => new ConfigModule(),
    'ig' => new InstagramModule(),
    'engine' => new EngineModule(),
    'feeds' => new FeedsModule(),
    'templates' => new TemplatesModule(),
    'preview' => new PreviewModule(),
    'accounts' => new AccountsModule(),
    'media' => new MediaModule(),
    'updater' => new UpdateCronModule(),
    'cleaner' => new CleanUpCronModule(),
    'token_refresher' => new TokenRefresherModule(),
    'rest_api' => new RestApiModule(),
    'server' => new ServerModule(),
    'ui' => new UiModule(),
    'shortcode' => new ShortcodeModule(),
    'wp_block' => new WpBlockModule(),
    'widget' => new WidgetModule(),
    'oembed' => new OEmbedModule(),
    'notifications' => new NotificationsModule(),
    'migrator' => new MigrationModule(),
    'saas' => new SaasModule(),
    'news' => new NewsModule(),
    'integrations/caching' => new CacheIntegrationsModule(),
    'security' => new SecurityModule(),
    'user' => new UserModule(),
    'leave_review' => new LeaveReviewModule(),
    'survey_2023' => new Survey2023Module(),
    'dev' => new DevModule(),
];
