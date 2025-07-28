import { defineConfig, loadEnv, ConfigEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

import Components from 'unplugin-vue-components/vite'
import AutoImport from 'unplugin-auto-import/vite'

export default ({ mode }: ConfigEnv) => {
  // carregar vars do env
  const env = loadEnv(mode, process.cwd(), '')

  return defineConfig({
    plugins: [
      vue(),
      Components({
        dirs: ['src/components'],
      }),
      AutoImport({
        include: [
          /\.[tj]sx?$/, // .ts, .tsx, .js, .jsx
          /\.vue$/,
          /\.vue\?vue/, // .vue
          /\.md$/, // .md
        ],
        imports: ['vue', 'vue-router', 'pinia'],
        dts: './auto-imports.d.ts',
        dirs: [],
        vueTemplate: true,
      }),
    ],
    base: env.BASE_URL || '/onhappy-teste/',
    resolve: {
      alias: [
        {
          find: '@',
          replacement: resolve(__dirname, 'src'),
        },
      ],
      extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json', '.vue'],
    },
    server: {
      host: true,
      port: 5173,
      strictPort: true,
      watch: {
        usePolling: true,
      },
    },
  })
}
