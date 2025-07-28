//https://vue3datepicker.com

interface MultiCalendarsOptions {
  solo?: boolean;
  static?: boolean;
  count?: string | number;
}

interface TextInputOptions {
  enterSubmit?: boolean;
  tabSubmit?: boolean;
  openMenu?: "open" | "toggle" | boolean;
  rangeSeparator?: string;
  selectOnFocus?: boolean;
  format?: string | string[] | ((value: string) => Date | null);
  escClose?: boolean;
}

interface InlineOptions {
  input?: boolean;
}

interface TimeZoneConfig {
  timezone?: string;
  exactMatch?: boolean;
  dateInTz?: string;
  emitTimezone?: string;
  convertModel?: boolean;
}

export interface Props {
  id: string;
  className?: string;
  placeholder?: string;
  name?: string;
  padding?: string;
  width?: string;
  minWidth?: string;
  maxWidth?: string;
  full?: boolean;
  disabled?: boolean;
  error?: boolean;
  range?: boolean;
  multiCalendars?: boolean | number | string | MultiCalendarsOptions;
  monthPicker?: boolean;
  timePicker?: boolean;
  enableTimePicker?: boolean;
  timePickerInline?: boolean;
  is24?: boolean;
  enableMinutes?: boolean;
  enableSeconds?: boolean;
  textInput?: boolean | TextInputOptions;
  inline?: boolean | InlineOptions;
  timezone?: string | TimeZoneConfig;
  locale?: string;
  format?: string;
  selectText?: string;
  cancelText?: string;
  position?: 'left' | 'center' | 'right';
  autoApply?: boolean
}