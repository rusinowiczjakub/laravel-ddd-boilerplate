'use client';

import { motion, useMotionValue, useSpring, useTransform } from 'framer-motion';
import { type MouseEvent, type ReactNode, useRef } from 'react';

interface HoverCard3DProps {
    children: ReactNode;
    className?: string;
    intensity?: number;
    perspective?: number;
    scale?: number;
}

export function HoverCard3D({
    children,
    className,
    intensity = 10,
    perspective = 1000,
    scale = 1.02,
}: HoverCard3DProps) {
    const ref = useRef<HTMLDivElement>(null);

    const x = useMotionValue(0);
    const y = useMotionValue(0);

    const springConfig = { stiffness: 300, damping: 30 };
    const rotateX = useSpring(useTransform(y, [-0.5, 0.5], [intensity, -intensity]), springConfig);
    const rotateY = useSpring(useTransform(x, [-0.5, 0.5], [-intensity, intensity]), springConfig);

    const handleMouseMove = (e: MouseEvent<HTMLDivElement>) => {
        if (!ref.current) return;

        const rect = ref.current.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;

        x.set((e.clientX - centerX) / rect.width);
        y.set((e.clientY - centerY) / rect.height);
    };

    const handleMouseLeave = () => {
        x.set(0);
        y.set(0);
    };

    return (
        <motion.div
            ref={ref}
            onMouseMove={handleMouseMove}
            onMouseLeave={handleMouseLeave}
            style={{
                perspective,
                rotateX,
                rotateY,
                transformStyle: 'preserve-3d',
            }}
            whileHover={{ scale }}
            transition={{ duration: 0.2 }}
            className={className}
        >
            {children}
        </motion.div>
    );
}

// Simpler scale + glow hover effect
interface HoverScaleProps {
    children: ReactNode;
    className?: string;
    scale?: number;
}

export function HoverScale({ children, className, scale = 1.02 }: HoverScaleProps) {
    return (
        <motion.div
            whileHover={{ scale }}
            whileTap={{ scale: 0.98 }}
            transition={{ duration: 0.2 }}
            className={className}
        >
            {children}
        </motion.div>
    );
}

// Hover with background glow
interface HoverGlowProps {
    children: ReactNode;
    className?: string;
    glowColor?: string;
}

export function HoverGlow({
    children,
    className,
    glowColor = 'rgba(16, 185, 129, 0.2)',
}: HoverGlowProps) {
    return (
        <motion.div
            whileHover={{
                boxShadow: `0 0 30px 0px ${glowColor}`,
            }}
            transition={{ duration: 0.3 }}
            className={className}
        >
            {children}
        </motion.div>
    );
}

// Slide arrow on hover (for links/buttons)
interface HoverArrowProps {
    children: ReactNode;
    className?: string;
}

export function HoverArrow({ children, className }: HoverArrowProps) {
    return (
        <motion.div
            className={`group inline-flex items-center gap-2 ${className}`}
            whileHover="hover"
        >
            {children}
            <motion.span
                variants={{
                    hover: { x: 4 },
                }}
                transition={{ duration: 0.2 }}
            >
                →
            </motion.span>
        </motion.div>
    );
}
