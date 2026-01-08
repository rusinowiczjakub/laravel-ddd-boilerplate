import { useRef, useState } from 'react';
import { Upload, X, FileText } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';

interface FileDropzoneProps {
    file: File | null;
    onFileChange: (file: File | null) => void;
    error?: string;
    progress?: { percentage: number };
    title?: string;
    description?: string;
    accept?: string;
    disabled?: boolean;
    currentFileName?: string;
    currentFileSize?: number;
}

export default function FileDropzone({
    file,
    onFileChange,
    error,
    progress,
    title = 'Upload File',
    description = 'Drag and drop your file here, or click to browse',
    accept,
    disabled = false,
    currentFileName,
    currentFileSize,
}: FileDropzoneProps) {
    const [isDragging, setIsDragging] = useState(false);
    const [isHovering, setIsHovering] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleDragOver = (e: React.DragEvent) => {
        if (disabled) return;
        e.preventDefault();
        setIsDragging(true);
    };

    const handleDragLeave = (e: React.DragEvent) => {
        if (disabled) return;
        e.preventDefault();
        setIsDragging(false);
    };

    const handleDrop = (e: React.DragEvent) => {
        if (disabled) return;
        e.preventDefault();
        setIsDragging(false);

        const droppedFile = e.dataTransfer.files[0];
        if (droppedFile) {
            onFileChange(droppedFile);
        }
    };

    const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (disabled) return;
        const selectedFile = e.target.files?.[0];
        if (selectedFile) {
            onFileChange(selectedFile);
        }
    };

    const handleRemove = () => {
        if (disabled) return;
        onFileChange(null);
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const handleClick = () => {
        if (disabled) return;
        fileInputRef.current?.click();
    };

    const formatFileSize = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    };

    const displayFile = file || (currentFileName && { name: currentFileName, size: currentFileSize || 0 });

    return (
        <div className="space-y-2">
            <input
                ref={fileInputRef}
                type="file"
                className="hidden"
                onChange={handleFileSelect}
                accept={accept}
                disabled={disabled}
            />

            {!displayFile ? (
                <div
                    onClick={handleClick}
                    onDragOver={handleDragOver}
                    onDragLeave={handleDragLeave}
                    onDrop={handleDrop}
                    onMouseEnter={() => !disabled && setIsHovering(true)}
                    onMouseLeave={() => !disabled && setIsHovering(false)}
                    className={`
                        relative rounded-lg border-2 border-dashed p-8 text-center transition-all duration-200
                        ${disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'}
                        ${isDragging && !disabled ? 'border-primary bg-neutral-100 dark:bg-neutral-800' : ''}
                        ${!isDragging && isHovering && !disabled ? 'border-primary bg-neutral-50 dark:bg-neutral-900' : ''}
                        ${!isDragging && !isHovering ? 'border-neutral-300' : ''}
                        ${error ? 'border-destructive' : ''}
                    `}
                >
                    <div className="flex flex-col items-center gap-2">
                        <div className="rounded-full bg-neutral-100 p-3">
                            <Upload className="h-6 w-6 text-neutral-600" />
                        </div>
                        <div>
                            <p className="text-sm font-medium">{title}</p>
                            <p className="text-xs text-muted-foreground mt-1">{description}</p>
                        </div>
                    </div>
                </div>
            ) : (
                <div className={`rounded-lg border p-4 ${disabled ? 'opacity-75 bg-muted/30' : ''}`}>
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="rounded bg-primary/10 p-2">
                                <FileText className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <p className="text-sm font-medium">{displayFile.name}</p>
                                <p className="text-xs text-muted-foreground">{formatFileSize(displayFile.size)}</p>
                            </div>
                        </div>
                        {!disabled && file && (
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={handleRemove}
                            >
                                <X className="h-4 w-4" />
                            </Button>
                        )}
                    </div>

                    {progress && progress.percentage > 0 && progress.percentage < 100 && (
                        <div className="mt-3">
                            <Progress value={progress.percentage} className="h-2" />
                            <p className="text-xs text-muted-foreground mt-1 text-right">
                                {progress.percentage}%
                            </p>
                        </div>
                    )}
                </div>
            )}

            {error && (
                <p className="text-sm text-destructive">{error}</p>
            )}
        </div>
    );
}
