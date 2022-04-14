## About the Project

LaraChat is a websocket-based chat application that utilizes its own chat server using laravel-websocket package. It is made with Laravel as its api backend, nuxt as its website and admin frontend, nuxt as its embeddable user-facing chat user interface and laravel with laravel-websocket package as its websocket server.

### Installation
- composer install
- php artisan key:generate

### Operation
- Setup env attributes. Pusher values may be anything as long as all systems involved shares the same values.
- Migrate and seed database: php artisan migrate --seed
- Laravel(API) - generate default app by triggering the chatapp factory
- Laravel(API) - Messaging uses queue: php artisan queue:listen
- Laravel(Websocket Server) - Init websocket server: php artisan websocket:serve
- Nuxt Chat(Chat-UI) - npm run dev. Run on localhost:3000
- Nuxt Admin(Nuxt-chat) - npm run dev. Run on port 3001
- Embed Nuxt Chat(Chat-UI) on any website.

LaraChat is a full chat ecosystem utilizing the Laravel Echo package. It uses Laravel's Cashier package for its subscription-based usage. Subscription plan details are configurable in `config/subscriptionplans.php`.

## Contributing

Thank you for deciding to contribute to the LaraChat project. Email the project owner at dev.weward@gmail.com.

## Security Vulnerabilities

If you discover a security vulnerability within LaraChat, please send an e-mail to Roland Edward Santos via [dev.weward@gmail.com](mailto:dev.weward@gmail.com). All security vulnerabilities will be promptly addressed.

## License

LaraChat is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Interests

Please send an e-mail to Roland Edward Santos via [dev.weward@gmail.com](mailto:dev.weward@gmail.com).