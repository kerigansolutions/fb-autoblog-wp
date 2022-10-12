const mix = require("laravel-mix")

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your WordPlate applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JavaScript files.
 |
 */

mix
  .setResourceRoot("../")
  .setPublicPath("./dist")
  .postCss("resources/styles/facebook-admin.css", "", [
    require("tailwindcss"),
    require("autoprefixer"),
  ])
  .js("resources/scripts/facebook-admin.js", "")
  .vue()
