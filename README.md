# Shazzoo Mobile Notifications

A Laravel package to manage Expo push notifications with per-device preferences for the Shazzoo mobile app.

[![Packagist Version](https://img.shields.io/packagist/v/finnwiel/shazzoo-mobile.svg)](https://packagist.org/packages/finnwiel/shazzoo-mobile)
![Laravel](https://img.shields.io/badge/laravel-12.x-red)
![PHP](https://img.shields.io/badge/php-^8.1-blue)

## Features

- Send push notification to registered phones.
- Send notifications to registered pc's.
- Per device preferences for notification's.


## Installation
```
composer require finnwiel/shazzoo-mobile
```

#### Publish the migrations
```
php artisan shazzoo-mobile:install
```
#### Ensure the Laravel API and broadcasting stack are installed:
```
php artisan install:api
php artisan install:broadcasting
```
## Usage

### Authentication

In the mobile app you can login with any user that has a profile on your laravel website. This package then stores the `Expo token` in a database together with the device type. This expo token is needed to send notifications.

For the desktop app you can also login using a profile that is registered in your app. 

Upon login your prefrences will all be set to `enabled`, if you want to disable them you will need to do so in the mobile app.


### Sending Notifications

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

## License

This package is open-sourced software licensed under the MIT license.
