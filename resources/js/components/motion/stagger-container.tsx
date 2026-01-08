'use client';

import { motion, type Variants } from 'framer-motion';
import { type ReactNode } from 'react';

interface StaggerContainerProps {
    children: ReactNode;
    staggerDelay?: number;
    delayChildren?: number;
    className?: string;
    once?: boolean;
}

export function StaggerContainer({
    children,
    staggerDelay = 0.1,
    delayChildren = 0,
    className,
    once = true,
}: StaggerContainerProps) {
    const containerVariants: Variants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                staggerChildren: staggerDelay,
                delayChildren,
            },
        },
    };

    return (
        <motion.div
            initial="hidden"
            whileInView="visible"
            viewport={{ once, margin: '-50px' }}
            variants={containerVariants}
            className={className}
        >
            {children}
        </motion.div>
    );
}

// Child item component for use inside StaggerContainer
interface StaggerItemProps {
    children: ReactNode;
    className?: string;
    direction?: 'up' | 'down' | 'left' | 'right' | 'none';
    distance?: number;
}

export function StaggerItem({
    children,
    className,
    direction = 'up',
    distance = 24,
}: StaggerItemProps) {
    const getOffset = () => {
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

    const itemVariants: Variants = {
        hidden: {
            opacity: 0,
            ...getOffset(),
        },
        visible: {
            opacity: 1,
            x: 0,
            y: 0,
            transition: {
                duration: 0.5,
                ease: [0.25, 0.4, 0.25, 1],
            },
        },
    };

    return (
        <motion.div variants={itemVariants} className={className}>
            {children}
        </motion.div>
    );
}
