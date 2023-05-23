/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
module.exports = {
  darkMode: 'class',
  content: ['./resources/**/*.js', './resources/js/**/*.vue'],
  theme: {
    extend: {
      colors: {
        primary: 'var(--primary)',
        gray: colors.slate,
      },
    },
  },
  plugins: [],
  important: '.nova-tabs',
}
