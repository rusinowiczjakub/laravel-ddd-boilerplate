import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import pl from './locales/pl.json';
import en from './locales/en.json';

// Function to detect user's preferred language
const detectUserLanguage = (): string => {
    // 1. Check if user has previously selected a language (localStorage)
    const savedLanguage = localStorage.getItem('preferredLanguage');
    if (savedLanguage) {
        return savedLanguage;
    }

    // 2. Detect language from browser (which often reflects user's location)
    const browserLanguage = navigator.language || navigator.languages?.[0] || 'en';

    // If browser language contains 'pl' (Polish), use Polish, otherwise default to English
    if (browserLanguage.toLowerCase().includes('pl')) {
        return 'pl';
    }

    return 'en';
};

const detectedLanguage = detectUserLanguage();

i18n.use(initReactI18next).init({
    resources: {
        pl: {
            translation: pl,
        },
        en: {
            translation: en,
        },
    },
    lng: detectedLanguage,
    fallbackLng: 'en',
    interpolation: {
        escapeValue: false,
    },
});

// Save language to localStorage whenever it changes
i18n.on('languageChanged', (lng) => {
    localStorage.setItem('preferredLanguage', lng);
});

export default i18n;
