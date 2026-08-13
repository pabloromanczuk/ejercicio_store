import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createVuetify } from 'vuetify';
import 'vuetify/styles';
import App from './App.vue';

const vuetify = createVuetify({
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                colors: {
                    primary: '#B3541E',
                    'primary-darken-1': '#93430F',
                    secondary: '#75716B',
                    danger: '#B3261E',
                    background: '#F7F7F5',
                    surface: '#FFFFFF',
                },
            },
            dark: {
                colors: {
                    primary: '#D98A4E',
                    'primary-darken-1': '#B3541E',
                    secondary: '#A09B94',
                    danger: '#E5736B',
                    background: '#121212',
                    surface: '#1E1E1E',
                    'on-surface': '#E0DED8',
                },
            },
        },
    },
});

createApp(App)
    .use(createPinia())
    .use(vuetify)
    .mount('#app');
