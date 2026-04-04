const { getColorsFromThemeJson } = require("./config/theme.js")

module.exports = {
  content: ["./parts/**/*.html", "./templates/**/*.html", "./patterns/**/*.php", "./functions.php", "./assets/js/**/*.js"],
  theme: {
    container: {
      center: true,
      padding: {
        DEFAULT: "1rem",
        md: "2rem",
      },
    },
    screens: {
      xs: "480px", // mobile
      sm: "640px", // mobile landscape
      md: "768px", // tablet
      lg: "1024px", // laptop
      xl: "1280px", // desktop
    },
    extend: {
      colors: {
        ...getColorsFromThemeJson(),
      },
      fontFamily: {
        sans: ["Inter", "-apple-system", "BlinkMacSystemFont", "Segoe UI", "sans-serif"],
      },
    },
  },
}
