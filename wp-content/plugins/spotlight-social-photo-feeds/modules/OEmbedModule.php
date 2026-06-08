<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Actions\OEmbedHandler;
use Psr\Container\ContainerInterface;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factories\Constructor;

class OEmbedModule extends Module
{
    public function run(ContainerInterface $c): void
    {
        wp_embed_register_handler(
            $c->get('id'),
            $c->get('regex'),
            $c->get('handler')
        );
    }

    public function getFactories(): array
    {
        return [
            // The ID of the handler
            'id' => new Value('sl_instagram_embed'),
            // The base URL to use to request embeds from Facebook's API
            'url' => new Value('https://graph.facebook.com/v22.0/instagram_oembed'),
            // The regex to match Instagram post URLs
            'regex' => new Value('#https?://((m|www)\.)?instagram\.com/p/(.*)#i'),
            // The handler function that creates the HTML string
            'handler' => new Constructor(OEmbedHandler::class, [
                'url',
                '@ig/client',
                '@accounts/cpt',
            ]),
        ];
    }
}
