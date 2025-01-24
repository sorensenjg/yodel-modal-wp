import { Config, Form as FormType } from "@/types";
import { DefaultForm, ContactForm7 } from "./forms";

interface FormControllerProps extends FormType {
  config: Config;
  modal_id: number;
  before?: string;
  after?: string;
  onSuccess: () => void;
  onClose: () => void;
}

export function Form({ form_type, ...props }: FormControllerProps) {
  if (form_type === "cf7_form") {
    return <ContactForm7 form_type={form_type} {...props} />;
  }

  return <DefaultForm form_type={form_type} {...props} />;
}
