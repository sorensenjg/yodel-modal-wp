import { Button as ButtonType } from "@/types";
import { Button } from "@/components/ui/button";
import { DialogHeader, DialogTitle } from "@/components/ui/dialog";

type Layout1Props = {
  image?: {
    src: string;
    alt: string;
    width: number;
    height: number;
  };
  title: string;
  content: string;
  buttons?: ButtonType[];
  onButtonClick: (button: ButtonType) => void;
};

export function Layout1({
  image,
  title,
  content,
  buttons,
  onButtonClick,
}: Layout1Props) {
  return (
    <div className="yodel-modal__row grid">
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
                  onClick={() => onButtonClick(button)}
                >
                  {button.title}
                </Button>
              ))}
            </div>
          )}
        </DialogHeader>
      </div>
    </div>
  );
}
