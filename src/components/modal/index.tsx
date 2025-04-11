import { useState, useEffect, useRef, useCallback } from "react";
import Cookies from "js-cookie";
import {
  Config,
  Settings,
  Modal as ModalType,
  Button as ButtonType,
} from "@/types";
import { applyCustomThemeVariables } from "@/lib/utils";
import { Dialog, DialogContent } from "@/components/ui/dialog";
import { Layout1, Layout2, Layout3, Layout4 } from "./layouts";

interface ModalProps extends ModalType {
  config: Config;
  globalSettings: Settings;
}

export function Modal({
  config,
  globalSettings,
  id,
  status,
  layout,
  columns,
  image,
  title,
  content,
  buttons,
  form_before,
  form_after,
  form,
  background_image,
  settings,
}: ModalProps) {
  const containerRef = useRef(null);
  const [isVisible, setIsVisible] = useState(false);
  const [visibilityBypass, setVisibilityBypass] = useState(false);
  const [formSubmitted, setFormSubmitted] = useState<boolean | string>(false);

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

  useEffect(() => {
    if (containerRef.current && settings.theme.color_variables) {
      applyCustomThemeVariables(
        containerRef.current,
        settings.theme.color_variables
      );
    }
  }, []);

  useEffect(() => {
    if (typeof formSubmitted === "string") {
      handleDismiss();
      window.location.href = formSubmitted;
    }
  }, [formSubmitted]);

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
          if (!visibilityBypass) {
            setIsVisible(true);
          }
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
    const dismissalExpiration = settings.display.dismissal_expiration || null;
    const cookieName = `yodel-wp-${id}-dismissed-at`;
    const currentTime = new Date().getTime().toString();

    if (dismissalExpiration === undefined || dismissalExpiration === null) {
      setVisibilityBypass(true);
      return; // No cookie if expiration is empty
    }

    const cookieOptions =
      dismissalExpiration === 0
        ? undefined // Session cookie
        : { expires: dismissalExpiration / 24 }; // Cookie with expiration in days

    Cookies.set(cookieName, currentTime, cookieOptions);
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
      console.log(formSubmitted);

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
  const isTargetPage = buttons?.some((button) => {
    return window.location.pathname.includes(button.url);
  });

  if ((!isDisplayedGlobally && !isDisplayedOnCurrentPage) || isTargetPage) {
    return null;
  }

  return (
    <div ref={containerRef} className={settings.theme.color_scheme}>
      <Dialog open={isVisible} onOpenChange={handleClose}>
        <DialogContent
          id={`yodel-wp-${id}`}
          className="yodel-modal z-[9999] w-full h-screen max-w-none p-0 border-none overflow-auto md:max-w-4xl md:h-auto md:overflow-hidden"
          onEscapeKeyDown={handleModalEvents}
          onPointerDownOutside={handleModalEvents}
          onInteractOutside={handleModalEvents}
          aria-describedby={undefined}
        >
          {layout === "layout_1" && (
            <Layout1
              image={image}
              title={title}
              content={content}
              buttons={buttons}
              onButtonClick={handleButtonClick}
            />
          )}
          {layout === "layout_2" && (
            <Layout2
              config={config}
              globalSettings={globalSettings}
              id={id}
              title={title}
              content={content}
              buttons={buttons}
              form={form}
              onButtonClick={handleButtonClick}
              onFormSubmitted={(success) => setFormSubmitted(success)}
              onClose={handleClose}
            />
          )}
          {layout === "layout_3" && (
            <Layout3
              config={config}
              globalSettings={globalSettings}
              id={id}
              title={title}
              content={content}
              buttons={buttons}
              form={form}
              onButtonClick={handleButtonClick}
              onFormSubmitted={(success) => setFormSubmitted(success)}
              onClose={handleClose}
            />
          )}
          {layout === "layout_4" && (
            <Layout4
              title={title}
              content={content}
              buttons={buttons}
              image={image}
              onButtonClick={handleButtonClick}
            />
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
