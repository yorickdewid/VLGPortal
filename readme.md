# VLG Portal

## Introduction

VLG Portal provides an expressive and fluent interface for RotterdamPortal authentication and authorization. It handles most of the authentication and authorization itself and builds upon trusted JWT tokens. VLG Portal is compatible with the PSR-4 standard.

## License

VLG Portal is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Installation

To get started with VLG Portal, add to your `composer.json` file as a dependency:

```bash
    composer require vlg/gssauth
```

Be sure to add the `https://github.com/yorickdewid/VLGPortal` as a repository in your composer file.

### Configuration

After installing the library, register the `VLG\GSSAuth\PortalServiceProvider` in your `config/app.php` configuration file:

```php
    'providers' => [
        // Other service providers...

        VLG\GSSAuth\PortalServiceProvider,
    ],
```

In the `App\Providers\AppServiceProvider` register the authentication driver and guard.

```php
    public function register()
    {
        Auth::provider('portal', function() {
            return new \VLG\GSSAuth\PortalUserProvider(new User());
        });

        Auth::extend('portal', function($app, $name, array $config) {
            return new \VLG\GSSAuth\PortalGuard(Auth::createUserProvider($config['provider']));
        });
    }
```

You will also need to add credentials for the authentication services your application utilizes. These credentials should be placed in your `config/services.php` configuration file, and should use the key `vlgportal` For example:

```php
    'vlgportal' => [
        'key'    => env('VLGPORTAL_KEY'),
        'secret' => env('VLGPORTAL_SECRET'),
    ],
```

### Basic Usage

The application is now able to authenticate users. You will need two routes: one for redirecting the user to the RotterdamPortal, and another for receiving the callback from the provider after authentication. For example:

```php
    Route::get('auth', 'AuthController@redirectToProvider');
    Route::get('auth/callback', 'AuthController@handleProviderCallback');
```

Then handle the authentication:

```php
    <?php

    namespace App\Http\Controllers;

    use Auth;
    use VLG\GSSAuth\Portal;

    class AuthController extends Controller
    {
        /**
         * Redirect the user to the authentication portal.
         *
         * @return Response
         */
        public function redirectToProvider()
        {
            return Auth::authenticateSSO();
        }
    
        /**
         * Obtain the user information from the portal.
         *
         * @return Response
         */
        public function handleProviderCallback(Request $request)
        {
            if (!Auth::callback()) {
                echo 'Oops';
            }
    
            return redirect('/home');
        }
    }
```

The `authenticateSSO` method takes care of sending the user to the portal, while the `callback` method will read the incoming request and retrieve the user's information from the provider.


#### Retrieving User Details

Once you have a user instance, you can use the default `Auth::user()` class.

```php
    // Providers
    $user = Auth::user();
    $user->getId();
    $user->isActive();
    $user->isAdmin();
    $user->canRead();
    $user->canWrite();
```
