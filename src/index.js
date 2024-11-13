import domReady from "@wordpress/dom-ready";
import { createRoot } from "@wordpress/element";
import App from "./app";

domReady(() => {
  if (!yodelWp.config.containerId) {
    console.error("[Yodel]: Container ID not provided");
    return;
  }

  const container = document.getElementById(yodelWp.config.containerId);

  if (!container) {
    console.error("[Yodel]: Container not found");
    return;
  }

  const root = createRoot(container);

  root.render(<App {...yodelWp} />);
});
