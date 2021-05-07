# Negativity-Interface

## What is it ?

It's a web interface which is used to manage Negativity's informations.

It have been made for [Negativity v2](https://www.spigotmc.org/resources/86874/) (premium). No support will be made if you are using this with free version.

## How to install ?

Pre-requis:
- Website server (apache/nginx/...)
- PhP
- PDO extension enabled in PhP config

1) Clone the git (or download the data)
2) Put all content in your website folder (in `/var/www/html` by default, on linux).
3) Now, go at http://your-website.com/config and config your server.
4) Everything done !

## How to use ?

You can connect to the website, and see informations.

If you have moderator, you can create users.

## Build Setup

``` bash
# install dependencies
$ npm install # Or yarn install

# watch and auto-compiles Sass every time it changes.
$ sass --watch include/css/sass/main.scss:include/css/main.css
```

Don't edit `/include/css/main.css`: changes will be ignored.
