/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/templates/*.{html,js,twig}"],
    theme: {
        extend: {
            fontFamily: {
                'manrope': 'Manrope, sans-serif',
                'montserrat': 'Montserrat, sans-serif',
            },
            colors: {
                'blue-mds': '#25B8C5'
            },
        },
    },
    plugins: [],
}