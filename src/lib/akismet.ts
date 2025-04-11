// https://akismet.com/developers/detailed-docs/comment-check/

export function getAkismetFieldValues(form_fields: any, values: any) {
  return form_fields
    .filter((field: any) => field.options.akismet)
    .reduce((acc: Record<string, any>, field: any) => {
      acc[`comment_${field.options.akismet}`] = values[field.name];
      return acc;
    }, {});
}

export async function checkSpam(comment: any) {
  const config = yodelWp.config;

  const response = await fetch(config.ajaxUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      ...comment,
      action: "yodel_akismet_check_spam",
      nonce: config.nonce,
      permalink: window.location.href,
      user_agent: navigator.userAgent,
    }),
  });

  if (!response.ok) {
    throw new Error(response.statusText);
  }

  const result = await response.json();

  return result;
}
