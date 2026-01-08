// Motion component library for Framer Motion animations
// Bold & Dynamic scroll-triggered animations

export { FadeIn } from './fade-in';
export { StaggerContainer, StaggerItem } from './stagger-container';
export { ScrollReveal } from './scroll-reveal';
export { TextReveal, LineReveal } from './text-reveal';
export { FloatingElement, FloatingRotate, PulsingGlow } from './floating-element';
export { Marquee, SimpleMarquee } from './marquee';
export { HoverCard3D, HoverScale, HoverGlow, HoverArrow } from './hover-card';
export { useReducedMotion, useMotionSafe } from './use-reduced-motion';

// Animation constants for consistent timing
export const ANIMATION = {
    duration: {
        fast: 0.2,
        normal: 0.4,
        slow: 0.6,
        verySlow: 0.8,
    },
    stagger: {
        fast: 0.05,
        normal: 0.1,
        slow: 0.15,
    },
    ease: {
        smooth: [0.25, 0.4, 0.25, 1] as const,
        bouncy: [0.68, -0.55, 0.265, 1.55] as const,
        snappy: [0.4, 0, 0.2, 1] as const,
    },
    spring: {
        gentle: { stiffness: 120, damping: 14 },
        bouncy: { stiffness: 300, damping: 10 },
        stiff: { stiffness: 400, damping: 30 },
    },
} as const;
