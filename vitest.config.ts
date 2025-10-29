import { defineConfig } from 'vitest/config'

export default defineConfig({
    test: {
        environment: 'jsdom',
        include: ['platform/themes/riorelax/assets/js/**/*.test.js'],
        globals: false,
        restoreMocks: true,
        clearMocks: true,
    },
})
