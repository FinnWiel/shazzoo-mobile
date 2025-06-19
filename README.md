# Shazzoo Mobile Notifications

A Laravel package to manage Expo push notifications with per-device preferences for the Shazzoo mobile app.

[![Packagist Version](https://img.shields.io/packagist/v/finnwiel/shazzoo-notify.svg)](https://packagist.org/packages/finnwiel/shazzoo-notify)
![Laravel](https://img.shields.io/badge/laravel-12.x-red)
![PHP](https://img.shields.io/badge/php-^8.1-blue)

## Features

- Send push notification to registered phones.
- Send notifications to registered pc's.
- Per device preferences for notification's.


## Installation
```
composer require finnwiel/shazzoo-notify
```

#### Publish the migrations
```
php artisan shazzoo-notify:install
```
#### Ensure the Laravel API and broadcasting stack are installed:
```
php artisan install:api
php artisan install:broadcasting //reverb
```
 Make sure to add the `HasApiTokens` to the `user` model.

 Also check if your `.env` file contains the correct settings for reverb.
```
BROADCAST_CONNECTION=reverb
BROADCAST_DRIVER=reverb

REVERB_APP_ID=<your-app-id>
REVERB_APP_KEY=<your-app-key>
REVERB_APP_SECRET=<your-app-secret>
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_PATH=

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## Usage

### Authentication

In the mobile app you can login with any user that has a profile on your laravel website. This package then stores the Expo token in a database together with the device type. This expo token is needed to send notifications.

For the desktop app you can also login using a profile that is registered in your app. This wil set a uuid to identify the pc and also set the device type to desktop.

Upon login your prefrences will all be set to `enabled`, if you want to disable them you will need to do so in the mobile app.

### Sending Notifications

The notifications to desktop are sent via a websocket connection. So make sure to start reverb.
```
php artisan reverb:start
```

- Use the provided Artisan command to send notifications:
```
php artisan shazzoo:notify 
```
The command also accepts some tags:

| Tag       | Description                                      | 
|-----------|--------------------------------------------------|
| `--user=` | Takes in the `email` or `id` of a user           | 
| `--type=` | Sets the type of notificaition you want to send. |
| `--title=`| Sets the title of the notification               |
| `--body=` | Sets the body of the notification                |


#### Notification Types

You can define your own notification types. This package provides a `notification_types` table, where each type includes:

- A unique name
- A description

These types help organize and filter which notifications are sent and managed.

### Queue

By default laravel uses a queue to send the events, so you will have to run:

```
php artisan queue:work
```

If you dont want the notifications to be sent on a queue you can set the `QUEUE_CONNECTION` to sync in the `.env` file.

## License

This package is open-sourced software licensed under the MIT license.
