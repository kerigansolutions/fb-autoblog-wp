const defaultTheme = require("tailwindcss/defaultTheme");
const colors = defaultTheme.colors;

module.exports = {
  darkMode: 'class',
  content: [
    "./dist/AdminOverview.php",
    "./resources/scripts/**/*.{vue,js,ts,jsx,tsx}",
    "./resources/scripts/**/*.js.map",
  ],
  theme: {
    colors: ({ colors }) => ({
      inherit: colors.inherit,
      current: colors.current,
      transparent: colors.transparent,
      gray: colors.gray,
      white: '#FFFFFF',
      black: '#000000',

      // Blue
      primary: {
        light: '#015071',
        DEFAULT: '#043553',
        dark: '#011B30',
      },

      // Green
      accent: {
        DEFAULT: '#b5be34',
      },

      // Errors
      error: {
        DEFAULT: '#FF715B'
      }
    })
  }
}
