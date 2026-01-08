import { useRef } from 'react';
import { Input } from './input';
import { cn } from '@/lib/utils';

interface OtpInputProps {
    value: string;
    onChange: (value: string) => void;
    length?: number;
    disabled?: boolean;
    autoFocus?: boolean;
}

export function OtpInput({ value, onChange, length = 8, disabled = false, autoFocus = false }: OtpInputProps) {
    const inputRefs = useRef<(HTMLInputElement | null)[]>([]);

    // Create array of digits from value
    const valueChars = value.split('');
    const digits = Array.from({ length }, (_, i) => valueChars[i] || '');

    const handleChange = (index: number, inputValue: string) => {
        const sanitized = inputValue.toUpperCase().replace(/[^A-Z0-9]/g, '');

        if (sanitized.length > 1) {
            // Handle paste
            const chars = sanitized.split('').slice(0, length);
            const newDigits = [...digits];
            chars.forEach((char, i) => {
                if (index + i < length) {
                    newDigits[index + i] = char;
                }
            });
            onChange(newDigits.join('').replace(/\s/g, ''));

            // Focus on the next empty input or the last one
            const nextIndex = Math.min(index + chars.length, length - 1);
            inputRefs.current[nextIndex]?.focus();
        } else {
            const newDigits = [...digits];
            newDigits[index] = sanitized;
            onChange(newDigits.join('').replace(/\s/g, ''));

            if (sanitized && index < length - 1) {
                inputRefs.current[index + 1]?.focus();
            }
        }
    };

    const handleKeyDown = (index: number, e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Backspace') {
            if (!digits[index] && index > 0) {
                inputRefs.current[index - 1]?.focus();
            }
            const newDigits = [...digits];
            newDigits[index] = '';
            onChange(newDigits.join('').replace(/\s/g, ''));
        } else if (e.key === 'ArrowLeft' && index > 0) {
            inputRefs.current[index - 1]?.focus();
        } else if (e.key === 'ArrowRight' && index < length - 1) {
            inputRefs.current[index + 1]?.focus();
        }
    };

    const handlePaste = (e: React.ClipboardEvent) => {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text');
        const sanitized = pastedData.toUpperCase().replace(/[^A-Z0-9]/g, '');
        const chars = sanitized.split('').slice(0, length);

        const newDigits = new Array(length).fill('');
        chars.forEach((char, i) => {
            newDigits[i] = char;
        });
        onChange(newDigits.join('').replace(/\s/g, ''));

        // Focus on the next empty input or the last one
        const nextIndex = Math.min(chars.length, length - 1);
        inputRefs.current[nextIndex]?.focus();
    };

    return (
        <div className="flex gap-2 justify-center">
            {digits.map((digit, index) => (
                <Input
                    key={index}
                    ref={(el) => (inputRefs.current[index] = el)}
                    type="text"
                    inputMode="text"
                    maxLength={1}
                    value={digit}
                    onChange={(e) => handleChange(index, e.target.value)}
                    onKeyDown={(e) => handleKeyDown(index, e)}
                    onPaste={index === 0 ? handlePaste : undefined}
                    disabled={disabled}
                    autoFocus={index === 0 && autoFocus}
                    autoComplete="off"
                    className={cn(
                        'h-14 w-12 text-center text-lg font-mono font-bold uppercase',
                        disabled && 'cursor-not-allowed opacity-50'
                    )}
                />
            ))}
        </div>
    );
}
