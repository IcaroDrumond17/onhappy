import * as momentTemp from "moment";
const moment = momentTemp["default"];

interface FormatDate {
  date: string | Date;
  hour?: string | Date;
  format?: string;
  locale?: string;
}

export function formatDateToBR({
  date,
  hour,
  format,
  locale = "pt-br",
}: FormatDate): string | Date | null {
  if (date && hour)
    return moment(`${date} ${hour}`)
      .locale(locale)
      .format(format || "DD/MM/YYYY [às] HH:mm");

  if (date && !hour)
    return moment(date)
      .locale(locale)
      .format(format || "DD/MM/YYYY");

  return null;
}