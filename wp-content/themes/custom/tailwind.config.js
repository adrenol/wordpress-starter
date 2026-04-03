const { getColorsFromThemeJson } = require("./config/theme.js")

module.exports = {
  content: ["./parts/**/*.html", "./templates/**/*.html", "./patterns/**/*.html", "./functions.php", "./assets/js/**/*.js"],
  theme: {
    extend: {
      colors: {
        ...getColorsFromThemeJson(),
      },
    },
  },
}
