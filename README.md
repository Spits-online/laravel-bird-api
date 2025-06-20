![image](https://repository-images.githubusercontent.com/820425309/723992d8-e187-420a-8bb6-b23c2991b8ca)

# Bird.com API Support for Laravel

1. [Introduction](#overview)
    - [Why This Package?](#why-this-package)
2. [Installation](#installation)
    - [Prerequisites](#prerequisites)
    - [Step-by-Step Installation](#step-by-step-installation)
3. [Configuration](#configuration)
4. [Usage](#usage)
    - [1. Contact Management](#1-contact-management)
        - [Retrieve Contacts](#retrieve-contacts)
        - [Retrieve a Single Contact](#retrieving-a-single-contact)
        - [Create or Update Contacts](#create-or-update-contacts)
        - [Delete Contacts](#delete-contacts)
        - [Contact Model Overview](#contact-model-overview)
    - [2. Sending Notifications](#2-sending-notifications)
        - [Supported Notification Channels](#supported-notification-channels)
        - [Example: Sending SMS Notifications](#example-sending-sms-notifications)
5. [Exception Handling](#exception-handling)
6. [Contributing](#contributing)
7. [License](#license)
8. [Contact](#contact)


## Overview
The Laravel Bird Package simplifies integrating the powerful MessageBird API into your Laravel applications. 
It provides a user-friendly way to manage contacts and send notifications via SMS, WhatsApp, email, and more. 
This package is designed to make communication between [Laravel](https://laravel.com) and [Bird](https://bird.com)
(AKA MessageBird) seamless and efficient.

### Why This Package?
- **Ease of Use**: Straightforward methods to interact with Bird's API.
- **Multi-Channel Support**: Send notifications via SMS, WhatsApp, Email, Telegram, and more.
- **Contact Management**: Create, update, retrieve, or delete contacts in Bird directly from your application.
- **Error Handling**: Built-in exception classes for better debugging and recovery.

[//]: # (- **Dynamic Templates**: Leverage reusable templates for consistent notifications.)


## Installation

### Prerequisites
Before installing this package, ensure your system meets the following requirements:
- **PHP**: Version `^8.3`
- **Laravel**: Version `^10.0`, `^11.0`, `^12.0`
- **Bird Account**

### Step-by-Step Installation
1. Add the package to your Laravel project using Composer:
    ```bash
    composer require spits-online/laravel-bird-api
   ```
2. Once installed, the package will automatically register the `BirdServiceProvider` using Laravel's package auto-discovery.
3. Run the following command to publish the package configuration:
   ```bash
   php artisan vendor:publish --tag="bird-config"
    ```
   This will create a `config/bird.php` file in your application.


## Configuration

The `config/bird.php` file contains all configurable options, including:

- API Access Key: Set your Bird.com API access key via `BIRD_ACCESS_KEY` in the .env file.
- Workspace ID: Define your workspace using the `BIRD_WORKSPACE_ID` environment variable.
- Channel IDs: Specify channel IDs (e.g., SMS, WhatsApp, Email) for notifications in your .env file:
    ```env
    BIRD_ACCESS_KEY={your-bird-access-key}
    BIRD_SMS_CHANNEL_ID={your-sms-channel-id}
    ```
For detailed configuration options, refer to the comments within the config/bird.php file.

## Usage

### 1. Contact Management

This package provides functionality for managing contacts via the Bird API. Below are the key actions you can perform with the `ContactService`.

#### Retrieve Contacts
You can retrieve a list of contacts using the `index()` method. This allows you to specify the number of contacts to retrieve and whether to reverse the order of the results. 
To be able to retrieve the contacts, make sure you have specified your `BIRD_WORKSPACE_ID` in you `.env` file.

```php
use Spits\Bird\Services\ContactService;

$birdContacts = app(new ContactService())->index(limit: 20, reverse: true);
```

Parameters:
- `limit`: The number of contacts to retrieve (default is 10).
- `reverse`: Set to `true` to retrieve contacts in reverse order.
- `nextPageToken`: Use for pagination when retrieving the next set of results.


#### Retrieving a single contact
You can also retrieve a single contact using the `show()` method. This allows you to get only one contact by specifying its id.

```php
use Spits\Bird\Services\ContactService;

$birdContact = app(new ContactService())->show('bird-contact-id-123');
```

Parameters:
- `contactId`: The id of the contact

#### Create or Update Contacts
You can create or update a contact by passing a `Contact` object to the `createOrUpdate()` method. 
This method requires the contact's identifier (phone number or email address)
to determine whether to create a new contact or update an existing one.

```php
use Spits\Bird\Models\Contact;
use Spits\Bird\Enums\IdentifierKey;
use Spits\Bird\Services\ContactService;

$contact = (new Contact())
    ->displayName('John Doe')
    ->phoneNumber('+12345678901')
    ->emailAdress('johndoe@mail.com');

$response = (new ContactService())->createOrUpdate($contact, IdentifierKey::PHONE_NUMBER);
```

Parameters:
- `Contact`: The `Contact` object containing the contact information.
- `IdentifierKey`: The identifier type used to identify the contact (either `PHONE_NUMBER` or `EMAIL_ADDRESS`).


#### Delete Contacts
To delete a contact, simply call the `delete()` method with the contact's ID.

```php
use Spits\Bird\Services\ContactService;

$response = (new ContactService())->delete('contact-id-123');

if ($response === true) {
    // Successfully deleted the contact
    dd('Contact deleted successfully.');
} else {
    // Handle error
    dd($response);
}
```

Parameters:
- `contactId`: The unique ID of the contact to be deleted.

Return Values:
- Returns `true` if the deletion was successful.
- Returns an error response if the deletion failed.

---

#### Contact Model Overview
The `Contact` model provides an easy-to-use interface for building
and manipulating contact records before sending them to the Bird API.

##### Example Contact Creation
```php
use Spits\Bird\Models\Contact;

$contact = (new Contact())
    ->displayName('Jane Doe')
    ->phoneNumber('+98765432103')
    ->emailAdress('jane@example.com')
    ->attribute('company', 'Acme Corp');

// The contact can then be passed to the `ContactService` for API interaction
```

##### Contact Methods:
- `displayName(string $name)`: Sets the display name of the contact.
- `phoneNumber(string $number)`: Sets the phone number of the contact. 
- `emailAdress(string $email)`: Sets the email address of the contact.
- `attribute(string $attribute, mixed $value)`: Adds additional attributes to the contact.
- `toArray()`: Converts the contact into an array for sending to Bird API.

Ensure to validate the phone number using the regex defined in the configuration
(`bird.phone_number_regex`) before sending it to the API.

---

### 2. Sending Notifications

This package supports a variety of notification channels, including SMS, WhatsApp, email, and more. 
Below are the details on using the SMS channel for sending notifications, leveraging predefined templates, and handling advanced use cases.


### Supported Notification Channels

Currently, we only support the SMS channel. 
This is not going to be the case soon though, 
as we are planning on adding support for WhatsApp, facebook and telegram notification channels.

### Example: Sending SMS Notifications

The `SMSChannel` allows you to send SMS notifications by leveraging Laravel notification system.
Make sure you are allowed to send SMS notifications using Bird.
You need to configure an SMS channel before you can send SMS notifications. 

#### Notification Class Example
Define a custom notification class implementing the `toSMS` method.

```php
use Illuminate\Notifications\Notification;use Spits\Bird\Channels\SMSChannel;use Spits\Bird\Messages\SMSMessage;

class OrderNotification extends Notification
{
    public function via(): array
    {
        return [ SMSChannel::class ];
    }

    public function toSMS($notifiable): SMSMessage
    {
        $contact = (new Contact())
            ->displayName('Jane Doe')
            ->phoneNumber('+98765432103')
            ->emailAdress('jane@example.com')
            ->attribute('company', 'Acme Corp');
        
        return (new SMSMessage())
            ->text('Your order has been shipped!')
            ->toContact($contact);
    }
}
```


#### Sending the Notification
You can send the notification using Laravel's `Notification` facade or the `notify` method.

## WhatsApp

### config
In the `bird.php` config file you'll see `templates` array and in there the empty `whatsapp` key.\
Follow the example below to add the keys belonging to your template.\
Recommended way  is to place the keys in your .env

```php

'whatsapp' => [
    'foo_template' => [
        'template_project_id' => `template_project_id`,
        'template_version' => `template_version`,
        'template_locale' => `template_locale`,
    ],
]
```

### usage
To send a message through WhatsApp create you own Notification class.\
That should then use our `WhatsappMessage` class. Example below

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;use Spits\Bird\Channels\WhatsappChannel;use Spits\Bird\Models\Messages\WhatsappMessage;

class WhatsappNotification extends Notification
{
    public function __construct(
        public string $content
    )
    {
        $this->setChannels([
            WhatsappChannel::class
        ]);
    }

    public function toWhatsapp($notifiable): WhatsappMessage {
        $message = new WhatsappMessage(
            config('bird.templates.whatsapp.foo_message.template_project_id'),
            config('bird.templates.whatsapp.foo_message.template_version'),
            config('bird.templates.whatsapp.foo_message.template_locale'),
            receiver: '+31'.$notifiable->phone_number,
            templateVariables: [
                'variables' => `set in template`
                ]
        );
        return $message;
    }
}

```


```php
use Illuminate\Support\Facades\Notification;

Notification::send($user, new OrderNotification());
```


### Exception Handling

The package uses custom exceptions to handle errors:

- `InvalidParameterException`: Thrown when a parameter is invalid.
- `ConnectionException`: Thrown when there is a connection error with the API.
- `NotAnSmsMessageException`: Thrown when the provided message is not an instance of `SMSMessage`.
- `NotificationNotSent`: Thrown when the notification could not be sent.

Make sure to catch these exceptions in your code to handle errors gracefully.

## Contributing

Please submit issues and pull requests to the [GitHub repository](https://github.com/Spits-online/laravel-bird-api).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Contact

For any inquiries or support, please contact [Spits](mailto:webapps@spits.online).
