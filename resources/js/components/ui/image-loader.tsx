import { useState, useEffect } from "react";
import { Loader2 } from "lucide-react";

interface ImageLoaderProps {
    src: string,
    loaderColor?: string|null,
    classNames?: string,
    className?: string,
    wrapperClassName?: string
}

export default function ImageLoader({src, loaderColor = null, classNames = '', className = '', wrapperClassName}: ImageLoaderProps) {
    const [loading, setLoading] = useState(true);
    const [retries, setRetries] = useState(0);
    const [source, setSource] = useState(src);

    // Support both classNames and className for backwards compatibility
    const imgClassName = className || classNames;

    // Update source when src prop changes
    useEffect(() => {
        setSource(src);
        setLoading(true);
        setRetries(0);
    }, [src]);

    const maxRetries = 10;
    const retryDelay = 8000;

    const handleError = () => {
        if (retries < maxRetries) {
            setRetries(retries + 1);
            setLoading(true);

            setTimeout(() => {
                // Wymuszamy ponowne załadowanie obrazka, dodając losowy query param, aby uniknąć cache
                setSource(`${src}?retry=${retries + 1}`);
            }, retryDelay);
        } else {
            console.error("Nie udało się załadować obrazka po kilku próbach.");
        }
    };

    return (
        <div className={wrapperClassName || "w-full h-full flex justify-center items-center"}>
            {loading && (
                <Loader2 className={`animate-spin w-6 h-6 ${loaderColor ? loaderColor : 'text-gray-900 dark:text-gray-100'} absolute`} />
            )}
            <img
                draggable={false}
                src={source}
                onLoad={() => setLoading(false)}
                onError={handleError}
                className={`${loading ? "opacity-0" : "opacity-100"} transition-opacity duration-500 ${imgClassName} select-none`}
            />
        </div>
    );
}
