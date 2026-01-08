import { useState, useEffect, useRef } from 'react';
import { MessageCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import { FeedbackModal } from './feedback-modal';

export function FeedbackButton() {
    const [isVisible, setIsVisible] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const lastScrollY = useRef(0);

    useEffect(() => {
        const handleScroll = () => {
            const currentScrollY = window.scrollY;

            if (currentScrollY < 100) {
                // Near top - always show
                setIsVisible(true);
            } else if (currentScrollY < lastScrollY.current) {
                // Scrolling up - show
                setIsVisible(true);
            } else if (currentScrollY > lastScrollY.current) {
                // Scrolling down - hide
                setIsVisible(false);
            }

            lastScrollY.current = currentScrollY;
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    return (
        <>
            <button
                onClick={() => setIsModalOpen(true)}
                className={cn(
                    'fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:bg-emerald-500 hover:shadow-xl',
                    isVisible ? 'translate-y-0 opacity-100' : 'translate-y-20 opacity-0 pointer-events-none'
                )}
                aria-label="Send Feedback"
            >
                <MessageCircle className="h-6 w-6" />
            </button>

            <FeedbackModal open={isModalOpen} onOpenChange={setIsModalOpen} />
        </>
    );
}
