'use client';

import { motion } from 'framer-motion';
import { type ReactNode } from 'react';

interface FloatingElementProps {
    children: ReactNode;
    amplitude?: number;
    duration?: number;
    delay?: number;
    className?: string;
}

export function FloatingElement({
    children,
    amplitude = 8,
    duration = 4,
    delay = 0,
    className,
}: FloatingElementProps) {
    return (
        <motion.div
            animate={{
                y: [-amplitude, amplitude, -amplitude],
            }}
            transition={{
                duration,
                delay,
                repeat: Infinity,
                ease: 'easeInOut',
            }}
            className={className}
        >
            {children}
        </motion.div>
    );
}

// Floating with rotation
interface FloatingRotateProps {
    children: ReactNode;
    amplitude?: number;
    rotation?: number;
    duration?: number;
    delay?: number;
    className?: string;
}

export function FloatingRotate({
    children,
    amplitude = 6,
    rotation = 3,
    duration = 5,
    delay = 0,
    className,
}: FloatingRotateProps) {
    return (
        <motion.div
            animate={{
                y: [-amplitude, amplitude, -amplitude],
                rotate: [-rotation, rotation, -rotation],
            }}
            transition={{
                duration,
                delay,
                repeat: Infinity,
                ease: 'easeInOut',
            }}
            className={className}
        >
            {children}
        </motion.div>
    );
}

// Pulsing glow effect
interface PulsingGlowProps {
    children: ReactNode;
    className?: string;
    glowColor?: string;
    duration?: number;
}

export function PulsingGlow({
    children,
    className,
    glowColor = 'rgba(16, 185, 129, 0.4)',
    duration = 2,
}: PulsingGlowProps) {
    return (
        <motion.div
            animate={{
                boxShadow: [
                    `0 0 20px 0px ${glowColor}`,
                    `0 0 40px 10px ${glowColor}`,
                    `0 0 20px 0px ${glowColor}`,
                ],
            }}
            transition={{
                duration,
                repeat: Infinity,
                ease: 'easeInOut',
            }}
            className={className}
        >
            {children}
        </motion.div>
    );
}
