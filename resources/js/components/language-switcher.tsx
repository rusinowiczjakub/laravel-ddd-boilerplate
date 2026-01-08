import { Globe } from 'lucide-react';
import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';

interface LanguageSwitcherProps {
    classNames?: string;
}
export function LanguageSwitcher({classNames = ''}: LanguageSwitcherProps) {
    const { i18n } = useTranslation();
    const [isOpen, setIsOpen] = useState(false);

    const languages = [
        { code: 'pl', label: 'Polski', flag: '🇵🇱' },
        { code: 'en', label: 'English', flag: '🇬🇧' },
    ];

    const currentLanguage = languages.find((lang) => lang.code === i18n.language) || languages[0];

    const changeLanguage = (code: string) => {
        i18n.changeLanguage(code);
        setIsOpen(false);
    };

    return (
        <div className={`relative ${classNames}`}>
            <button
                onClick={() => setIsOpen(!isOpen)}
                className="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                type="button"
            >
                <Globe className="h-4 w-4" />
                <span className="hidden sm:inline">{currentLanguage.label}</span>
                <span className="sm:hidden">{currentLanguage.flag}</span>
            </button>

            {isOpen && (
                <>
                    <div className="fixed inset-0 z-10" onClick={() => setIsOpen(false)} />
                    <div className="absolute right-0 top-full z-20 mt-2 w-40 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg">
                        {languages.map((lang) => (
                            <button
                                key={lang.code}
                                onClick={() => changeLanguage(lang.code)}
                                className={`flex w-full items-center gap-3 px-4 py-2 text-left text-sm transition hover:bg-slate-50 ${
                                    i18n.language === lang.code ? 'bg-indigo-50 font-semibold text-indigo-600' : 'text-slate-700'
                                }`}
                                type="button"
                            >
                                <span className="text-lg">{lang.flag}</span>
                                <span>{lang.label}</span>
                            </button>
                        ))}
                    </div>
                </>
            )}
        </div>
    );
}
