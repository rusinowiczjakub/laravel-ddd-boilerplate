'use client';

import { motion, useInView, type Variants } from 'framer-motion';
import { type ReactNode, useRef, useMemo } from 'react';

interface TextRevealProps {
    children: string;
    as?: 'h1' | 'h2' | 'h3' | 'h4' | 'p' | 'span';
    mode?: 'words' | 'characters';
    staggerDelay?: number;
    className?: string;
    once?: boolean;
    delay?: number;
}

const containerVariants: Variants = {
    hidden: { opacity: 1 },
    visible: (staggerDelay: number) => ({
        opacity: 1,
        transition: {
            staggerChildren: staggerDelay,
        },
    }),
};

const itemVariants: Variants = {
    hidden: {
        opacity: 0,
        y: 20,
    },
    visible: {
        opacity: 1,
        y: 0,
        transition: {
            duration: 0.4,
            ease: [0.25, 0.4, 0.25, 1],
        },
    },
};

export function TextReveal({
    children,
    as: Component = 'p',
    mode = 'words',
    staggerDelay = 0.08,
    className,
    once = true,
    delay = 0,
}: TextRevealProps) {
    const ref = useRef<HTMLDivElement>(null);
    const isInView = useInView(ref, { once, margin: '-50px' });

    const items = useMemo(() => {
        return mode === 'words' ? children.split(' ') : children.split('');
    }, [children, mode]);

    const separator = mode === 'words' ? '\u00A0' : '';

    return (
        <motion.div
            ref={ref}
            initial="hidden"
            animate={isInView ? 'visible' : 'hidden'}
            variants={containerVariants}
            custom={staggerDelay}
            transition={{ delayChildren: delay }}
            className={className}
            aria-label={children}
        >
            <Component className="inline">
                {items.map((item, index) => (
                    <motion.span
                        key={`${item}-${index}`}
                        variants={itemVariants}
                        className="inline-block"
                    >
                        {item}
                        {index < items.length - 1 && separator}
                    </motion.span>
                ))}
            </Component>
        </motion.div>
    );
}

// Simpler line-by-line reveal for multi-line text
interface LineRevealProps {
    children: ReactNode;
    className?: string;
    delay?: number;
    once?: boolean;
}

export function LineReveal({ children, className, delay = 0, once = true }: LineRevealProps) {
    const ref = useRef<HTMLDivElement>(null);
    const isInView = useInView(ref, { once, margin: '-50px' });

    return (
        <motion.div
            ref={ref}
            initial={{ opacity: 0, y: 20 }}
            animate={isInView ? { opacity: 1, y: 0 } : { opacity: 0, y: 20 }}
            transition={{
                duration: 0.5,
                delay,
                ease: [0.25, 0.4, 0.25, 1],
            }}
            className={className}
        >
            {children}
        </motion.div>
    );
}
