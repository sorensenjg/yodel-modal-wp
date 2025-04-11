import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: string[]) {
  return twMerge(clsx(inputs));
}

export function applyCustomThemeVariables(
  ref: HTMLElement,
  colorVariablesString: string
) {
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

export const jsonToFormData = (json: any) => {
  try {
    const data = new FormData();

    for (const k in json) {
      if (json.hasOwnProperty(k)) {
        data.append(k, json[k]);
      }
    }

    return data;
  } catch (error) {
    console.error(error);
    return null;
  }
};

export const isBusinessEmail = (email: string) => {
  const nonBusinessEmail =
    /^(?!.+@(gmail|google|yahoo|outlook|hotmail|msn|icloud)\..+)(.+@.+\..+)$/;

  return nonBusinessEmail.test(email.toLowerCase());
};

export const getFieldValueByType = (
  values: any,
  fields: any[],
  type: "name" | "email" | "message"
) => {
  const patterns = {
    name: /^(name|full[_-]?name|first[_-]?name|given[_-]?name|last[_-]?name|family[_-]?name|your[_-]?name)$/i,
    email:
      /^(email|email[_-]?address|your[_-]?email|contact[_-]?email|e[_-]?mail)$/i,
    message:
      /^(message|comment|content|body|your[_-]?message|inquiry|feedback|description)$/i,
  };

  // Find all matching field names from form_fields
  const matchingFields = fields
    .filter((field) => patterns[type].test(field.name))
    .map((field) => field.name);

  // If it's a name field, collect all values
  if (type === "name") {
    return matchingFields
      .map((fieldName) => values[fieldName])
      .filter(Boolean)
      .join(" ")
      .trim();
  }

  // For email and message, return the first matching value
  const matchingField = matchingFields[0];
  return matchingField ? values[matchingField] : "";
};
