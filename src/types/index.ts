export type Config = {
  ajaxUrl: string;
  nonce: string;
  isUserAdmin: boolean;
  isUserLoggedIn: boolean;
};

export type Settings = {
  theme: {
    color_scheme: string;
    color_variables: string;
  };
  //   email: {
  //     to: string;
  //     from: string;
  //     subject: string;
  //     headers: string[];
  //   };
};

export type Button = {
  type: "link" | "close";
  variant:
    | "default"
    | "destructive"
    | "outline"
    | "secondary"
    | "ghost"
    | "link";
  title: string;
  url: string;
  target: string;
};

export type Form = {
  fields: any;
  form_type: "default_form" | "cf7_form" | "gravity_form";
  form_id?: string;
  form_disabled?: boolean;
  submission_type: "database" | "email" | "both";
  messages: {
    success: string;
    error: string;
  };
};

export type Layout = {
  layout: "layout_1" | "layout_2" | "layout_3" | "layout_cf7";
  columns: number;
  image?: {
    src: string;
    alt: string;
    width: number;
    height: number;
  };
  title: string;
  content: string;
  form_before?: string;
  form_after?: string;
  buttons?: Button[];
  background_image?: {
    src: string;
    width: number;
    height: number;
  };
};

export interface ModalSettings extends Settings {
  display: {
    displayed_at: string[];
    initialization: "button" | "timer" | "exit_intent";
    delay: number;
    dismissal_expiration: number;
  };
}

export interface Modal extends Layout {
  id: number;
  status: string;
  form?: Form;
  settings: ModalSettings;
}
