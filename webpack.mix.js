let mix = require('laravel-mix');
require('laravel-mix-tailwind');

mix.sass('resources/sass/early-access.scss', 'public/css')
    .copyDirectory('resources/svg', 'public/svg')
    .tailwind();

if (mix.production) {
    mix.version();
}
