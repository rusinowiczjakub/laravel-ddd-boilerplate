'use client';

import { motion, useInView, type Variants } from 'framer-motion';
import { type ReactNode, useRef } from 'react';

interface ScrollRevealProps {
    children: ReactNode;
    className?: string;
    threshold?: number;
    once?: boolean;
    animation?: 'fade' | 'slide' | 'scale' | 'slideScale';
    delay?: number;
    duration?: number;
}

const animations: Record<string, Variants> = {
    fade: {
        hidden: { opacity: 0 },
        visible: { opacity: 1 },
    },
    slide: {
        hidden: { opacity: 0, y: 40 },
        visible: { opacity: 1, y: 0 },
    },
    scale: {
        hidden: { opacity: 0, scale: 0.95 },
        visible: { opacity: 1, scale: 1 },
    },
    slideScale: {
        hidden: { opacity: 0, y: 40, scale: 0.95 },
        visible: { opacity: 1, y: 0, scale: 1 },
    },
};

export function ScrollReveal({
    children,
    className,
    threshold = 0.2,
    once = true,
    animation = 'slide',
    delay = 0,
    duration = 0.6,
}: ScrollRevealProps) {
    const ref = useRef<HTMLDivElement>(null);
    const isInView = useInView(ref, { once, amount: threshold });

    const variants = animations[animation];

    return (
        <motion.div
            ref={ref}
            initial="hidden"
            animate={isInView ? 'visible' : 'hidden'}
            variants={variants}
            transition={{
                duration,
                delay,
                ease: [0.25, 0.4, 0.25, 1],
            }}
            className={className}
        >
            {children}
        </motion.div>
    );
}
