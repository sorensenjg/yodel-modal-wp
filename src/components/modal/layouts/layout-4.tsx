import { Button as ButtonType } from "@/types";
import { Button } from "@/components/ui/button";
import { DialogHeader, DialogTitle } from "@/components/ui/dialog";

type Layout4Props = {
  title: string;
  content: string;
  buttons?: ButtonType[];
  image?: {
    src: string;
    alt: string;
    width: number;
    height: number;
  };
  onButtonClick: (button: ButtonType) => void;
};

export function Layout4({
  title,
  content,
  buttons,
  image,
  onButtonClick,
}: Layout4Props) {
  return (
    <div className="yodel-modal__row grid md:grid-cols-2">
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
      <div className="yodel-modal__column relative flex justify-center items-center px-6 py-12 text-white bg-primary">
        {image && (
          <img className="yodel-modal__image" src={image.src} alt={image.alt} />
        )}
      </div>
    </div>
  );
}
