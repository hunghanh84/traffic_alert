<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Configuration
    |--------------------------------------------------------------------------
    */
    
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
    
    'channel_id' => env('TELEGRAM_CHANNEL_ID', ''),
    
    'api_url' => 'https://api.telegram.org/bot',
];
