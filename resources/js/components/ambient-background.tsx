import { useEffect, useRef } from 'react';

interface AmbientBackgroundProps {
    imageUrl: string;
    className?: string;
}

export default function AmbientBackground({ imageUrl, className = '' }: AmbientBackgroundProps) {
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const canvas = canvasRef.current;
        const container = containerRef.current;

        if (!canvas || !container) {
            console.log('AmbientBackground: Canvas or container ref not found');
            return;
        }

        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        if (!ctx) {
            console.log('AmbientBackground: Could not get canvas context');
            return;
        }

        // Set canvas to match container size
        const updateCanvasSize = () => {
            const rect = container.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
        };

        updateCanvasSize();

        console.log('AmbientBackground: Loading image:', imageUrl);

        // Load the image
        const img = new Image();
        // Remove crossOrigin for local storage URLs
        if (!imageUrl.startsWith('http://localhost')) {
            img.crossOrigin = 'anonymous';
        }

        img.onload = () => {
            console.log('AmbientBackground: Image loaded successfully', {
                imageWidth: img.width,
                imageHeight: img.height,
                canvasWidth: canvas.width,
                canvasHeight: canvas.height
            });

            // Calculate dimensions to cover entire canvas
            const scale = Math.max(canvas.width / img.width, canvas.height / img.height) * 1.2;
            const x = (canvas.width - img.width * scale) / 2;
            const y = (canvas.height - img.height * scale) / 2;

            console.log('AmbientBackground: Drawing with scale:', { scale, x, y });

            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw image scaled (blur will be applied via CSS)
            ctx.drawImage(
                img,
                x,
                y,
                img.width * scale,
                img.height * scale
            );

            console.log('AmbientBackground: Drawing complete');
        };

        img.onerror = (e) => {
            console.error('AmbientBackground: Failed to load image', e);
        };

        img.src = imageUrl;
    }, [imageUrl]);

    return (
        <div ref={containerRef} className={className}>
            <canvas
                ref={canvasRef}
                aria-hidden="true"
                className="pointer-events-none h-full w-full"
                style={{
                    filter: 'blur(40px) saturate(200%) brightness(130%)',
                }}
            />
        </div>
    );
}
