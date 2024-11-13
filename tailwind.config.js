const { fontFamily } = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
  // corePlugins: {
  //   preflight: false,
  // },
  important: true,
  darkMode: ["class"],
  content: ["./src/**/*.{js,jsx,ts,tsx}"],
  theme: {
    container: {
      center: true,
      padding: "2rem",
      screens: {
        "2xl": "1400px",
      },
    },
    extend: {
      colors: {
        border: "hsl(var(--border))",
        input: "hsl(var(--input))",
        ring: "hsl(var(--ring))",
        background: "hsl(var(--background))",
        foreground: "hsl(var(--foreground))",
        primary: {
          DEFAULT: "hsl(var(--primary))",
          foreground: "hsl(var(--primary-foreground))",
        },
        secondary: {
          DEFAULT: "hsl(var(--secondary))",
          foreground: "hsl(var(--secondary-foreground))",
        },
        destructive: {
          DEFAULT: "hsl(var(--destructive))",
          foreground: "hsl(var(--destructive-foreground))",
        },
        muted: {
          DEFAULT: "hsl(var(--muted))",
          foreground: "hsl(var(--muted-foreground))",
        },
        accent: {
          DEFAULT: "hsl(var(--accent))",
          foreground: "hsl(var(--accent-foreground))",
        },
        popover: {
          DEFAULT: "hsl(var(--popover))",
          foreground: "hsl(var(--popover-foreground))",
        },
        card: {
          DEFAULT: "hsl(var(--card))",
          foreground: "hsl(var(--card-foreground))",
        },
      },
      borderRadius: {
        lg: `var(--radius)`,
        md: `calc(var(--radius) - 2px)`,
        sm: "calc(var(--radius) - 4px)",
      },
      fontFamily: {
        sans: ["var(--font-sans)", ...fontFamily.sans],
      },
      keyframes: {
        "accordion-down": {
          from: { height: "0" },
          to: { height: "var(--radix-accordion-content-height)" },
        },
        "accordion-up": {
          from: { height: "var(--radix-accordion-content-height)" },
          to: { height: "0" },
        },
      },
      animation: {
        "accordion-down": "accordion-down 0.2s ease-out",
        "accordion-up": "accordion-up 0.2s ease-out",
      },
      typography: ({ theme }) => ({
        DEFAULT: {
          css: {
            // "h1, h2, h3, h4, h5, h6": {
            //   fontWeight: theme("fontWeight.bold"),
            //   lineHeight: theme("lineHeight.snug"),
            //   margin: "1rem 0",
            // },
            // h1: {
            //   fontSize: theme("fontSize.5xl"),
            // },
            // h2: {
            //   fontSize: theme("fontSize.4xl"),
            // },
            // h3: {
            //   fontSize: theme("fontSize.3xl"),
            // },
            // h4: {
            //   fontSize: theme("fontSize.2xl"),
            // },
            // h5: {
            //   fontSize: theme("fontSize.xl"),
            // },
            // h6: {
            //   fontSize: theme("fontSize.xl"),
            //   fontStyle: "italic",
            // },
            // ol: {
            //   counterReset: "number-list",
            //   listStyle: "none",
            //   paddingLeft: "1.75rem",

            //   li: {
            // 	position: "relative",

            // 	"&:before": {
            // 	  content: "counter(number-list)",
            // 	  counterIncrement: "number-list",
            // 	  color: theme("colors.white"),
            // 	  fontSize: "16px",
            // 	  fontWeight: "bold",
            // 	  position: "absolute",
            // 	  top: "0.15rem",
            // 	  left: "-1.75rem",
            // 	  width: "1.5rem",
            // 	  height: "1.5rem",
            // 	  padding: "0.08rem 0 0 0.05rem",
            // 	  display: "flex",
            // 	  justifyContent: "center",
            // 	  alignItems: "center",
            // 	  backgroundColor: theme("colors.blue[700]"),
            // 	  borderRadius: "50%",
            // 	},
            //   },
            // },
            // ul: {
            //   listStyle: "none",
            //   paddingLeft: "1.75rem",

            //   li: {
            // 	position: "relative",

            // 	"&:before": {
            // 	  content: "'\\203A'",
            // 	  color: theme("colors.white"),
            // 	  fontSize: "26px",
            // 	  fontWeight: "bold",
            // 	  position: "absolute",
            // 	  top: "0.15rem",
            // 	  left: "-1.75rem",
            // 	  width: "1.5rem",
            // 	  height: "1.5rem",
            // 	  padding: "0 0 0.15rem 0.15rem",
            // 	  display: "flex",
            // 	  justifyContent: "center",
            // 	  alignItems: "center",
            // 	  backgroundColor: theme("colors.blue[700]"),
            // 	  borderRadius: "50%",
            // 	},
            //   },
            // },
            "img, .wp-caption": {
              maxWidth: "100%",
            },
            "img.alignright, a img.alignright": {
              float: "right",
              margin: "0 0 1em 1em",
            },
            "img.alignleft, a img.alignleft": {
              float: "left",
              margin: "0 1em 1em 0",
            },
            "img.aligncenter, a img.aligncenter": {
              display: "block",
              margin: "0 auto",
            },
            iframe: {
              maxWidth: "100%",
            },
          },
        },
      }),
    },
  },
  plugins: [require("@tailwindcss/typography"), require("tailwindcss-animate")],
};
