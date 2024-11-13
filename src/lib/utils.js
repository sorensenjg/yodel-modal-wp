import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

export function applyCustomThemeVariables(ref, colorVariablesString) {
  if (!ref || !colorVariablesString) {
    return;
  }

  const lines = colorVariablesString
    .split(";")
    .filter((line) => line.trim() !== "");

  lines.forEach((line) => {
    const [key, value] = line.split(":").map((part) => part.trim());

    if (key && value) {
      const cssVarName = key.startsWith("--") ? key : `--${key}`;
      ref.style.setProperty(cssVarName, value);
    }
  });
}
