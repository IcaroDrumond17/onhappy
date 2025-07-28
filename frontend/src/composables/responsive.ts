export enum DeviceType {
  Mobile = "mobile",
  Tablet = "tablet",
  Notebook = "notebook",
  Desktop = "desktop",
}

export function detectDeviceType(useUserAgent = false): DeviceType | null {
  if (useUserAgent) {
    const userAgent = navigator.userAgent || navigator.vendor;

    if (/iPhone|iPod|Android.*Mobile|Windows Phone/i.test(userAgent)) {
      return DeviceType.Mobile;
    }

    if (/iPad|Android|Tablet/i.test(userAgent)) {
      return DeviceType.Tablet;
    }
  } else {
    const width = window.innerWidth;

    if (width <= 767) return DeviceType.Mobile;
    if (width <= 1023) return DeviceType.Tablet;
    if (width <= 1200) return DeviceType.Notebook;
    else return DeviceType.Desktop;
  }

  return null;
}

export function isMobile(): boolean {
  return detectDeviceType() === DeviceType.Mobile;
}