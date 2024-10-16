/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.js',
  ],
  theme: {
    extend: {
      boxShadow: {
        'around': '0 0px 20px  rgba(0, 0, 0, 0.3)',
      }
    },
  },
  plugins: [],
};
