import type { Toasted } from 'vue-toasted'

declare module 'vue' {
  interface ComponentCustomProperties {
    $toasted: Toasted
  }
}

declare module '@hoppscotch/vue-toasted' {
  const plugin: any;
  export default plugin;

  export function useToasted(): {
    show: (text: string, options?: any) => void;
    success: (text: string, options?: any) => void;
    error: (text: string, options?: any) => void;
    clear: () => void;
  };
}