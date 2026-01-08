import { useState, useCallback } from 'react';
import {
    ChangeEmailModal,
    ProfileGeneralCard,
    ProfilePreferencesCard,
    ProfileSecurityCard,
    UpdatePasswordModal,
} from '@/components/settings/profile';
import { TwoFactorSetupModal } from '@/components/settings/two-factor';
import { useChangeEmail } from '@/hooks/use-change-email';
import { useConfirmation } from '@/hooks/use-confirmation';
import { useProfileSettings } from '@/hooks/use-profile-settings';
import { useTwoFactorAuth } from '@/hooks/use-two-factor-auth';
import { useUpdatePassword } from '@/hooks/use-update-password';
import SettingsLayout from '@/layouts/settings-layout';
import { type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';

interface ProfileProps {
    mustVerifyEmail: boolean;
    status?: string;
}

export default function Profile({ mustVerifyEmail, status }: ProfileProps) {
    const { auth } = usePage<SharedData>().props;
    const [showTwoFactorModal, setShowTwoFactorModal] = useState(false);
    const { confirm } = useConfirmation();

    const {
        name,
        setName,
        email,
        avatar,
        twoFactorEnabled,
        theme,
        setTheme,
        isSaving,
        handleNameBlur,
        uploadAvatar,
        removeAvatar,
        isUploadingAvatar,
    } = useProfileSettings();

    const {
        isModalOpen,
        openModal,
        closeModal,
        password,
        setPassword,
        newEmail,
        setNewEmail,
        submit: submitEmailChange,
        isSubmitting: isEmailChanging,
        errors: emailErrors,
    } = useChangeEmail();

    const {
        setupData,
        recoveryCodes,
        isLoading: isTwoFactorLoading,
        errors: twoFactorErrors,
        clearSetupData,
        clearErrors: clearTwoFactorErrors,
        fetchRecoveryCodes,
        enableTwoFactor,
        confirmTwoFactor,
        disableTwoFactor,
        regenerateRecoveryCodes,
    } = useTwoFactorAuth();

    const {
        isModalOpen: isPasswordModalOpen,
        openModal: openPasswordModal,
        closeModal: closePasswordModal,
        currentPassword,
        setCurrentPassword,
        newPassword,
        setNewPassword,
        confirmPassword,
        setConfirmPassword,
        submit: submitPasswordChange,
        isSubmitting: isPasswordChanging,
        errors: passwordErrors,
    } = useUpdatePassword();

    const handleTwoFactorToggle = useCallback(async (enabled: boolean) => {
        if (enabled) {
            enableTwoFactor();
            setShowTwoFactorModal(true);
        } else {
            const confirmed = await confirm({
                title: 'Disable Two-Factor Authentication',
                description: 'Are you sure you want to disable two-factor authentication? This will make your account less secure.',
                confirmText: 'Disable',
                variant: 'destructive',
            });
            if (confirmed) {
                disableTwoFactor();
            }
        }
    }, [enableTwoFactor, disableTwoFactor, confirm]);

    const handleTwoFactorConfirm = useCallback((code: string) => {
        confirmTwoFactor(code);
    }, [confirmTwoFactor]);

    const handleCloseTwoFactorModal = useCallback(() => {
        clearSetupData();
        clearTwoFactorErrors();
    }, [clearSetupData, clearTwoFactorErrors]);

    return (
        <SettingsLayout>
            <Head title="Profile" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-3xl font-bold">Profile</h1>
                    <p className="text-muted-foreground">Your personal information and preferences.</p>
                </div>

                <ProfileGeneralCard
                    name={name}
                    onNameChange={setName}
                    onNameBlur={handleNameBlur}
                    email={email}
                    isSaving={isSaving}
                    mustVerifyEmail={mustVerifyEmail}
                    isEmailVerified={auth.user?.email_verified_at !== null}
                    verificationStatus={status}
                    onChangeEmailClick={openModal}
                    avatar={avatar}
                    onAvatarUpload={uploadAvatar}
                    onAvatarRemove={removeAvatar}
                    isUploadingAvatar={isUploadingAvatar}
                />

                <ProfileSecurityCard
                    twoFactorEnabled={twoFactorEnabled}
                    onTwoFactorToggle={handleTwoFactorToggle}
                    onUpdatePassword={openPasswordModal}
                    recoveryCodes={recoveryCodes}
                    isLoadingRecoveryCodes={isTwoFactorLoading}
                    onFetchRecoveryCodes={fetchRecoveryCodes}
                    onRegenerateRecoveryCodes={regenerateRecoveryCodes}
                />

                <ProfilePreferencesCard
                    theme={theme}
                    onThemeChange={setTheme}
                />
            </div>

            <ChangeEmailModal
                isOpen={isModalOpen}
                onClose={closeModal}
                password={password}
                onPasswordChange={setPassword}
                newEmail={newEmail}
                onNewEmailChange={setNewEmail}
                onSubmit={submitEmailChange}
                isSubmitting={isEmailChanging}
                errors={emailErrors}
            />

            <TwoFactorSetupModal
                open={showTwoFactorModal}
                onOpenChange={setShowTwoFactorModal}
                qrCodeSvg={setupData.qrCodeSvg}
                secretKey={setupData.secretKey}
                isLoading={isTwoFactorLoading}
                errors={twoFactorErrors}
                onConfirm={handleTwoFactorConfirm}
                onClose={handleCloseTwoFactorModal}
            />

            <UpdatePasswordModal
                isOpen={isPasswordModalOpen}
                onClose={closePasswordModal}
                currentPassword={currentPassword}
                onCurrentPasswordChange={setCurrentPassword}
                newPassword={newPassword}
                onNewPasswordChange={setNewPassword}
                confirmPassword={confirmPassword}
                onConfirmPasswordChange={setConfirmPassword}
                onSubmit={submitPasswordChange}
                isSubmitting={isPasswordChanging}
                errors={passwordErrors}
            />
        </SettingsLayout>
    );
}
