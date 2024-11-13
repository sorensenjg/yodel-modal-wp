import { useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { z } from "zod";
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

const formSchema = z.object({
  email: z.string({
    required_error: "Email address is required.",
  }),
  given_name: z.string().optional(),
  family_name: z.string().optional(),
  message: z.string().optional(),
});

type FormData = z.infer<typeof formSchema>;

interface FormProps extends FormType {
  config: Config;
  id: number;
  before?: string;
  after?: string;
  onSuccess: () => void;
  onClose: () => void;
}

export function Form({
  config,
  id,
  before,
  after,
  submission_type,
  messages,
  onSuccess,
  onClose,
}: FormProps) {
  const [isSubmitted, setIsSubmitted] = useState(false);
  const form = useForm<FormData>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      given_name: "",
      family_name: "",
      message: "",
    },
  });

  const onSubmit = async (values: FormData) => {
    try {
      const response = await fetch(config.ajaxUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "yodel_form_submit",
          nonce: config.nonce,
          post_id: id.toString(),
          submission_type: submission_type,
          ...values,
          referrer: window.location.href,
        }),
      });

      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const result = await response.json();
      console.log(result);

      if (result.success) {
        setIsSubmitted(true);
        onSuccess();
      } else {
        throw new Error(result.data);
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
    <div className="yodel-wp-modal__form-container relative w-full">
      {isSubmitted ? (
        <div className="yodel-wp-modal__form-success-message text-center py-12 space-y-6">
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
            <p
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
                <div className="grid sm:grid-cols-2 gap-4">
                  <FormField
                    control={form.control}
                    name="given_name"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>First Name</FormLabel>
                        <FormControl>
                          <Input
                            className="text-foreground"
                            type="text"
                            autoComplete="given-name"
                            {...field}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <FormField
                    control={form.control}
                    name="family_name"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Last Name</FormLabel>
                        <FormControl>
                          <Input
                            className="text-foreground"
                            type="text"
                            autoComplete="family-name"
                            {...field}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </div>
                <FormField
                  control={form.control}
                  name="email"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Email</FormLabel>
                      <FormControl>
                        <Input
                          className="text-foreground"
                          type="email"
                          autoComplete="email"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                <FormField
                  control={form.control}
                  name="message"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Message</FormLabel>
                      <FormControl>
                        <Textarea
                          className="text-foreground"
                          autoComplete="off"
                          {...field}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                {/* <Input type="hidden" {...form.register("promo-code")} /> */}
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
