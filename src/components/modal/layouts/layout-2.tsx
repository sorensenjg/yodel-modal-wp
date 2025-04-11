import { Fragment } from "react";
import {
  Config,
  Settings,
  Button as ButtonType,
  Form as FormType,
} from "@/types";
import { DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Form } from "../../form";

type Layout2Props = {
  config: Config;
  globalSettings: Settings;
  id: number;
  title: string;
  content: string;
  buttons?: ButtonType[];
  form_before?: string;
  form_after?: string;
  form?: FormType;
  background_image?: {
    src: string;
    alt: string;
    width: number;
    height: number;
  };
  onButtonClick: (button: ButtonType) => void;
  onFormSubmitted: (success: boolean | string) => void;
  onClose: () => void;
};

export function Layout2({
  config,
  globalSettings,
  id,
  title,
  content,
  buttons,
  form_before,
  form_after,
  form,
  background_image,
  onButtonClick,
  onFormSubmitted,
  onClose,
}: Layout2Props) {
  return (
    <div className="yodel-modal__row grid">
      <div className="yodel-modal__column flex flex-col justify-center items-center px-6 py-12">
        <DialogHeader className="yodel-modal__header">
          <DialogTitle
            className="yodel-modal__title text-3xl md:text-4xl"
            dangerouslySetInnerHTML={{
              __html: title ?? "",
            }}
          />
          {content && (
            <div
              className="yodel-modal__content prose max-w-none"
              dangerouslySetInnerHTML={{
                __html: content,
              }}
            />
          )}
          {buttons && buttons.length > 0 && (
            <div className="yodel-modal__buttons w-full grid grid-cols-2 gap-4 pt-8">
              {buttons?.map((button, index) => (
                <Button
                  key={index}
                  variant={button.variant}
                  onClick={() => onButtonClick(button)}
                >
                  {button.title}
                </Button>
              ))}
            </div>
          )}
        </DialogHeader>
      </div>
      {form && !form.form_disabled && (
        <div
          className="yodel-modal__column relative flex justify-center items-center px-6 py-12 text-white bg-primary"
          // style={{ backgroundColor: modalSettings.bgColor }}
        >
          {background_image && (
            <Fragment>
              <div
                className="yodel-modal__background-image absolute top-0 left-0 w-full h-full bg-cover bg-center"
                style={{
                  backgroundImage: `url(${background_image.src})`,
                }}
              />
              <div className="yodel-modal__background-overlay absolute top-0 left-0 w-full h-full bg-black/70" />
            </Fragment>
          )}
          <Form
            config={config}
            globalSettings={globalSettings}
            modal_id={id}
            before={form_before}
            after={form_after}
            {...form}
            onSuccess={(success) => onFormSubmitted(success)}
            onClose={onClose}
          />
        </div>
      )}
    </div>
  );
}
