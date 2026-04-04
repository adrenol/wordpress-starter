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
      sm: "600px",
      md: "782px",
      lg: "1024px",
      xl: "1200px",
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
