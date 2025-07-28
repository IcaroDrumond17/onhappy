export interface SelectProps<
  Option,
  Reduce,
  Label,
  Key,
  Create,
  Dropdown,
  Args
> {
  id?: string;
  label: string;
  options: Option[];
  loading?: boolean;
  error?: string;
  maxLength?: number;
  placeholder?: string;
  className?: string;
  width?: string;
  minWidth?: string;
  maxWidth?: string;
  noOptionsTitle?: string;
  allOptionsSelected?: string;
  disabled?: boolean;
  multiple?: boolean;
  clearable?: boolean;
  taggable?: boolean;
  filterable?: boolean;
  full?: boolean;
  closeOnSelect?: boolean;
  deselectFromDropdown?: boolean;
  pushTags?: boolean;
  searchable?: boolean;
  nowrap?: boolean;
  noAllSelect?: boolean;
  hideLabelOpenSelect?: boolean;
  focus?: boolean;

  reduce?: () => Reduce;
  getOptionLabel?: () => Label;
  getOptionKey?: () => Key;
  createOption?: () => Create;
  // eslint-disable-next-line no-unused-vars
  dropdownShouldOpen?: (args: Args) => Dropdown;
}