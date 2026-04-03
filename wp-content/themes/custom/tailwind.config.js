const { getColorsFromThemeJson } = require("./config/theme.js")

module.exports = {
  content: ["./parts/**/*.html", "./templates/**/*.html", "./patterns/**/*.html", "./functions.php", "./assets/js/**/*.js"],
  theme: {
    // 1. Настраиваем адаптивный контейнер
    container: {
      center: true,
      padding: {
        DEFAULT: '1rem',
        md: '2rem',
      },
    },
    // 2. Синхронизируем брейкпоинты с WordPress Gutenberg
    screens: {
      sm: '600px',   // WP mobile breakpoint
      md: '782px',   // WP tablet breakpoint
      lg: '1024px',  // Стандартный десктоп
      xl: '1200px',  // Совпадает с wideSize из theme.json
    },
    extend: {
      colors: {
        ...getColorsFromThemeJson(),
      },
      fontFamily: {
        // Добавляем шрифт из theme.json для использования классов font-sans
        sans: ["Inter", "-apple-system", "BlinkMacSystemFont", "Segoe UI", "sans-serif"],
      }
    },
  },
}
