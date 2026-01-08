import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Monitor, Moon, Sun } from 'lucide-react';

interface ProfilePreferencesCardProps {
    theme: string;
    onThemeChange: (theme: string) => void;
}

const themeOptions = [
    { value: 'light', label: 'Light', icon: Sun },
    { value: 'dark', label: 'Dark', icon: Moon },
    { value: 'system', label: 'System', icon: Monitor },
];

export const ProfilePreferencesCard = ({ theme, onThemeChange }: ProfilePreferencesCardProps) => {
    const currentTheme = themeOptions.find((t) => t.value === theme) ?? themeOptions[1];
    const ThemeIcon = currentTheme.icon;

    return (
        <Card>
            <CardHeader>
                <CardTitle>Preferences</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
                <div className="flex items-start justify-between gap-4">
                    <div className="flex-1">
                        <Label htmlFor="theme">Theme</Label>
                        <p className="text-muted-foreground text-sm">Choose your preferred theme</p>
                    </div>
                    <Select value={theme} onValueChange={onThemeChange}>
                        <SelectTrigger className="w-48">
                            <SelectValue>
                                <div className="flex items-center gap-2">
                                    <ThemeIcon className="size-4" />
                                    <span>{currentTheme.label}</span>
                                </div>
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            {themeOptions.map((option) => {
                                const Icon = option.icon;
                                return (
                                    <SelectItem key={option.value} value={option.value}>
                                        <div className="flex items-center gap-2">
                                            <Icon className="size-4" />
                                            <span>{option.label}</span>
                                        </div>
                                    </SelectItem>
                                );
                            })}
                        </SelectContent>
                    </Select>
                </div>
            </CardContent>
        </Card>
    );
};
