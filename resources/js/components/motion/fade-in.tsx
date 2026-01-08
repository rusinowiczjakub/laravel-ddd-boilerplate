'use client';

import { motion, type Variants } from 'framer-motion';
import { type ReactNode } from 'react';

interface FadeInProps {
    children: ReactNode;
    delay?: number;
    duration?: number;
    direction?: 'up' | 'down' | 'left' | 'right' | 'none';
    distance?: number;
    className?: string;
    once?: boolean;
}

const getDirectionOffset = (direction: FadeInProps['direction'], distance: number) => {
    switch (direction) {
        case 'up':
            return { y: distance };
        case 'down':
            return { y: -distance };
        case 'left':
            return { x: distance };
        case 'right':
            return { x: -distance };
        default:
            return {};
    }
};

export function FadeIn({
    children,
    delay = 0,
    duration = 0.5,
    direction = 'up',
    distance = 24,
    className,
    once = true,
}: FadeInProps) {
    const variants: Variants = {
        hidden: {
            opacity: 0,
            ...getDirectionOffset(direction, distance),
        },
        visible: {
            opacity: 1,
            x: 0,
            y: 0,
            transition: {
                duration,
                delay,
                ease: [0.25, 0.4, 0.25, 1],
            },
        },
    };

    return (
        <motion.div
            initial="hidden"
            whileInView="visible"
            viewport={{ once, margin: '-50px' }}
            variants={variants}
            className={className}
        >
            {children}
        </motion.div>
    );
}
