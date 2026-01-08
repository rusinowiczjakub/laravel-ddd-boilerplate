import { useState, useRef } from 'react';
import { router } from '@inertiajs/react';
import { Bug, Lightbulb, MessageCircle, X, Upload } from 'lucide-react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface FeedbackModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

type FeedbackType = 'bug' | 'feature' | 'general';

const feedbackTypes = [
    { value: 'bug', label: 'Bug Report', icon: Bug },
    { value: 'feature', label: 'Feature Request', icon: Lightbulb },
    { value: 'general', label: 'General Feedback', icon: MessageCircle },
] as const;

export function FeedbackModal({ open, onOpenChange }: FeedbackModalProps) {
    const [type, setType] = useState<FeedbackType>('general');
    const [message, setMessage] = useState('');
    const [screenshot, setScreenshot] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        if (message.length < 10) {
            setError('Message must be at least 10 characters');
            return;
        }

        setIsSubmitting(true);

        const formData = new FormData();
        formData.append('type', type);
        formData.append('message', message);
        formData.append('url', window.location.href);
        formData.append('userAgent', navigator.userAgent);

        if (screenshot) {
            formData.append('screenshot', screenshot);
        }

        router.post('/feedback', formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                // Reset form and close
                setType('general');
                setMessage('');
                setScreenshot(null);
                onOpenChange(false);
            },
            onError: (errors) => {
                setError(errors.message || 'Failed to send feedback');
            },
            onFinish: () => {
                setIsSubmitting(false);
            },
        });
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            if (file.size > 10 * 1024 * 1024) {
                setError('File size must be less than 10MB');
                return;
            }
            setScreenshot(file);
            setError(null);
        }
    };

    const removeScreenshot = () => {
        setScreenshot(null);
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const selectedType = feedbackTypes.find(t => t.value === type);

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Send Feedback</DialogTitle>
                    <DialogDescription>
                        Help us improve - report bugs, suggest features, or share your thoughts.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    {/* Type Select */}
                    <div className="space-y-2">
                        <Label htmlFor="feedback-type">Feedback Type</Label>
                        <Select value={type} onValueChange={(value) => setType(value as FeedbackType)}>
                            <SelectTrigger id="feedback-type">
                                <SelectValue>
                                    {selectedType && (
                                        <span className="flex items-center gap-2">
                                            <selectedType.icon className="h-4 w-4" />
                                            {selectedType.label}
                                        </span>
                                    )}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                {feedbackTypes.map((feedbackType) => (
                                    <SelectItem key={feedbackType.value} value={feedbackType.value}>
                                        <span className="flex items-center gap-2">
                                            <feedbackType.icon className="h-4 w-4" />
                                            {feedbackType.label}
                                        </span>
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    {/* Message */}
                    <div className="space-y-2">
                        <Label htmlFor="feedback-message">
                            Message <span className="text-muted-foreground">(min. 10 characters)</span>
                        </Label>
                        <Textarea
                            id="feedback-message"
                            placeholder="Describe your feedback in detail..."
                            value={message}
                            onChange={(e) => setMessage(e.target.value)}
                            rows={5}
                            maxLength={2000}
                            className="resize-none"
                        />
                        <p className="text-xs text-muted-foreground text-right">
                            {message.length}/2000 characters
                        </p>
                    </div>

                    {/* Screenshot Upload */}
                    <div className="space-y-2">
                        <Label>Screenshot <span className="text-muted-foreground">(optional)</span></Label>
                        {screenshot ? (
                            <div className="flex items-center gap-2 rounded-md border p-2">
                                <span className="flex-1 truncate text-sm">{screenshot.name}</span>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    onClick={removeScreenshot}
                                >
                                    <X className="h-4 w-4" />
                                </Button>
                            </div>
                        ) : (
                            <div
                                className="flex cursor-pointer items-center justify-center gap-2 rounded-md border border-dashed p-4 text-sm text-muted-foreground hover:border-primary hover:text-foreground transition-colors"
                                onClick={() => fileInputRef.current?.click()}
                            >
                                <Upload className="h-4 w-4" />
                                Click to upload (max 10MB)
                            </div>
                        )}
                        <input
                            ref={fileInputRef}
                            type="file"
                            accept="image/*"
                            onChange={handleFileChange}
                            className="hidden"
                        />
                    </div>

                    {/* Error */}
                    {error && (
                        <p className="text-sm text-destructive">{error}</p>
                    )}

                    {/* Actions */}
                    <div className="flex justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => onOpenChange(false)}
                            disabled={isSubmitting}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" disabled={isSubmitting || message.length < 10}>
                            {isSubmitting ? 'Sending...' : 'Send Feedback'}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}
