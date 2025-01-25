import { useState } from "react";
import { useForm } from "react-hook-form";
// import { zodResolver } from "@hookform/resolvers/zod";
// import { z } from "zod";
// import { cn } from "@/lib/utils";
import { MoveRightIcon } from "lucide-react";
import { Config, Form as FormType } from "@/types";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import {
  Form as FormProvider,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
  FormButton,
} from "@/components/ui/form";
import { jsonToFormData } from "@/lib/utils";

// const formSchema = z.object({
//   email: z.string({
//     required_error: "Email address is required.",
//   }),
//   given_name: z.string().optional(),
//   family_name: z.string().optional(),
//   phone: z.string().optional(),
//   message: z.string().optional(),
//   ohnohoney: z.number().optional(),
// });

// type FormData = z.infer<typeof formSchema>;

interface FormProps extends FormType {
  config: Config;
  modal_id: number;
  before?: string;
  after?: string;
  onSuccess: () => void;
  onClose: () => void;
}

export function ContactForm7({
  config,
  modal_id,
  form_id,
  form_fields,
  before,
  after,
  messages,
  redirects,
  onSuccess,
  onClose,
}: FormProps) {
  const [isSubmitted, setIsSubmitted] = useState(false);
  const form = useForm({
    // resolver: zodResolver(formSchema),
    defaultValues: {
      // given_name: "",
      // family_name: "",
      // phone: "",
      // message: "",
      _wpcf7_unit_tag: modal_id.toString(),
      ohnohoney: 0,
    },
  });

  const onSubmit = async (values: any) => {
    const { ohnohoney, ...rest } = values;

    if (values.ohnohoney === 1) {
      console.log("Bleep boop, you are a bot!");
      return;
    }

    try {
      const formData = jsonToFormData({ _yodel_modal_form: true, ...rest });

      const response = await fetch(
        `${config.baseUrl}/wp-json/contact-form-7/v1/contact-forms/${form_id}/feedback`,
        {
          method: "POST",
          body: formData,
        }
      );

      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const result = await response.json();

      if (result.status !== "mail_sent") {
        throw new Error(result.message);
      }

      if (redirects.success) {
        window.location.href = redirects.success;
      } else {
        setIsSubmitted(true);
        onSuccess();
      }
    } catch (error) {
      if (error instanceof Error) {
        console.error(error.message);
      } else {
        console.error("An unknown error occurred");
      }
    }
  };

  return (
    <div className="yodel-modal__form-container relative w-full">
      {isSubmitted ? (
        <div className="yodel-modal__form-success-message text-center py-12 space-y-6">
          <p
            className="text-inherit text-lg max-w-[280px] mx-auto"
            dangerouslySetInnerHTML={{
              __html: messages.success,
            }}
          />
          <Button
            className="px-12"
            type="button"
            variant="secondary"
            onClick={onClose}
          >
            Close
          </Button>
        </div>
      ) : (
        <div className="w-full space-y-6">
          {before && (
            <div
              className="yodel-modal__form-before prose prose-sm text-inherit max-w-2xl mx-auto"
              dangerouslySetInnerHTML={{
                __html: before,
              }}
            />
          )}
          <FormProvider {...form}>
            <form
              className="yodel-modal__form flex flex-col space-y-8"
              onSubmit={(e) => {
                e.preventDefault();
                form.handleSubmit(onSubmit)(e);
              }}
            >
              <div className="grid gap-4">
                {form_fields.map((formField: any, index: number) => {
                  return (
                    <FormField
                      key={index}
                      control={form.control}
                      name={formField.name}
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>{formField.label}</FormLabel>
                          <FormControl>
                            <>
                              {[
                                "text",
                                "email",
                                "tel",
                                "url",
                                "number",
                              ].includes(formField.type) && (
                                <Input
                                  className="text-foreground"
                                  type={formField.type}
                                  required={formField.required}
                                  autoComplete={formField.options.autocomplete}
                                  {...field}
                                />
                              )}
                              {formField.type === "textarea" && (
                                <Textarea
                                  className="text-foreground"
                                  required={formField.required}
                                  autoComplete={formField.options.autocomplete}
                                  {...field}
                                />
                              )}
                            </>
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  );
                })}
                <Input type="hidden" {...form.register("_wpcf7_unit_tag")} />
                <Input
                  className="hidden"
                  type="checkbox"
                  value={0}
                  tabIndex={-1}
                  autoComplete="off"
                  {...form.register("ohnohoney")}
                />
              </div>
              <div className="yodel-modal__form-buttons grid grid-cols-2 gap-4 sm:justify-end">
                <Button type="button" variant="ghost" onClick={onClose}>
                  No Thanks
                </Button>
                <FormButton className="space-x-2" variant="secondary">
                  <span>
                    {form.formState.isSubmitting ? "Submitting..." : "Submit"}
                  </span>
                  <MoveRightIcon className="w-5 h-5" />
                </FormButton>
              </div>
            </form>
          </FormProvider>
          {after && (
            <div
              className="yodel-modal__form-after prose prose-sm text-inherit text-xs max-w-2xl mx-auto"
              dangerouslySetInnerHTML={{
                __html: after,
              }}
            />
          )}
        </div>
      )}
    </div>
  );
}
