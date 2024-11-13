module.exports = {
  plugins: [
    require("tailwindcss"),
    require("postcss-prefix-selector")({
      prefix: "#yodel-wp-container",
      exclude: [".yodel-wp-button"],
    }),
    require("autoprefixer"),
  ],
};
