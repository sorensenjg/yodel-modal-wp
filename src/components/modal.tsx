import { Fragment, useState, useEffect, useRef, useCallback } from "react";
import Cookies from "js-cookie";
import { Config, Modal as ModalType, Button as ButtonType } from "@/types";
import { cn, applyCustomThemeVariables } from "@/lib/utils";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Form } from "./form";

interface ModalProps extends ModalType {
  config: Config;
}

export function Modal({
  config,
  id,
  status,
  layout,
  columns,
  image,
  title,
  content: initialContent,
  buttons,
  form_before,
  form_after,
  form,
  background_image,
  settings,
}: ModalProps) {
  const containerRef = useRef(null);
  const [isVisible, setIsVisible] = useState(false);
  const [formSubmitted, setFormSubmitted] = useState(false);
  const [content, setContent] = useState(initialContent ?? "");

  useEffect(() => {
    if (containerRef.current && settings.theme.color_variables) {
      applyCustomThemeVariables(
        containerRef.current,
        settings.theme.color_variables
      );
    }
  }, []);

  const isExitIntent = settings.display.initialization === "exit_intent";

  useEffect(() => {
    const handleShortcodeButtonClick = (e: MouseEvent) => {
      if (
        e.target instanceof HTMLElement &&
        e.target.classList.contains("yodel-wp-button")
      ) {
        const button_id = e.target.getAttribute("data-id");
        const button_type = e.target.getAttribute("data-type");

        if (button_id === id.toString() && button_type === "modal") {
          setIsVisible(true);
        }
      }
    };

    const handleCF7FormSubmit = (e: any) => {
      if (!form?.form_id) return;

      const id = e.detail.contactFormId;
      const response = e.detail.apiResponse;

      if (id.toString() === form.form_id) {
        if (response.status === "mail_sent") {
          // const responseOutput = document.querySelector(
          //   ".wpcf7-response-output"
          // );

          // if (responseOutput) {
          //   responseOutput.remove();
          // }

          // if (initialContent.trim() === "") {
          //   setContent(`<p class="text-center">${response.message}</p>`);
          // }

          setIsVisible(true);
        }
      }
    };

    document.addEventListener("click", handleShortcodeButtonClick);

    if (form?.form_type === "cf7_form" && form?.form_disabled) {
      document.addEventListener("wpcf7submit", handleCF7FormSubmit);
    }

    return () => {
      document.removeEventListener("click", handleShortcodeButtonClick);
      if (form?.form_type === "cf7_form" && form?.form_disabled) {
        document.removeEventListener("wpcf7submit", handleCF7FormSubmit);
      }
    };
  }, []);

  const handleExitIntent = useCallback(
    (e: MouseEvent) => {
      if (e.clientY <= 10 && !isVisible) {
        setIsVisible(true);
      }
    },
    [isVisible]
  );

  useEffect(() => {
    if (!config.isUserAdmin && status === "private") return;

    const lastDismissed = Cookies.get(`yodel-wp-${id}-dismissed-at`);
    const currentTime = new Date().getTime();
    const dismissalExpiration = settings.display.dismissal_expiration;

    if (settings.display.initialization === "button") {
      return;
    }

    if (
      dismissalExpiration === undefined ||
      dismissalExpiration === null ||
      dismissalExpiration === 0 ||
      !lastDismissed ||
      (dismissalExpiration > 0 &&
        currentTime - parseInt(lastDismissed) >
          dismissalExpiration * 24 * 60 * 60 * 1000)
    ) {
      if (isExitIntent) {
        document.addEventListener("mousemove", handleExitIntent);
      } else {
        const timer = setTimeout(() => {
          setIsVisible(true);
        }, settings.display.delay * 1000);
        return () => clearTimeout(timer);
      }
    }

    return () => {
      if (isExitIntent) {
        document.removeEventListener("mousemove", handleExitIntent);
      }
    };
  }, [isExitIntent, handleExitIntent]);

  const handleDismiss = () => {
    const dismissalExpiration = settings.display.dismissal_expiration;

    if (dismissalExpiration === undefined || dismissalExpiration === null) {
      // Don't set a cookie if expiration is empty
      return;
    } else if (dismissalExpiration === 0) {
      // Set a session-only cookie
      Cookies.set(
        `yodel-wp-${id}-dismissed-at`,
        new Date().getTime().toString()
      );
    } else {
      // Set a cookie with the specified expiration in days
      Cookies.set(
        `yodel-wp-${id}-dismissed-at`,
        new Date().getTime().toString(),
        { expires: dismissalExpiration / 24 }
      );
    }
  };

  const handleClose = () => {
    setIsVisible(false);
    handleDismiss();
  };

  const handleButtonClick = (button: ButtonType) => {
    if (button.type === "link") {
      handleDismiss();
      window.open(button.url, button.target);
    } else if (button.type === "close") {
      handleClose();
    }
  };

  const handleModalEvents = useCallback(
    (e: any) => {
      if (!form || form.form_disabled || formSubmitted) {
        handleClose();
      } else {
        e.preventDefault();
      }
    },
    [form, formSubmitted, handleClose]
  );

  const isDisplayedGlobally = settings.display.displayed_at.length === 0;
  const isDisplayedOnCurrentPage = settings.display.displayed_at.includes(
    window.location.pathname
  );
  const isTargetPage = buttons?.some((button) =>
    window.location.pathname.includes(button.url)
  );

  if ((!isDisplayedGlobally && !isDisplayedOnCurrentPage) || isTargetPage) {
    return null;
  }

  return (
    <div ref={containerRef} className={settings.theme.color_scheme}>
      <Dialog open={isVisible} onOpenChange={handleClose}>
        <DialogContent
          id={`yodel-wp-${id}`}
          className="yodel-wp z-[9999] w-full h-screen max-w-none p-0 border-none overflow-auto md:max-w-4xl md:h-auto md:overflow-hidden"
          onEscapeKeyDown={handleModalEvents}
          onPointerDownOutside={handleModalEvents}
          onInteractOutside={handleModalEvents}
          aria-describedby={undefined}
        >
          <div
            className={cn(
              "yodel-modal__row grid",
              columns === 2 && "md:grid-cols-2"
            )}
          >
            <div className="yodel-modal__column flex flex-col justify-center items-center px-6 py-12">
              <DialogHeader className="yodel-modal__header">
                {image && (
                  <img
                    className="yodel-modal__image"
                    src={image.src}
                    alt={image.alt}
                  />
                )}
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
                        onClick={() => handleButtonClick(button)}
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
                  id={id}
                  before={form_before}
                  after={form_after}
                  {...form}
                  onSuccess={() => setFormSubmitted(true)}
                  onClose={handleClose}
                />
              </div>
            )}
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
