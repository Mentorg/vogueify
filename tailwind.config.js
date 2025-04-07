import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            container: {
                center: true,
                padding: "2rem",
                screens: {
                    sm: "100%",
                    md: "750px",
                    lg: "900px",
                    xl: "1100px",
                    "2xl": "1400px",
                },
            },
            fontFamily: {
                sans: ["Jost", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
