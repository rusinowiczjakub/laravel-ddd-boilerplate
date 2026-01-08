'use client';

import { motion } from 'framer-motion';
import { type ReactNode } from 'react';

interface MarqueeProps {
    children: ReactNode;
    speed?: number;
    direction?: 'left' | 'right';
    pauseOnHover?: boolean;
    className?: string;
    repeat?: number;
}

export function Marquee({
    children,
    speed = 30,
    direction = 'left',
    pauseOnHover = true,
    className,
    repeat = 4,
}: MarqueeProps) {
    const directionMultiplier = direction === 'left' ? -1 : 1;

    return (
        <div
            className={`group relative flex overflow-hidden ${className}`}
            style={{
                maskImage: 'linear-gradient(to right, transparent, black 10%, black 90%, transparent)',
                WebkitMaskImage: 'linear-gradient(to right, transparent, black 10%, black 90%, transparent)',
            }}
        >
            {Array.from({ length: repeat }).map((_, i) => (
                <motion.div
                    key={i}
                    className={`flex shrink-0 items-center gap-12 ${pauseOnHover ? 'group-hover:[animation-play-state:paused]' : ''}`}
                    animate={{
                        x: [0, directionMultiplier * -100 + '%'],
                    }}
                    transition={{
                        x: {
                            duration: speed,
                            repeat: Infinity,
                            ease: 'linear',
                        },
                    }}
                    style={{
                        animationPlayState: 'running',
                    }}
                >
                    {children}
                </motion.div>
            ))}
        </div>
    );
}

// Simple infinite scroll marquee with CSS animation fallback
interface SimpleMarqueeProps {
    children: ReactNode;
    duration?: number;
    direction?: 'left' | 'right';
    pauseOnHover?: boolean;
    className?: string;
}

export function SimpleMarquee({
    children,
    duration = 40,
    direction = 'left',
    pauseOnHover = true,
    className,
}: SimpleMarqueeProps) {
    return (
        <div
            className={`group relative flex overflow-hidden ${className}`}
            style={{
                maskImage: 'linear-gradient(to right, transparent, black 10%, black 90%, transparent)',
                WebkitMaskImage: 'linear-gradient(to right, transparent, black 10%, black 90%, transparent)',
            }}
        >
            <div
                className={`flex shrink-0 animate-marquee items-center gap-12 ${pauseOnHover ? 'group-hover:[animation-play-state:paused]' : ''}`}
                style={{
                    ['--marquee-duration' as string]: `${duration}s`,
                    animationDirection: direction === 'right' ? 'reverse' : 'normal',
                }}
            >
                {children}
            </div>
            <div
                className={`flex shrink-0 animate-marquee items-center gap-12 ${pauseOnHover ? 'group-hover:[animation-play-state:paused]' : ''}`}
                style={{
                    ['--marquee-duration' as string]: `${duration}s`,
                    animationDirection: direction === 'right' ? 'reverse' : 'normal',
                }}
                aria-hidden="true"
            >
                {children}
            </div>
        </div>
    );
}
