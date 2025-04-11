import { useEffect, useRef } from "react";
import { Config, Settings, Modal as ModalType } from "@/types";
import { cn, applyCustomThemeVariables } from "@/lib/utils";
import { Modal } from "./components/modal";

import "@/styles/globals.css";

interface AppProps {
  config: Config;
  settings: Settings;
  modals: ModalType[];
}

export default function App({ config, settings, modals }: AppProps) {
  const appRef = useRef(null);

  useEffect(() => {
    if (appRef.current && settings.theme.color_variables) {
      applyCustomThemeVariables(appRef.current, settings.theme.color_variables);
    }
  }, []);

  return (
    <div ref={appRef} className={cn("yodel-app", settings.theme.color_scheme)}>
      {modals.map((modal) => (
        <Modal
          key={`modal-${modal.id}`}
          config={config}
          globalSettings={settings}
          {...modal}
        />
      ))}
    </div>
  );
}
