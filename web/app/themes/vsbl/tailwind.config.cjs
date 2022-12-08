// https://tailwindcss.com/docs/configuration
module.exports = {
  content: ["./index.php", "./app/**/*.php", "./resources/**/*.{php,vue,js}"],
  theme: {
    extend: {
      colors: {}, // Extend Tailwind's default colors
    },
    screens: {
      xs: "568px",
      sm: "768px",
      md: "1024px",
      lg: "1280px",
      xl: "1440px",
      notouch: { raw: "(hover: hover)" },
    },
  },
  plugins: [],
  corePlugins: {
    container: false,
  },
};
