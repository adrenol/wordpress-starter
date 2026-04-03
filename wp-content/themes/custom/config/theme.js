const fs = require("fs")
const path = require("path")

/**
 * Reads color palette from theme.json as single source of truth.
 * Returns an object like { base: "#ffffff", accent: "#0073aa", ... }
 */
function getColorsFromThemeJson() {
  const themeJsonPath = path.resolve(__dirname, "../theme.json")
  const themeJson = JSON.parse(fs.readFileSync(themeJsonPath, "utf-8"))
  const palette = themeJson.settings?.color?.palette ?? []

  const colors = {}
  for (const entry of palette) {
    colors[entry.slug] = entry.color
  }
  return colors
}

module.exports = { getColorsFromThemeJson }
