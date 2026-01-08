import { useEffect, useState } from 'react';

/**
 * Hook to detect user's reduced motion preference.
 * Returns true if user prefers reduced motion.
 */
export function useReducedMotion(): boolean {
    const [prefersReducedMotion, setPrefersReducedMotion] = useState(false);

    useEffect(() => {
        // Check if we're in a browser environment
        if (typeof window === 'undefined') return;

        const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        setPrefersReducedMotion(mediaQuery.matches);

        const handler = (event: MediaQueryListEvent) => {
            setPrefersReducedMotion(event.matches);
        };

        mediaQuery.addEventListener('change', handler);
        return () => mediaQuery.removeEventListener('change', handler);
    }, []);

    return prefersReducedMotion;
}

/**
 * Returns animation variants that respect reduced motion preference.
 * If reduced motion is preferred, returns instant transitions.
 */
export function useMotionSafe<T extends Record<string, unknown>>(
    variants: T,
    reducedVariants?: Partial<T>
): T {
    const prefersReducedMotion = useReducedMotion();

    if (prefersReducedMotion && reducedVariants) {
        return { ...variants, ...reducedVariants } as T;
    }

    if (prefersReducedMotion) {
        // Return variants with instant transitions
        return Object.fromEntries(
            Object.entries(variants).map(([key, value]) => [
                key,
                typeof value === 'object' && value !== null
                    ? { ...value, transition: { duration: 0 } }
                    : value,
            ])
        ) as T;
    }

    return variants;
}
