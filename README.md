# Shazzoo Mobile Notifications

A Laravel package to manage Expo push notifications with per-device preferences for the Shazzoo mobile app.

## Features

- Authenticate users and associate Expo push tokens per device.
- Manage notification types and device-specific notification preferences.
- Send notifications respecting user preferences per device.
- Supports Laravel Sanctum for API authentication.

## Installation
```
composer require finnwiel/shazzoo-mobile
```
```
php artisan shazzoo-mobile:install
```
You also need to install the api from laravel if you haven't already
```
php artisan install:api
```
## Usage

### Authentication

In the mobile app you can login with any user that has a profile on your laravel website. This package then stores the `Expo token` in a database together with the device type. This expo token is needed to send notifications.

Upon login your prefrences will all be set to `enabled`, if you want to disable them you will need to do so in the mobile app.


### Sending Notifications

- Use the provided Artisan command to send notifications respecting user preferences:
```
php artisan shazzoo:notify 
```
The command also accepts some tags:

| Tag       | Description                   | 
|-----------|----------------------------|
| `--user=` | Takes in the `email` or `id` of a user | 
| `--type=` | Sets the type of notificaition you want to send. |
| `--title=`| Sets the title of the notification |
| `--body=` | Sets the body of the notification |


#### Notification Types

You can dconfigure what notification types you have in your applicaiton yourself, all this package does is create the `notification_types` table for you. 

This table takes in a unique name and a description of the notification.

## API Endpoints

| Method | Endpoint                   | Description                          | Auth Required |
|--------|----------------------------|------------------------------------|---------------|
| POST   | `/api/login`               | Login and register Expo token      | No            |
| POST   | `/api/logout`              | Logout and remove Expo token       | Yes           |
| GET    | `/api/notification-preferences` | Get notification preferences       | Yes           |
| POST   | `/api/notification-preferences`| Update notification preferences    | Yes           |
| GET    | `/api/notification-types` | Get all available notification types | Yes         |

## License

This package is open-sourced software licensed under the MIT license.
